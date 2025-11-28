<?php

namespace App\Observers;

use App\Services\ImageConversionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ImageConversionObserver
{
    protected ImageConversionService $conversionService;

    public function __construct(ImageConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    /**
     * Handle the model "saved" event.
     * Convert images to WebP after the model is saved.
     */
    public function saved(Model $model): void
    {
        // Define image fields for each model type
        $imageFields = $this->getImageFields($model);

        foreach ($imageFields as $field) {
            // Only convert if the field was changed or model was just created
            if (!$model->wasRecentlyCreated && !$model->isDirty($field)) {
                continue;
            }

            $imagePath = $model->getAttribute($field);

            if (empty($imagePath)) {
                continue;
            }

            // Skip external URLs (http:// or https://)
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                continue;
            }

            // Skip if already WebP
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            if ($extension === 'webp') {
                continue;
            }

            // If already WebP, just clean up any original file that might exist
            if ($extension === 'webp') {
                // Check if there's an original JPG/JPEG file that should be deleted
                $originalPath = preg_replace('/\.webp$/', '.jpg', $imagePath);
                if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($originalPath)) {
                    $originalPath = preg_replace('/\.webp$/', '.jpeg', $imagePath);
                }
                
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($originalPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($originalPath);
                }
                return;
            }

            // Convert to WebP
            $webpPath = $this->conversionService->convertToWebP($imagePath);

            if ($webpPath && $webpPath !== $imagePath) {
                // Update model with WebP path
                $model->withoutEvents(function () use ($model, $field, $webpPath) {
                    $model->setAttribute($field, $webpPath);
                    $model->saveQuietly();
                });

                // Delete original file only if it exists
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }

                Log::info('Image converted to WebP via observer', [
                    'model' => get_class($model),
                    'id' => $model->getKey(),
                    'field' => $field,
                    'original' => $imagePath,
                    'webp' => $webpPath,
                ]);
            }
        }
    }

    /**
     * Get image fields for a model
     */
    protected function getImageFields(Model $model): array
    {
        $modelClass = get_class($model);

        return match ($modelClass) {
            \App\Models\Post::class => ['thumbnail'],
            \App\Models\HeroSlide::class => ['image'],
            \App\Models\Official::class => ['photo'],
            default => [],
        };
    }
}

