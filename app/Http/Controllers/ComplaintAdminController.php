<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintComment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ComplaintAdminController
{
    /**
     * Show Kanban board
     */
    public function index()
    {
        $user = Auth::user();

        // Get complaints grouped by status
        $complaints = Complaint::with(['assignedUser', 'updates' => function ($query) {
            $query->latest()->limit(1);
        }])
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('status');

        // Get all statuses
        $statuses = ['backlog', 'verification', 'todo', 'in_progress', 'done', 'rejected'];

        // Initialize empty arrays for missing statuses
        foreach ($statuses as $status) {
            if (!isset($complaints[$status])) {
                $complaints[$status] = collect();
            }
        }

        return view('admin.complaints.kanban', [
            'complaints' => $complaints,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Show complaint detail
     */
    public function show(Complaint $complaint)
    {
        $this->authorize('view', $complaint);

        $complaint->load([
            'assignedUser',
            'updates.updatedBy',
            'comments.user'
        ]);

        // Get all petugas for assignment dropdown
        $petugas = User::where('role', 'petugas')->get();

        return view('admin.complaints.detail', [
            'complaint' => $complaint,
            'petugas' => $petugas,
        ]);
    }

    /**
     * Update complaint status (AJAX)
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $this->authorize('changeStatus', [$complaint, $request->input('status')]);

        $request->validate([
            'status' => ['required', 'string', 'in:backlog,verification,todo,in_progress,done,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $oldStatus = $complaint->status;
        $newStatus = $request->input('status');

        try {
            DB::beginTransaction();

            // Update complaint status
            $complaint->update(['status' => $newStatus]);

            // Create activity log for status change
            $imagePath = $request->hasFile('image') 
                ? $request->file('image')->store('complaints/progress', 'public')
                : null;
            
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'model_type' => \App\Models\Complaint::class,
                'model_id' => $complaint->id,
                'complaint_id' => $complaint->id,
                'status_from' => $oldStatus,
                'status_to' => $newStatus,
                'note' => $request->input('note'),
                'image' => $imagePath,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Log audit
            Log::info('Complaint status changed', [
                'complaint_id' => $complaint->id,
                'tracking_code' => $complaint->tracking_code,
                'status_from' => $oldStatus,
                'status_to' => $newStatus,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diupdate',
                    'complaint' => $complaint->fresh(['assignedUser', 'updates']),
                ]);
            }

            return back()->with('success', 'Status berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Status update failed', [
                'error' => $e->getMessage(),
                'complaint_id' => $complaint->id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate status',
                ], 500);
            }

            return back()->withErrors(['error' => 'Gagal mengupdate status']);
        }
    }

    /**
     * Assign petugas to complaint
     */
    public function assignPetugas(Request $request, Complaint $complaint)
    {
        $this->authorize('assignPetugas', $complaint);

        $request->validate([
            'petugas_id' => ['required', 'exists:users,id'],
        ]);

        $petugas = User::findOrFail($request->input('petugas_id'));

        if ($petugas->role !== 'petugas') {
            return back()->withErrors(['error' => 'User yang dipilih bukan petugas']);
        }

        try {
            DB::beginTransaction();

            // Assignment tracking is handled by ComplaintObserver
            $complaint->update(['assigned_to' => $petugas->id]);

            // Log audit
            Log::info('Complaint assigned', [
                'complaint_id' => $complaint->id,
                'tracking_code' => $complaint->tracking_code,
                'petugas_id' => $petugas->id,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', "Pengaduan berhasil ditugaskan kepada {$petugas->name}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assignment failed', [
                'error' => $e->getMessage(),
                'complaint_id' => $complaint->id,
            ]);

            return back()->withErrors(['error' => 'Gagal menugaskan petugas']);
        }
    }

    /**
     * Store comment
     */
    public function commentStore(Request $request, Complaint $complaint)
    {
        $this->authorize('addComment', $complaint);

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            ComplaintComment::create([
                'complaint_id' => $complaint->id,
                'sender_type' => 'admin',
                'sender_name' => Auth::user()->name,
                'message' => $request->input('message'),
                'user_id' => Auth::id(),
            ]);

            // Log audit
            Log::info('Comment added', [
                'complaint_id' => $complaint->id,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Komentar berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Comment creation failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Gagal menambahkan komentar']);
        }
    }

    /**
     * Export monthly PDF
     */
    public function exportPDF(Request $request)
    {
        $this->authorize('exportPDF', Complaint::class);

        $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = $request->input('month', now()->format('Y-m'));
        $startDate = \Carbon\Carbon::parse($month)->startOfMonth();
        $endDate = \Carbon\Carbon::parse($month)->endOfMonth();

        $complaints = Complaint::whereBetween('created_at', [$startDate, $endDate])
            ->with(['assignedUser', 'updates'])
            ->get();

        // Statistics
        $stats = [
            'total' => $complaints->count(),
            'by_status' => $complaints->groupBy('status')->map->count(),
            'by_category' => $complaints->groupBy('category')->map->count(),
            'completed' => $complaints->where('status', 'done')->count(),
            'pending' => $complaints->whereNotIn('status', ['done', 'rejected'])->count(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.complaints.export.pdf', [
            'complaints' => $complaints,
            'stats' => $stats,
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        $filename = 'laporan-pengaduan-' . $month . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Dashboard statistics
     */
    public function dashboard()
    {
        $this->authorize('viewDashboard', Complaint::class);

        $user = Auth::user();

        // Base query based on role
        $query = Complaint::query();
        
        if ($user->isPetugas()) {
            $query->where('assigned_to', $user->id);
        }

        // Statistics
        $stats = [
            'total_this_month' => (clone $query)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'completed' => (clone $query)->where('status', 'done')->count(),
            'pending' => (clone $query)->whereNotIn('status', ['done', 'rejected'])->count(),
            'overdue' => (clone $query)->overdue()->count(),
            'nearing_deadline' => (clone $query)->nearingDeadline()->count(),
        ];

        // By category
        $byCategory = (clone $query)->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        // By RT
        $byRT = (clone $query)->select('rt', DB::raw('count(*) as count'))
            ->whereNotNull('rt')
            ->groupBy('rt')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'rt');

        // Top 5 most reported issues
        $topIssues = (clone $query)->select('title', DB::raw('count(*) as count'))
            ->groupBy('title')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.complaints.dashboard', [
            'stats' => $stats,
            'byCategory' => $byCategory,
            'byRT' => $byRT,
            'topIssues' => $topIssues,
        ]);
    }
}
