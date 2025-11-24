<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficialResource\Pages;
use App\Models\Official;
use App\Services\ImageConversionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class OfficialResource extends Resource
{
    protected static ?string $model = Official::class;

    protected static ?string $navigationGroup = 'Government';
    
    protected static ?string $navigationLabel = 'Officials';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->disk('public')
                    ->directory('officials/photos')
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
                        $path = $file->storePublicly('officials/photos', 'public');
                        
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
                
                Forms\Components\RichEditor::make('greeting')
                    ->label('Sambutan')
                    ->helperText('Tuliskan sambutan/welcome message untuk ditampilkan di homepage. Jika kosong, akan menggunakan sambutan default.')
                    ->columnSpanFull(),
                
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
                Tables\Columns\ImageColumn::make('photo')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('has_greeting')
                    ->label('Ada Sambutan')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->greeting))
                    ->sortable(),
                
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
            'index' => Pages\ListOfficials::route('/'),
            'create' => Pages\CreateOfficial::route('/create'),
            'edit' => Pages\EditOfficial::route('/{record}/edit'),
        ];
    }
}

