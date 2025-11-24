<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\ImageConversionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixPostThumbnails extends Command
{
    protected $signature = 'posts:fix-thumbnails';
    protected $description = 'Fix post thumbnails by converting remaining JPG files to WebP and updating database';

    protected ImageConversionService $conversionService;

    public function __construct(ImageConversionService $conversionService)
    {
        parent::__construct();
        $this->conversionService = $conversionService;
    }

    public function handle(): int
    {
        $this->info('Fixing post thumbnails...');
        $this->newLine();

        $posts = Post::whereNotNull('thumbnail')->get();
        $fixed = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($posts as $post) {
            $thumbnailPath = $post->thumbnail;

            // Skip if already WebP
            if (str_ends_with($thumbnailPath, '.webp')) {
                // Check if WebP file exists
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    $skipped++;
                    continue;
                } else {
                    // WebP path in DB but file doesn't exist, try to find JPG version
                    $jpgPath = preg_replace('/\.webp$/', '.jpg', $thumbnailPath);
                    $jpegPath = preg_replace('/\.webp$/', '.jpeg', $thumbnailPath);
                    
                    if (Storage::disk('public')->exists($jpgPath)) {
                        $this->line("Found JPG for missing WebP: {$jpgPath}");
                        $webpPath = $this->conversionService->convertToWebP($jpgPath);
                        if ($webpPath) {
                            $post->thumbnail = $webpPath;
                            $post->saveQuietly();
                            Storage::disk('public')->delete($jpgPath);
                            $fixed++;
                            $this->info("  Fixed post ID {$post->id}");
                        }
                    } elseif (Storage::disk('public')->exists($jpegPath)) {
                        $this->line("Found JPEG for missing WebP: {$jpegPath}");
                        $webpPath = $this->conversionService->convertToWebP($jpegPath);
                        if ($webpPath) {
                            $post->thumbnail = $webpPath;
                            $post->saveQuietly();
                            Storage::disk('public')->delete($jpegPath);
                            $fixed++;
                            $this->info("  Fixed post ID {$post->id}");
                        }
                    } else {
                        $this->warn("  Post ID {$post->id}: WebP file not found and no JPG/JPEG alternative");
                        $failed++;
                    }
                }
                continue;
            }

            // Check if it's an external URL
            if (str_starts_with($thumbnailPath, 'http://') || str_starts_with($thumbnailPath, 'https://')) {
                $this->line("  Post ID {$post->id}: External URL detected, downloading...");
                
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(30)->get($thumbnailPath);
                    
                    if ($response->successful()) {
                        // Generate filename
                        $filename = \Illuminate\Support\Str::random(40) . '.jpg';
                        $tempPath = 'posts/thumbnails/' . $filename;
                        
                        // Save temporarily
                        Storage::disk('public')->put($tempPath, $response->body());
                        
                        // Convert to WebP
                        $webpPath = $this->conversionService->convertToWebP($tempPath);
                        
                        if ($webpPath && $webpPath !== $tempPath) {
                            // Update database
                            $post->thumbnail = $webpPath;
                            $post->saveQuietly();
                            
                            // Delete temp file
                            Storage::disk('public')->delete($tempPath);
                            
                            $fixed++;
                            $this->info("  Fixed post ID {$post->id}: Downloaded and converted to {$webpPath}");
                        } else {
                            // If conversion failed, keep the temp file
                            $post->thumbnail = $tempPath;
                            $post->saveQuietly();
                            $fixed++;
                            $this->info("  Fixed post ID {$post->id}: Downloaded to {$tempPath} (conversion failed)");
                        }
                    } else {
                        $this->warn("  Post ID {$post->id}: Failed to download from URL, setting to null");
                        // Set thumbnail to null if download fails
                        $post->thumbnail = null;
                        $post->saveQuietly();
                        $fixed++;
                    }
                } catch (\Exception $e) {
                    $this->warn("  Post ID {$post->id}: Error downloading - {$e->getMessage()}, setting to null");
                    // Set thumbnail to null if error occurs
                    $post->thumbnail = null;
                    $post->saveQuietly();
                    $fixed++;
                }
                continue;
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($thumbnailPath)) {
                $this->warn("  Post ID {$post->id}: File not found - {$thumbnailPath}");
                $failed++;
                continue;
            }

            // Convert to WebP
            $webpPath = $this->conversionService->convertToWebP($thumbnailPath);

            if ($webpPath && $webpPath !== $thumbnailPath) {
                // Update database
                $post->thumbnail = $webpPath;
                $post->saveQuietly();

                // Delete original
                Storage::disk('public')->delete($thumbnailPath);

                $fixed++;
                $this->info("  Fixed post ID {$post->id}: {$thumbnailPath} -> {$webpPath}");
            } else {
                $failed++;
                $this->error("  Failed to convert post ID {$post->id}: {$thumbnailPath}");
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Fixed', $fixed],
                ['Skipped', $skipped],
                ['Failed', $failed],
            ]
        );

        return Command::SUCCESS;
    }
}

