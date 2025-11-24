<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = null;
    
    protected static ?string $navigationGroup = 'Pengaduan';
    
    protected static ?string $navigationLabel = 'Activity Log';
    
    protected static ?int $navigationSort = 4;

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
                    ->sortable()
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'status_changed' => 'primary',
                        'assigned' => 'info',
                        'progress_update' => 'warning',
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'status_changed' => 'Status Changed',
                        'assigned' => 'Assigned',
                        'progress_update' => 'Progress Update',
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    }),
                
                Tables\Columns\TextColumn::make('status_from')
                    ->label('Dari')
                    ->badge()
                    ->color('gray')
                    ->default('—')
                    ->visible(fn ($record) => $record && $record->isStatusChange()),
                
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
                        default => 'gray',
                    })
                    ->default('—')
                    ->visible(fn ($record) => $record && $record->isStatusChange()),
                
                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(50)
                    ->wrap()
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '—')
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Aksi')
                    ->options([
                        'status_changed' => 'Status Changed',
                        'assigned' => 'Assigned',
                        'progress_update' => 'Progress Update',
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                
                Tables\Filters\SelectFilter::make('complaint_id')
                    ->label('Pengaduan')
                    ->relationship('complaint', 'tracking_code')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
