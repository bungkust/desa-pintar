<?php

namespace App\Filament\Resources\ComplaintResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Komentar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->label('Komentar')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sender_name')
            ->columns([
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
                    ->wrap()
                    ->limit(100),
                
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['sender_type'] = 'admin';
                        $data['sender_name'] = Auth::user()->name;
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'asc');
    }
}
