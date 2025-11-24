<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;

    protected static ?string $navigationGroup = 'Website Content';
    
    protected static ?string $navigationLabel = 'Agenda';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Agenda')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->required()
                            ->options([
                                'pemerintahan' => 'Pemerintahan',
                                'kesehatan' => 'Kesehatan',
                                'lingkungan' => 'Lingkungan',
                                'budaya' => 'Budaya',
                                'umum' => 'Umum',
                            ])
                            ->native(false),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Waktu & Lokasi')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Waktu Mulai')
                            ->seconds(false),
                        
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Waktu Selesai')
                            ->seconds(false),
                        
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('google_maps_url')
                            ->label('Google Maps URL')
                            ->url()
                            ->maxLength(500)
                            ->helperText('Link Google Maps untuk lokasi acara')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Kontak & Organizer')
                    ->schema([
                        Forms\Components\TextInput::make('organizer')
                            ->label('Organizer')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('contact_person')
                            ->label('Kontak')
                            ->maxLength(255)
                            ->helperText('Nama dan nomor kontak yang bisa dihubungi'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Media & Pengaturan')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('agendas')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Maksimal 2MB. Format: JPG, PNG, GIF')
                            ->columnSpanFull(),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Tampilkan sebagai Featured')
                            ->default(false)
                            ->helperText('Agenda featured akan ditampilkan lebih menonjol'),
                        
                        Forms\Components\Toggle::make('is_recurring')
                            ->label('Acara Berulang')
                            ->default(false)
                            ->live()
                            ->helperText('Centang jika acara ini berulang setiap minggu atau bulan'),
                        
                        Forms\Components\Select::make('recurring_type')
                            ->label('Jenis Pengulangan')
                            ->options([
                                'weekly' => 'Mingguan',
                                'monthly' => 'Bulanan',
                            ])
                            ->native(false)
                            ->visible(fn (Forms\Get $get) => $get('is_recurring'))
                            ->required(fn (Forms\Get $get) => $get('is_recurring')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemerintahan' => 'info',
                        'kesehatan' => 'success',
                        'lingkungan' => 'warning',
                        'budaya' => 'danger',
                        'umum' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pemerintahan' => 'Pemerintahan',
                        'kesehatan' => 'Kesehatan',
                        'lingkungan' => 'Lingkungan',
                        'budaya' => 'Budaya',
                        'umum' => 'Umum',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Waktu')
                    ->formatStateUsing(function ($record) {
                        $startRaw = $record->getRawOriginal('start_time');
                        $endRaw = $record->getRawOriginal('end_time');
                        
                        if ($startRaw && $endRaw) {
                            return $startRaw . ' - ' . $endRaw . ' WIB';
                        } elseif ($startRaw) {
                            return $startRaw . ' WIB';
                        }
                        return '-';
                    })
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('Berulang')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('recurring_type')
                    ->label('Jenis Pengulangan')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        default => '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'pemerintahan' => 'Pemerintahan',
                        'kesehatan' => 'Kesehatan',
                        'lingkungan' => 'Lingkungan',
                        'budaya' => 'Budaya',
                        'umum' => 'Umum',
                    ])
                    ->native(false),
                
                Tables\Filters\Filter::make('is_featured')
                    ->label('Featured')
                    ->query(fn ($query) => $query->where('is_featured', true)),
                
                Tables\Filters\Filter::make('is_recurring')
                    ->label('Berulang')
                    ->query(fn ($query) => $query->where('is_recurring', true)),
                
                Tables\Filters\Filter::make('upcoming')
                    ->label('Mendatang')
                    ->query(fn ($query) => $query->where('date', '>=', now()->toDateString())),
                
                Tables\Filters\Filter::make('past')
                    ->label('Sudah Lewat')
                    ->query(fn ($query) => $query->where('date', '<', now()->toDateString())),
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
            ->defaultSort('date', 'desc')
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
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }
}
