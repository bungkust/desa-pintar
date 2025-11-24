<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageConversionService
{
    protected ImageManager $imageManager;
    protected int $quality;

    public function __construct(int $quality = 85)
    {
        $this->quality = $quality;
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Check if WebP encoding is supported
     */
    public function isWebPSupported(): bool
    {
        if (extension_loaded('gd')) {
            $gdInfo = gd_info();
            return isset($gdInfo['WebP Support']) && $gdInfo['WebP Support'];
        }

        if (extension_loaded('imagick')) {
            $imagick = new \Imagick();
            $formats = $imagick->queryFormats();
            return in_array('WEBP', $formats);
        }

        return false;
    }

    /**
     * Convert an image to WebP format
     *
     * @param string $filePath Relative path to the image file
     * @param string $disk Storage disk name (default: 'public')
     * @return string|null New WebP file path or null on failure
     */
    public function convertToWebP(string $filePath, string $disk = 'public'): ?string
    {
        if (!$this->isWebPSupported()) {
            Log::error('WebP conversion failed: WebP encoding not supported', [
                'file' => $filePath,
            ]);
            return null;
        }

        $storage = Storage::disk($disk);

        // Check if file exists
        if (!$storage->exists($filePath)) {
            Log::warning('WebP conversion failed: File does not exist', [
                'file' => $filePath,
                'disk' => $disk,
            ]);
            return null;
        }

        // Skip if already WebP
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension === 'webp') {
            return $filePath;
        }

        try {
            // Read the image file
            $imageContent = $storage->get($filePath);

            // Create image from binary data
            $image = $this->imageManager->read($imageContent);

            // Generate WebP path
            $webpPath = $this->getWebPPath($filePath);

            // Encode as WebP
            $webpContent = $image->toWebp($this->quality);

            // Save WebP file
            $storage->put($webpPath, $webpContent);

            Log::info('Image converted to WebP', [
                'original' => $filePath,
                'webp' => $webpPath,
            ]);

            return $webpPath;
        } catch (\Exception $e) {
            Log::error('WebP conversion failed', [
                'file' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Convert and replace original file with WebP version
     *
     * @param string $originalPath Relative path to the original image
     * @param string $disk Storage disk name (default: 'public')
     * @return string|null New WebP file path or null on failure
     */
    public function convertAndReplace(string $originalPath, string $disk = 'public'): ?string
    {
        $webpPath = $this->convertToWebP($originalPath, $disk);

        if ($webpPath && $webpPath !== $originalPath) {
            // Delete original file
            Storage::disk($disk)->delete($originalPath);
            return $webpPath;
        }

        return $webpPath;
    }

    /**
     * Generate WebP file path from original path
     *
     * @param string $originalPath Original file path
     * @return string WebP file path
     */
    public function getWebPPath(string $originalPath): string
    {
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '';
        $filename = $pathInfo['filename'];

        return $directory . $filename . '.webp';
    }

    /**
     * Set WebP quality
     *
     * @param int $quality Quality value (0-100)
     * @return self
     */
    public function setQuality(int $quality): self
    {
        $this->quality = max(0, min(100, $quality));
        return $this;
    }

    /**
     * Get current quality setting
     *
     * @return int
     */
    public function getQuality(): int
    {
        return $this->quality;
    }
}

