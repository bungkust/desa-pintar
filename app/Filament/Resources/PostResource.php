<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Services\ImageConversionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = 'Berita';
    
    protected static ?string $navigationLabel = 'Posts';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                        // Only auto-generate slug if it's empty
                        if (empty($get('slug'))) {
                            $set('slug', Str::slug($state));
                        }
                    }),
                
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Auto-generated from title, but can be edited'),
                
                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->maxSize(5120) // 5MB max size in KB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                    ->disk('public')
                    ->directory('posts/thumbnails')
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
                        try {
                            $conversionService = app(ImageConversionService::class);
                            $storage = \Illuminate\Support\Facades\Storage::disk('public');
                            
                            // Save file first
                            $path = $file->storePublicly('posts/thumbnails', 'public');
                            
                            if (!$path) {
                                return null;
                            }
                            
                            // Convert to WebP immediately
                            $webpPath = $conversionService->convertToWebP($path);
                            
                            // Return WebP path if conversion succeeded, otherwise return original
                            // Don't delete original here - let observer handle it after model is saved
                            if ($webpPath && $webpPath !== $path && $storage->exists($webpPath)) {
                                return $webpPath;
                            }
                            
                            // If conversion failed, return original path
                            return $path;
                        } catch (\Exception $e) {
                            // Log error but don't break the upload
                            \Illuminate\Support\Facades\Log::error('File upload conversion error', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                            
                            // Return original path if conversion fails
                            return $path ?? null;
                        }
                    })
                    ->columnSpanFull(),
                
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->fileAttachmentsDirectory('posts/attachments')
                    ->fileAttachmentsDisk('public')
                    ->saveUploadedFileAttachmentsUsing(function (TemporaryUploadedFile $file) {
                        $conversionService = app(ImageConversionService::class);
                        
                        // Save file first (storePublicly for public disk)
                        $path = $file->storePublicly('posts/attachments', 'public');
                        
                        // Convert to WebP
                        $webpPath = $conversionService->convertToWebP($path);
                        
                        // Delete original if conversion succeeded
                        if ($webpPath && $webpPath !== $path) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                            return $webpPath;
                        }
                        
                        return $path;
                    })
                    ->columnSpanFull(),
                
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Published At')
                    ->helperText('Leave empty to save as draft'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('public_url')
                    ->label('URL Publik')
                    ->state(fn (?Post $record) => $record ? route('post.show', $record->slug) : null)
                    ->icon('heroicon-o-link')
                    ->copyable()
                    ->copyMessage('Link disalin')
                    ->url(fn (?Post $record) => $record ? route('post.show', $record->slug) : null, shouldOpenInNewTab: true)
                    ->visible(fn (?Post $record) => $record && filled($record->published_at) && $record->published_at->lte(now()))
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Published')
                    ->placeholder('Draft'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->label('Published')
                    ->query(fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())),
                
                Tables\Filters\Filter::make('draft')
                    ->label('Draft')
                    ->query(fn ($query) => $query->where(function ($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '>', now());
                    })),
            ])
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Post')
                    ->modalDescription('Apakah Anda yakin ingin menghapus post ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->action(function (Model $record) {
                        $record->delete();
                        return redirect()->to(static::getUrl('index'));
                    })
                    ->successNotificationTitle('Post berhasil dihapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('published_at', 'desc')
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

