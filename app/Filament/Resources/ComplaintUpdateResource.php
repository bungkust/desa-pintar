<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintUpdateResource\Pages;
use App\Models\ComplaintUpdate;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplaintUpdateResource extends Resource
{
    protected static ?string $model = ComplaintUpdate::class;

    protected static ?string $navigationIcon = null;
    
    protected static ?string $navigationGroup = 'Pengaduan';
    
    protected static ?string $navigationLabel = 'Riwayat Update';
    
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false; // View-only resource
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('complaint.tracking_code')
                    ->label('Kode Tracking')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status_from')
                    ->label('Dari')
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('status_to')
                    ->label('Ke')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'backlog' => 'gray',
                        'verification' => 'warning',
                        'todo' => 'info',
                        'in_progress' => 'primary',
                        'done' => 'success',
                        'rejected' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(50)
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->label('Oleh')
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_to')
                    ->label('Status')
                    ->options([
                        'backlog' => 'Backlog',
                        'verification' => 'Verification',
                        'todo' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaintUpdates::route('/'),
            'view' => Pages\ViewComplaintUpdate::route('/{record}'),
        ];
    }
}
