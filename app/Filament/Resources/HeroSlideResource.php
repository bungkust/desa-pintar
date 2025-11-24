<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use App\Services\ImageConversionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationGroup = 'Website Content';
    
    protected static ?string $navigationLabel = 'Hero Slides';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('subtitle')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->maxSize(5120) // 5MB max size in KB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->disk('public')
                    ->directory('hero-slides')
                    ->visibility('public')
                    ->getUploadedFileUsing(function (string $file) {
                        $storage = \Illuminate\Support\Facades\Storage::disk('public');
                        
                        // Fix URL to use current request host instead of APP_URL
                        $url = $storage->url($file);
                        
                        // Replace localhost with current request host if needed
                        if (str_contains($url, 'localhost') && request()->getHost() !== 'localhost') {
                            $url = str_replace('http://localhost', request()->getSchemeAndHttpHost(), $url);
                        }
                        
                        return [
                            'name' => basename($file),
                            'size' => $storage->exists($file) ? $storage->size($file) : 0,
                            'type' => $storage->exists($file) ? $storage->mimeType($file) : null,
                            'url' => $url,
                        ];
                    })
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                        $conversionService = app(ImageConversionService::class);
                        $storage = \Illuminate\Support\Facades\Storage::disk('public');
                        
                        // Save file first
                        $path = $file->storePublicly('hero-slides', 'public');
                        
                        // Convert to WebP immediately
                        $webpPath = $conversionService->convertToWebP($path);
                        
                        // Return WebP path if conversion succeeded, otherwise return original
                        // Don't delete original here - let observer handle it after model is saved
                        if ($webpPath && $webpPath !== $path && $storage->exists($webpPath)) {
                            return $webpPath;
                        }
                        
                        // If conversion failed, return original path
                        return $path;
                    })
                    ->columnSpanFull(),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
                
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('subtitle')
                    ->limit(50)
                    ->searchable(),
                
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All slides')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
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
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}

