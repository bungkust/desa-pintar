<?php

namespace App\Filament\Resources\ComplaintResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Riwayat Aktivitas';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
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
                    ->wrap()
                    ->limit(100)
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
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
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10);
    }
}
