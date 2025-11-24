<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Models\Complaint;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintDashboardPage extends Page
{
    protected static string $resource = ComplaintResource::class;

    protected static string $view = 'filament.resources.complaint-resource.pages.complaint-dashboard-page';

    protected static ?string $navigationLabel = 'Dashboard Pengaduan';

    protected static ?string $navigationIcon = null;

    protected static ?int $navigationSort = 4;

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

    public function getStatsProperty(): array
    {
        $user = Auth::user();
        
        $query = Complaint::query();
        
        if ($user->isPetugas()) {
            $query->where('assigned_to', $user->id);
        }

        return [
            'total_this_month' => (clone $query)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'completed' => (clone $query)->where('status', 'done')->count(),
            'pending' => (clone $query)->whereNotIn('status', ['done', 'rejected'])->count(),
            'overdue' => (clone $query)->overdue()->count(),
            'nearing_deadline' => (clone $query)->nearingDeadline()->count(),
        ];
    }

    public function getByCategoryProperty(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        
        $query = Complaint::query();
        
        if ($user->isPetugas()) {
            $query->where('assigned_to', $user->id);
        }

        return (clone $query)->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');
    }

    public function getByRTProperty(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        
        $query = Complaint::query();
        
        if ($user->isPetugas()) {
            $query->where('assigned_to', $user->id);
        }

        return (clone $query)->select('rt', DB::raw('count(*) as count'))
            ->whereNotNull('rt')
            ->groupBy('rt')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'rt');
    }

    public function getTopIssuesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        
        $query = Complaint::query();
        
        if ($user->isPetugas()) {
            $query->where('assigned_to', $user->id);
        }

        return (clone $query)->select('title', DB::raw('count(*) as count'))
            ->groupBy('title')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }
}
