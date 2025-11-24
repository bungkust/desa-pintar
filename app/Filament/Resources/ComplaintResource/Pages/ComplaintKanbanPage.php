<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ComplaintKanbanPage extends Page
{
    protected static string $resource = ComplaintResource::class;

    protected static string $view = 'filament.resources.complaint-resource.pages.complaint-kanban-page';

    protected static ?string $navigationLabel = 'Kanban Board';

    protected static ?string $navigationIcon = null;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Pengaduan';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Anda harus login untuk mengakses halaman ini.');
        }
        
        // In development/local, allow all authenticated users
        if (config('app.env') === 'local') {
            return;
        }
        
        // Allow if user can view any complaints via policy
        try {
            if ($user->can('viewAny', Complaint::class)) {
                return;
            }
        } catch (\Exception $e) {
            // Policy might not be registered, fall through to role check
        }
        
        // Fallback: allow if user has any complaint-related role
        if ($user->canManageComplaints() || $user->isPetugas() || $user->isViewer()) {
            return;
        }
        
        // Additional fallback: check role directly
        if (in_array($user->role ?? null, ['super_admin', 'admin_desa', 'lurah', 'petugas', 'viewer'])) {
            return;
        }
        
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini. Role: ' . ($user->role ?? 'null'));
    }

    protected function getComplaints(): \Illuminate\Database\Eloquent\Collection
    {
        $query = Complaint::with(['assignedUser', 'updates' => function ($query) {
            $query->latest()->limit(1);
        }]);

        // Petugas can only see assigned complaints
        if (Auth::user()->isPetugas()) {
            $query->where('assigned_to', Auth::id());
        }

        // Apply filters from request
        $filters = request()->only(['search', 'category', 'rt', 'rw', 'priority', 'assigned', 'overdue']);
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }
        
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        
        if (!empty($filters['rt'])) {
            $query->where('rt', $filters['rt']);
        }
        
        if (!empty($filters['rw'])) {
            $query->where('rw', $filters['rw']);
        }
        
        if (!empty($filters['assigned'])) {
            if ($filters['assigned'] === 'assigned') {
                $query->whereNotNull('assigned_to');
            } elseif ($filters['assigned'] === 'unassigned') {
                $query->whereNull('assigned_to');
            }
        }
        
        if (!empty($filters['overdue'])) {
            $query->overdue();
        }

        // Get all complaints and sort by priority (overdue first, then due soon, then others)
        $complaints = $query->get();
        
        // Apply priority filter if set
        if (!empty($filters['priority'])) {
            $complaints = $complaints->filter(function ($complaint) use ($filters) {
                $priority = 'medium';
                if ($complaint->isOverdue()) {
                    $priority = 'high';
                } elseif ($complaint->isNearingDeadline()) {
                    $priority = 'high';
                } elseif (in_array($complaint->status, ['verification', 'todo'])) {
                    $priority = 'medium';
                } else {
                    $priority = 'low';
                }
                return $priority === $filters['priority'];
            });
        }
        
        return $complaints->sortBy(function ($complaint) {
            // Priority: 0 = overdue, 1 = due soon, 2 = normal
            if ($complaint->isOverdue()) {
                return 0;
            }
            if ($complaint->isNearingDeadline()) {
                return 1;
            }
            return 2;
        })->values();
    }

    public function getComplaintsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getComplaints();
    }
    
    public function getStatusesProperty(): array
    {
        return ['backlog', 'verification', 'todo', 'in_progress', 'done', 'rejected'];
    }
    
    public function updateStatus($complaintId, $newStatus)
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        if (!Auth::user()->can('changeStatus', [$complaint, $newStatus])) {
            \Filament\Notifications\Notification::make()
                ->title('Tidak diizinkan')
                ->body('Anda tidak memiliki izin untuk mengubah status pengaduan ini.')
                ->danger()
                ->send();
            return;
        }
        
        $oldStatus = $complaint->status;
        $complaint->update(['status' => $newStatus]);
        
        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'note' => 'Status diupdate via Kanban board',
            'updated_by' => Auth::id(),
        ]);
        
        \Illuminate\Support\Facades\Log::info('Complaint status changed via Kanban', [
            'complaint_id' => $complaint->id,
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'user_id' => Auth::id(),
        ]);
        
        \Filament\Notifications\Notification::make()
            ->title('Status berhasil diupdate')
            ->success()
            ->send();
        
        $this->dispatch('status-updated');
    }
}
