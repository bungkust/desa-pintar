<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApbdesResource\Pages;
use App\Models\Apbdes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class ApbdesResource extends Resource
{
    protected static ?string $model = Apbdes::class;

    protected static ?string $navigationGroup = 'Transparency';
    
    protected static ?string $navigationLabel = 'APBDes';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->default(now()->year),
                
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'pendapatan' => 'Pendapatan',
                        'belanja' => 'Belanja',
                        'pembiayaan' => 'Pembiayaan',
                    ]),
                
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('realisasi')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->thousandsSeparator('.')
                    ->helperText('Realisasi in Rupiah')
                    ->default(0),
                
                Forms\Components\TextInput::make('anggaran')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->thousandsSeparator('.')
                    ->helperText('Anggaran in Rupiah')
                    ->default(0),
                
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->thousandsSeparator('.')
                    ->helperText('Legacy field - optional, kept for backward compatibility'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendapatan' => 'success',
                        'belanja' => 'danger',
                        'pembiayaan' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('realisasi')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('anggaran')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                
                Tables\Columns\ViewColumn::make('capaian')
                    ->label('Capaian')
                    ->view('filament.tables.columns.apbdes-capaian')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw("(realisasi / NULLIF(anggaran, 0)) * 100 {$direction}");
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(function () {
                        return \Illuminate\Support\Facades\Cache::remember('apbdes_filter_years', 3600, function () {
                        return Apbdes::query()
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year', 'year')
                            ->mapWithKeys(fn ($year) => [$year => $year])
                            ->toArray();
                        });
                    }),
                
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'pendapatan' => 'Pendapatan',
                        'belanja' => 'Belanja',
                        'pembiayaan' => 'Pembiayaan',
                    ]),
                
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(function () {
                        return \Illuminate\Support\Facades\Cache::remember('apbdes_filter_categories', 3600, function () {
                        return Apbdes::query()
                            ->distinct()
                            ->orderBy('category')
                            ->pluck('category', 'category')
                            ->toArray();
                        });
                    })
                    ->searchable(),
            ])
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->deferFilters()
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApbdes::route('/'),
            'create' => Pages\CreateApbdes::route('/create'),
            'edit' => Pages\EditApbdes::route('/{record}/edit'),
        ];
    }
}

