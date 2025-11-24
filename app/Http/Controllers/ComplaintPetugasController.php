<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComplaintPetugasController
{
    /**
     * List assigned complaints (privacy-filtered)
     */
    public function indexAssigned()
    {
        $user = Auth::user();

        if (!$user->isPetugas()) {
            abort(403, 'Hanya petugas yang dapat mengakses halaman ini');
        }

        $complaints = Complaint::where('assigned_to', $user->id)
            ->with(['updates' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('petugas.complaints.index', [
            'complaints' => $complaints,
        ]);
    }

    /**
     * Update status by petugas (limited transitions)
     */
    public function updateStatusByPetugas(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->isPetugas() || $complaint->assigned_to !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:todo,in_progress,done'],
            'note' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $oldStatus = $complaint->status;
        $newStatus = $request->input('status');

        // Validate transition (petugas can only: todo -> in_progress -> done)
        $validTransitions = [
            'todo' => ['in_progress'],
            'in_progress' => ['done'],
        ];

        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            return back()->withErrors(['error' => 'Transisi status tidak valid']);
        }

        try {
            DB::beginTransaction();

            $complaint->update(['status' => $newStatus]);

            $imagePath = $request->hasFile('image') 
                ? $request->file('image')->store('complaints/progress', 'public')
                : null;
            
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
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
            Log::info('Complaint status updated by petugas', [
                'complaint_id' => $complaint->id,
                'status_from' => $oldStatus,
                'status_to' => $newStatus,
                'user_id' => $user->id,
            ]);

            DB::commit();

            return back()->with('success', 'Status berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Status update failed', [
                'error' => $e->getMessage(),
                'complaint_id' => $complaint->id,
            ]);

            return back()->withErrors(['error' => 'Gagal mengupdate status']);
        }
    }

    /**
     * Upload progress photo
     */
    public function uploadProgressPhoto(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->isPetugas() || $complaint->assigned_to !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $imagePath = $request->file('image')->store('complaints/progress', 'public');

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'progress_update',
                'model_type' => \App\Models\Complaint::class,
                'model_id' => $complaint->id,
                'complaint_id' => $complaint->id,
                'status_from' => $complaint->status,
                'status_to' => $complaint->status,
                'note' => $request->input('note', 'Foto progress pekerjaan'),
                'image' => $imagePath,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', 'Foto progress berhasil diupload');

        } catch (\Exception $e) {
            Log::error('Photo upload failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Gagal mengupload foto']);
        }
    }
}
