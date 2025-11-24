<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuickLinkResource\Pages;
use App\Models\QuickLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class QuickLinkResource extends Resource
{
    protected static ?string $model = QuickLink::class;

    protected static ?string $navigationGroup = 'Website Content';
    
    protected static ?string $navigationLabel = 'Quick Links';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Select::make('icon_class')
                    ->label('Icon')
                    ->required()
                    ->searchable()
                    ->native(false)
                    ->options([
                        'heroicon-o-document-text' => 'Document Text',
                        'heroicon-o-scale' => 'Scale / Balance',
                        'heroicon-o-shopping-bag' => 'Shopping Bag',
                        'heroicon-o-chat-bubble-left' => 'Chat / Message',
                        'heroicon-o-user-group' => 'User Group',
                        'heroicon-o-home' => 'Home',
                        'heroicon-o-map' => 'Map / Location',
                        'heroicon-o-chart-bar' => 'Chart / Statistics',
                        'heroicon-o-clipboard-document-list' => 'Clipboard / List',
                        'heroicon-o-envelope' => 'Envelope / Mail',
                        'heroicon-o-phone' => 'Phone',
                        'heroicon-o-globe-alt' => 'Globe / Website',
                        'heroicon-o-newspaper' => 'Newspaper / News',
                        'heroicon-o-building-office' => 'Building / Office',
                        'heroicon-o-calendar' => 'Calendar',
                        'heroicon-o-clock' => 'Clock / Time',
                        'heroicon-o-banknotes' => 'Banknotes / Money',
                        'heroicon-o-identification' => 'Identification / ID',
                        'heroicon-o-document-check' => 'Document Check',
                        'heroicon-o-information-circle' => 'Information',
                        'heroicon-o-exclamation-circle' => 'Exclamation / Alert',
                        'heroicon-o-check-circle' => 'Check / Complete',
                        'heroicon-o-star' => 'Star',
                        'heroicon-o-heart' => 'Heart',
                        'heroicon-o-bookmark' => 'Bookmark',
                        'heroicon-o-megaphone' => 'Megaphone / Announcement',
                        'heroicon-o-flag' => 'Flag',
                        'heroicon-o-light-bulb' => 'Light Bulb / Idea',
                        'heroicon-o-sparkles' => 'Sparkles',
                        'heroicon-o-gift' => 'Gift',
                        'heroicon-o-trophy' => 'Trophy',
                        'heroicon-o-fire' => 'Fire / Hot',
                        'heroicon-o-hand-raised' => 'Hand Raised',
                        'heroicon-o-academic-cap' => 'Academic Cap / Education',
                        'heroicon-o-briefcase' => 'Briefcase',
                    ])
                    ->helperText('Pilih icon yang akan ditampilkan pada quick link card. Icon menggunakan Heroicons Outline style.'),
                
                Forms\Components\TextInput::make('url')
                    ->label('URL/Route')
                    ->helperText('Masukkan URL lengkap (https://...), route name (berita), atau path (/berita). Kosongkan atau isi # untuk menggunakan halaman dummy berdasarkan label.')
                    ->maxLength(255)
                    ->nullable(),
                
                Forms\Components\ColorPicker::make('color')
                    ->label('Color')
                    ->helperText('Hex color code (e.g., #3B82F6)'),
                
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
                
                Tables\Columns\TextColumn::make('icon_class')
                    ->label('Icon')
                    ->formatStateUsing(function ($state) {
                        return $state ? str_replace('heroicon-o-', '', $state) : '-';
                    })
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('url')
                    ->limit(50),
                
                Tables\Columns\ColorColumn::make('color'),
                
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
            'index' => Pages\ListQuickLinks::route('/'),
            'create' => Pages\CreateQuickLink::route('/create'),
            'edit' => Pages\EditQuickLink::route('/{record}/edit'),
        ];
    }
}

