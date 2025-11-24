<?php

namespace App\Filament\Pages;

use App\Services\ImageConversionService;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Spatie\LaravelSettings\Settings;

class ManageIdentity extends Page
{
    protected static string $view = 'filament.pages.manage-identity';
    
    protected static ?string $navigationGroup = 'Government';
    
    protected static ?string $navigationLabel = 'Manage Identity';
    
    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Settings::group(GeneralSettings::class);
        
        $this->form->fill([
            'site_name' => $settings->site_name ?? '',
            'village_address' => $settings->village_address ?? '',
            'whatsapp' => $settings->whatsapp ?? '',
            'logo_path' => $settings->logo_path ?? null,
            'instagram' => $settings->instagram ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('site_name')
                    ->label('Site Name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('village_address')
                    ->label('Village Address')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('whatsapp')
                    ->label('WhatsApp Number')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Format: 6281227666999'),
                
                Forms\Components\FileUpload::make('logo_path')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('settings/logo')
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
                    ->saveUploadedFileUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) {
                        try {
                            $conversionService = app(ImageConversionService::class);
                            $storage = \Illuminate\Support\Facades\Storage::disk('public');
                            
                            // Save file first
                            $path = $file->storePublicly('settings/logo', 'public');
                            
                            if (!$path) {
                                return null;
                            }
                            
                            // Convert to WebP immediately
                            $webpPath = $conversionService->convertToWebP($path);
                            
                            // Return WebP path if conversion succeeded, otherwise return original
                            if ($webpPath && $webpPath !== $path && $storage->exists($webpPath)) {
                                return $webpPath;
                            }
                            
                            // If conversion failed, return original path
                            return $path;
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('File upload conversion error', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                            
                            return $path ?? null;
                        }
                    })
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('instagram')
                    ->label('Instagram')
                    ->url()
                    ->maxLength(255)
                    ->helperText('Full Instagram URL'),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Convert logo to WebP if provided
        if (!empty($data['logo_path'])) {
            $conversionService = app(ImageConversionService::class);
            $webpPath = $conversionService->convertToWebP($data['logo_path']);
            
            if ($webpPath && $webpPath !== $data['logo_path']) {
                // Delete original
                \Illuminate\Support\Facades\Storage::disk('public')->delete($data['logo_path']);
                $data['logo_path'] = $webpPath;
            }
        }
        
        Settings::group(GeneralSettings::class)->fill($data)->save();
        
        $this->notify('success', 'Settings saved successfully.');
    }
}

