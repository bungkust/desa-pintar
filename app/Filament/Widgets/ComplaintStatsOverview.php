<?php

namespace App\Filament\Widgets;

use App\Models\Complaint;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ComplaintStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Base query - apply petugas filter if needed
        $baseQuery = Complaint::query();
        if ($user && method_exists($user, 'isPetugas') && $user->isPetugas()) {
            $baseQuery->where('assigned_to', $user->id);
        }

        // Total Pengaduan
        $totalPengaduan = (clone $baseQuery)->count();

        // Selesai (Done)
        $selesai = (clone $baseQuery)
            ->where('status', 'done')
            ->count();

        // Sedang Diproses (In Progress)
        $sedangDiproses = (clone $baseQuery)
            ->where('status', 'in_progress')
            ->count();

        return [
            Stat::make('Pengaduan', $totalPengaduan)
                ->description('Total pengaduan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([$totalPengaduan]),
            
            Stat::make('Selesai', $selesai)
                ->description('Pengaduan yang telah selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([$selesai]),
            
            Stat::make('Sedang Diproses', $sedangDiproses)
                ->description('Pengaduan yang sedang ditangani')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning')
                ->chart([$sedangDiproses]),
        ];
    }
}

