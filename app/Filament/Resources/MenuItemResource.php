<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Filament\Resources\MenuItemResource\RelationManagers;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationGroup = 'Website Content';
    
    protected static ?string $navigationLabel = 'Menu Items';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Label Menu')
                    ->helperText('Nama yang ditampilkan di menu'),
                
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Menu')
                    ->options(function ($record) {
                        return \Illuminate\Support\Facades\Cache::remember('menu_parent_options_' . ($record?->id ?? 'new'), 3600, function () use ($record) {
                            $query = MenuItem::query();
                            
                            // Exclude current record and its descendants to prevent circular reference
                            if ($record) {
                                $excludeIds = [$record->id];
                                // Get all descendant IDs
                                $descendants = $record->descendants()->pluck('id')->toArray();
                                $excludeIds = array_merge($excludeIds, $descendants);
                                $query->whereNotIn('id', $excludeIds);
                            }
                            
                            // Only show items that can be parents (max level 1)
                            // This ensures max 2 levels: Menu Utama > Submenu > Sub-submenu (max level 2)
                            $query->where(function ($q) {
                                $q->whereNull('parent_id') // Menu Utama (level 0)
                                  ->orWhereHas('parent', function ($parentQ) {
                                      $parentQ->whereNull('parent_id'); // Submenu (level 1, parent is Menu Utama)
                                  });
                            });
                            
                            // Format options with breadcrumb path and level indicator
                            return $query->orderBy('order')->get()->mapWithKeys(function ($item) {
                                $level = $item->getLevel();
                                $levelLabel = $item->getLevelLabel();
                                $prefix = str_repeat('  ', $level) . ($level > 0 ? '└─ ' : '');
                                $label = $prefix . $item->label . ' (' . $levelLabel . ')';
                                return [$item->id => $label];
                            })->toArray();
                        });
                    })
                    ->searchable()
                    ->helperText(function ($record) {
                        $currentLevel = $record ? $record->getLevel() : -1;
                        $maxLevel = 2; // Menu Utama (0) > Submenu (1) > Sub-submenu (2)
                        
                        if ($currentLevel >= $maxLevel) {
                            return 'Menu ini sudah mencapai level maksimum (Sub-submenu). Tidak bisa ditambahkan submenu lagi.';
                        }
                        
                        return 'Pilih parent menu jika ini adalah submenu. Kosongkan untuk Menu Utama. Maksimal 3 level: Menu Utama > Submenu > Sub-submenu.';
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Validate parent selection
                        if ($state) {
                            $parent = MenuItem::find($state);
                            if ($parent) {
                                $parentLevel = $parent->getLevel();
                                if ($parentLevel >= 2) {
                                    // Cannot select parent that would create level > 2
                                    $set('parent_id', null);
                                }
                            }
                        }
                    })
                    ->nullable()
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                if ($value) {
                                    $parent = MenuItem::find($value);
                                    if ($parent) {
                                        $parentLevel = $parent->getLevel();
                                        if ($parentLevel >= 2) {
                                            $fail('Parent menu yang dipilih akan membuat level melebihi maksimum (3 level).');
                                        }
                                    }
                                }
                            };
                        },
                    ])
                    ->disabled(fn ($record) => $record && $record->children()->count() > 0),
                
                Forms\Components\Select::make('type')
                    ->options([
                        'anchor' => 'Anchor Link (#section)',
                        'route' => 'Route Name',
                        'url' => 'Full URL',
                        'dropdown' => 'Dropdown Menu (Tidak redirect, hanya menampilkan submenu)',
                    ])
                    ->default('anchor')
                    ->required()
                    ->label('Tipe Menu')
                    ->helperText('Pilih tipe link menu. Gunakan "Dropdown Menu" untuk menu yang hanya menampilkan submenu tanpa redirect.')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Auto-set URL to "#" when type is dropdown
                        if ($state === 'dropdown') {
                            $set('url', '#');
                            $set('route_name', null);
                            $set('route_params', null);
                        }
                        // Auto-detect and set route if URL matches internal post pattern
                        if ($state === 'route' && $get('url')) {
                            $url = $get('url');
                            if (preg_match('/^\/posts\/([^\/?#]+)/', $url, $matches)) {
                                $slug = $matches[1];
                                // Remove numeric suffix if exists
                                if (preg_match('/^(.+)-(\d+)$/', $slug, $slugMatches)) {
                                    $slug = $slugMatches[1];
                                }
                                $set('route_name', 'post.show');
                                $set('route_params', ['slug' => $slug]);
                                $set('url', null);
                            }
                        }
                    }),
                
                // URL field - hidden for dropdown, shown for anchor/url
                Forms\Components\TextInput::make('url')
                    ->maxLength(255)
                    ->label('URL/Link')
                    ->helperText(function ($get) {
                        $type = $get('type');
                        if ($type === 'dropdown') {
                            return 'URL otomatis diisi "#" untuk dropdown menu.';
                        }
                        if ($type === 'anchor') {
                            return 'Format: #section-name (harus dimulai dengan #)';
                        }
                        if ($type === 'route') {
                            return 'Masukkan URL internal seperti /posts/slug atau gunakan field Route Name di bawah.';
                        }
                        return 'Contoh: https://example.com (URL eksternal).';
                    })
                    ->required(fn ($get) => !in_array($get('type'), ['dropdown', 'route']))
                    ->default(function ($get, $record) {
                        if ($get('type') === 'dropdown') {
                            return '#';
                        }
                        return $record?->url ?? '';
                    })
                    ->dehydrateStateUsing(function ($state, $get) {
                        if ($get('type') === 'dropdown') {
                            return '#';
                        }
                        if ($get('type') === 'route' && empty($state)) {
                            return null;
                        }
                        return $state;
                    })
                    ->rules([
                        fn ($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $type = $get('type');
                            if ($type === 'anchor' && !empty($value) && !str_starts_with($value, '#')) {
                                $fail('Anchor link harus dimulai dengan #');
                            }
                        },
                    ])
                    ->hidden(fn ($get) => $get('type') === 'dropdown')
                    ->visible(fn ($get) => in_array($get('type'), ['anchor', 'url', 'route']))
                    ->reactive(),
                
                // Route Name field - shown only for route type
                Forms\Components\Select::make('route_name')
                    ->label('Route Name')
                    ->options([
                        'post.show' => 'Post Detail (post.show)',
                        'berita' => 'Berita List (berita)',
                        'apbdes.show' => 'APBDes Detail (apbdes.show)',
                        'statistik-lengkap' => 'Statistik Lengkap (statistik-lengkap)',
                        'layanan-surat' => 'Layanan Surat (layanan-surat)',
                        'peraturan-desa' => 'Peraturan Desa (peraturan-desa)',
                    ])
                    ->searchable()
                    ->helperText('Pilih route name untuk link internal')
                    ->visible(fn ($get) => $get('type') === 'route')
                    ->live(),
                
                // Route Params field - shown only for route type with route_name = post.show
                Forms\Components\TextInput::make('route_params.slug')
                    ->label('Post Slug')
                    ->helperText('Slug dari post yang akan ditampilkan. Auto-extracted dari URL jika URL berupa /posts/{slug}')
                    ->visible(fn ($get) => $get('type') === 'route' && $get('route_name') === 'post.show')
                    ->reactive(),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->helperText('Nonaktifkan untuk menyembunyikan menu'),
                
                Forms\Components\Placeholder::make('computed_level')
                    ->label('Level Menu (Otomatis)')
                    ->content(function ($get) {
                        $parentId = $get('parent_id');
                        
                        if ($parentId) {
                            $parent = MenuItem::find($parentId);
                            if ($parent) {
                                $parentLevel = $parent->getLevel();
                                $newLevel = $parentLevel + 1;
                                
                                if ($newLevel > 2) {
                                    return 'Tidak valid: Melebihi level maksimum (Level ' . $newLevel . ')';
                                }
                                
                                return match ($newLevel) {
                                    0 => 'Menu Utama',
                                    1 => 'Submenu',
                                    2 => 'Sub-submenu',
                                    default => 'Level ' . $newLevel,
                                };
                            }
                        }
                        
                        // No parent = level 0
                        return 'Menu Utama';
                    })
                    ->reactive()
                    ->visible(fn ($record) => $record !== null)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(function ($record, $get) {
                        // Auto-assign order: get max order for same parent_id + 1
                        $parentId = $get('parent_id');
                        $maxOrder = MenuItem::where('parent_id', $parentId)->max('order') ?? 0;
                        return $maxOrder + 1;
                    })
                    ->required()
                    ->label('Urutan')
                    ->helperText('Urutan akan diatur otomatis saat menggunakan drag & drop reorder. Urutan harus unik per parent yang sama.')
                    ->rules([
                        function ($get, $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $parentId = $get('parent_id');
                                
                                if ($value === null || $value === '') {
                                    return;
                                }
                                
                                // Check if order already exists for same parent_id
                                $existing = MenuItem::where('parent_id', $parentId)
                                    ->where('order', $value)
                                    ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                    ->first();
                                
                                if ($existing) {
                                    $parentLabel = $parentId 
                                        ? (MenuItem::find($parentId)?->label ?? 'Parent') 
                                        : 'Menu Utama';
                                    $fail("Urutan {$value} sudah digunakan oleh menu lain pada {$parentLabel}. Urutan harus unik per parent.");
                                }
                            };
                        },
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Label column with tree indentation
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->label('Label Menu')
                    ->weight('bold')
                    ->formatStateUsing(function ($state, $record) {
                        $level = $record->getLevel();
                        $icon = match ($level) {
                            0 => '📌',
                            1 => '└─',
                            2 => '└─',
                            default => '',
                        };
                        
                        // Simple indentation
                        $indent = str_repeat('    ', $level);
                        
                        return $indent . $icon . ' ' . $state;
                    }),
                
                // Level badge column
                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->state(function ($record) {
                        return $record->getLevelLabel();
                    })
                    ->color(fn ($record): string => match ($record->getLevel()) {
                        0 => 'primary',
                        1 => 'success',
                        2 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('
                            CASE 
                                WHEN parent_id IS NULL THEN 0
                                WHEN parent_id IN (SELECT id FROM menu_items WHERE parent_id IS NULL) THEN 1
                                ELSE 2
                            END ' . $direction
                        );
                    }),
                
                // Parent name column
                Tables\Columns\TextColumn::make('parent.label')
                    ->label('Parent')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?? '—')
                    ->default('—'),
                
                // URL column
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->label('URL')
                    ->formatStateUsing(function ($state, $record) {
                        // For dropdown type, always show as dropdown
                        if ($record->type === 'dropdown') {
                            return '— (Dropdown)';
                        }
                        // Show actual URL for other types
                        return $state ?? '—';
                    })
                    ->limit(40)
                    ->copyable()
                    ->copyMessage('URL disalin!'),
                
                // Type column
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'anchor' => 'Anchor',
                        'route' => 'Route',
                        'url' => 'URL',
                        'dropdown' => 'Dropdown',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'anchor' => 'success',      // Green for anchor
                        'route' => 'primary',       // Blue for route
                        'url' => 'warning',         // Yellow for external URL
                        'dropdown' => 'gray',       // Gray for dropdown
                        default => 'gray',
                    }),
                
                // Has Children column
                Tables\Columns\TextColumn::make('children_count')
                    ->label('Submenu')
                    ->counts('children')
                    ->formatStateUsing(function ($state, $record) {
                        $count = $record->children_count ?? $state ?? 0;
                        return $count > 0 ? "{$count} submenu" : '—';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $count = $record->children_count ?? 0;
                        return $count > 0 ? 'success' : 'gray';
                    })
                    ->sortable(),
                
                // Status column
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status')
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                // Warning badge for orphans
                Tables\Columns\TextColumn::make('warning')
                    ->label('')
                    ->formatStateUsing(function ($record) {
                        if ($record->isOrphan()) {
                            return '⚠️ Invalid';
                        }
                        return '';
                    })
                    ->badge()
                    ->color('danger')
                    ->default(''),
                
                // Order column (for reordering)
                Tables\Columns\TextColumn::make('order')
                    ->sortable()
                    ->label('Urutan')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter 1: Level
                Tables\Filters\SelectFilter::make('level')
                    ->label('Level')
                    ->options([
                        0 => 'Menu Utama',
                        1 => 'Submenu',
                        2 => 'Sub-submenu',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            isset($data['value']) && $data['value'] !== null,
                            fn (Builder $q) => match ((int) $data['value']) {
                                0 => $q->whereNull('parent_id'),
                                1 => $q->whereHas('parent', function ($parentQ) {
                                    $parentQ->whereNull('parent_id');
                                }),
                                2 => $q->whereHas('parent', function ($parentQ) {
                                    $parentQ->whereHas('parent', function ($subQ) {
                                        $subQ->whereNull('parent_id');
                                    });
                                }),
                                default => $q,
                            }
                        );
                    }),
                
                // Filter 2: Parent Menu (Conditional - only shown when Level is set and != 0)
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Menu')
                    ->options(function () {
                        // Query all possible parents (level 0 and 1)
                        // The actual filtering will be handled by the level filter
                        return MenuItem::query()
                            ->where(function ($q) {
                                // Level 0 parents (Menu Utama)
                                $q->whereNull('parent_id')
                                  // Level 1 parents (Submenu)
                                  ->orWhereHas('parent', function ($parentQ) {
                                      $parentQ->whereNull('parent_id');
                                  });
                            })
                            ->orderBy('order')
                            ->get()
                            ->mapWithKeys(fn ($item) => [$item->id => $item->label])
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            isset($data['value']) && $data['value'] !== null,
                            fn (Builder $q) => $q->where('parent_id', $data['value'])
                        );
                    })
                    ->searchable()
                    ->preload(),
                
                // Filter 3: Tipe Menu
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Menu')
                    ->options([
                        'anchor' => 'Anchor',
                        'url' => 'URL',
                        'route' => 'Route',
                        'dropdown' => 'Dropdown',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            isset($data['value']) && $data['value'] !== null,
                            fn (Builder $q) => $q->where('type', $data['value'])
                        );
                    }),
                
                // Filter 4: Status Aktif
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                isset($data['value']) && $data['value'] === '1',
                                fn (Builder $q) => $q->where('is_active', 1)
                            )
                            ->when(
                                isset($data['value']) && $data['value'] === '0',
                                fn (Builder $q) => $q->where('is_active', 0)
                            );
                    }),
            ])
            ->persistFiltersInSession()
            ->reorderable('order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Menu Item')
                    ->modalDescription(fn ($record) => 
                        $record->children()->count() > 0 
                            ? "Peringatan: Menu ini memiliki {$record->children()->count()} submenu yang juga akan terhapus!"
                            : 'Apakah Anda yakin ingin menghapus menu ini?'
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('order', 'asc')
            ->defaultPaginationPageOption(25)
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('Tidak ada menu items')
            ->emptyStateDescription('Mulai dengan membuat menu item baru.')
            ->emptyStateIcon('heroicon-o-bars-3-bottom-left');
    }

    /**
     * Optimize query with eager loading to prevent N+1 queries
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['parent.parent', 'children'])
            ->withCount('children');
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
