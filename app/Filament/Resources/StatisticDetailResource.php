<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatisticDetailResource\Pages;
use App\Filament\Resources\StatisticDetailResource\RelationManagers;
use App\Models\Statistic;
use App\Models\StatisticDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatisticDetailResource extends Resource
{
    protected static ?string $model = StatisticDetail::class;

    protected static ?string $navigationGroup = 'Statistics';
    
    protected static ?string $navigationLabel = 'Statistic Details';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('statistic_id')
                    ->label('Statistik')
                    ->relationship('statistic', 'label')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),
                
                Forms\Components\Select::make('year')
                    ->label('Tahun')
                    ->options(array_combine(range(2020, 2030), range(2020, 2030)))
                    ->required()
                    ->native(false)
                    ->default(date('Y')),
                
                Forms\Components\TextInput::make('value')
                    ->label('Nilai')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\KeyValue::make('additional_data')
                    ->label('Data Tambahan')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->helperText('Data tambahan dalam format key-value (opsional)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('statistic.label')
                    ->label('Statistik')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('statistic.category')
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
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statistic_id')
                    ->label('Statistik')
                    ->relationship('statistic', 'label')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(array_combine(range(2020, 2030), range(2020, 2030))),
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
            ->defaultSort('year', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginated([10, 25, 50, 100]);
    }

    /**
     * Optimize query with eager loading to prevent N+1 queries
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('statistic');
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
            'index' => Pages\ListStatisticDetails::route('/'),
            'create' => Pages\CreateStatisticDetail::route('/create'),
            'edit' => Pages\EditStatisticDetail::route('/{record}/edit'),
        ];
    }
}
