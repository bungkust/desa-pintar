<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintCommentResource\Pages;
use App\Models\ComplaintComment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplaintCommentResource extends Resource
{
    protected static ?string $model = ComplaintComment::class;

    protected static ?string $navigationIcon = null;
    
    protected static ?string $navigationGroup = 'Pengaduan';
    
    protected static ?string $navigationLabel = 'Komentar';
    
    protected static ?int $navigationSort = 5;

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
                
                Tables\Columns\TextColumn::make('sender_type')
                    ->label('Pengirim')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'warga' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'warga' => 'Warga',
                    }),
                
                Tables\Columns\TextColumn::make('sender_name')
                    ->label('Nama')
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(100)
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sender_type')
                    ->label('Tipe Pengirim')
                    ->options([
                        'admin' => 'Admin',
                        'warga' => 'Warga',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaintComments::route('/'),
            'view' => Pages\ViewComplaintComment::route('/{record}'),
        ];
    }
}
