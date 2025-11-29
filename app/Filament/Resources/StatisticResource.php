<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatisticResource\Pages;
use App\Models\Statistic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class StatisticResource extends Resource
{
    protected static ?string $model = Statistic::class;

    protected static ?string $navigationGroup = 'Statistics';
    
    protected static ?string $navigationLabel = 'Statistics';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'demografi' => 'Demografi',
                        'geografis' => 'Geografis',
                        'ekonomi' => 'Ekonomi',
                        'infrastruktur' => 'Infrastruktur',
                        'sosial' => 'Sosial',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->default('demografi')
                    ->native(false),
                
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('icon')
                    ->label('Icon Class')
                    ->maxLength(255)
                    ->helperText('e.g., heroicon-o-users'),
                
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'demografi' => 'info',
                        'geografis' => 'success',
                        'ekonomi' => 'warning',
                        'infrastruktur' => 'danger',
                        'sosial' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'demografi' => 'Demografi',
                        'geografis' => 'Geografis',
                        'ekonomi' => 'Ekonomi',
                        'infrastruktur' => 'Infrastruktur',
                        'sosial' => 'Sosial',
                        default => ucfirst($state),
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('value')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('icon')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('order')
            ->defaultPaginationPageOption(25)
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatistics::route('/'),
            'create' => Pages\CreateStatistic::route('/create'),
            'edit' => Pages\EditStatistic::route('/{record}/edit'),
        ];
    }
}

