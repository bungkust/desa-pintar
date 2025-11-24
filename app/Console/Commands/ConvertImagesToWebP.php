<?php

namespace App\Console\Commands;

use App\Models\HeroSlide;
use App\Models\Official;
use App\Models\Post;
use App\Services\ImageConversionService;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ConvertImagesToWebP extends Command
{
    protected $signature = 'images:convert-to-webp 
                            {--dry-run : Show what would be converted without doing it}
                            {--backup : Keep original files after conversion}
                            {--force : Convert even if WebP already exists}';

    protected $description = 'Convert all existing images to WebP format and update database records';

    protected ImageConversionService $conversionService;

    public function __construct(ImageConversionService $conversionService)
    {
        parent::__construct();
        $this->conversionService = $conversionService;
    }

    public function handle(): int
    {
        if (!$this->conversionService->isWebPSupported()) {
            $this->error('WebP encoding is not supported on this system.');
            $this->info('Please ensure PHP GD or Imagick extension is installed with WebP support.');
            return Command::FAILURE;
        }

        $this->info('Starting image conversion to WebP...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $backup = $this->option('backup');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be converted');
            $this->newLine();
        }

        // Find all image files
        $imageFiles = $this->findImageFiles();
        $this->info("Found {$imageFiles->count()} image files to process");
        $this->newLine();

        if ($imageFiles->isEmpty()) {
            $this->info('No images found to convert.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($imageFiles->count());
        $progressBar->start();

        $converted = 0;
        $skipped = 0;
        $failed = 0;
        $dbUpdated = 0;

        foreach ($imageFiles as $filePath) {
            $progressBar->advance();

            // Skip if WebP already exists and not forcing
            if (!$force && $this->webpExists($filePath)) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $converted++;
                continue;
            }

            // Convert to WebP
            $webpPath = $this->conversionService->convertToWebP($filePath);

            if (!$webpPath) {
                $failed++;
                continue;
            }

            $converted++;

            // Update database records
            if ($this->updateDatabaseRecords($filePath, $webpPath)) {
                $dbUpdated++;
            }

            // Delete original if not backing up
            if (!$backup && $webpPath !== $filePath) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Conversion Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Converted', $converted],
                ['Skipped', $skipped],
                ['Failed', $failed],
                ['Database Updated', $dbUpdated],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Run without --dry-run to perform actual conversion.');
        }

        return Command::SUCCESS;
    }

    /**
     * Find all image files in storage/app/public
     */
    protected function findImageFiles(): \Illuminate\Support\Collection
    {
        $storage = Storage::disk('public');
        $allFiles = $storage->allFiles();
        
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        return collect($allFiles)->filter(function ($file) use ($imageExtensions) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($extension, $imageExtensions);
        });
    }

    /**
     * Check if WebP version already exists
     */
    protected function webpExists(string $filePath): bool
    {
        $pathInfo = pathinfo($filePath);
        $directory = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '';
        $filename = $pathInfo['filename'];
        $webpPath = $directory . $filename . '.webp';
        return Storage::disk('public')->exists($webpPath);
    }

    /**
     * Update database records with new WebP paths
     */
    protected function updateDatabaseRecords(string $oldPath, string $newPath): bool
    {
        if ($oldPath === $newPath) {
            return false;
        }

        $updated = false;

        try {
            DB::beginTransaction();

            // Update posts.thumbnail
            $postsUpdated = Post::where('thumbnail', $oldPath)->update(['thumbnail' => $newPath]);
            if ($postsUpdated > 0) {
                $this->line("  Updated {$postsUpdated} post(s) thumbnail");
                $updated = true;
            }

            // Update hero_slides.image
            $heroSlidesUpdated = HeroSlide::where('image', $oldPath)->update(['image' => $newPath]);
            if ($heroSlidesUpdated > 0) {
                $this->line("  Updated {$heroSlidesUpdated} hero slide(s) image");
                $updated = true;
            }

            // Update officials.photo
            $officialsUpdated = Official::where('photo', $oldPath)->update(['photo' => $newPath]);
            if ($officialsUpdated > 0) {
                $this->line("  Updated {$officialsUpdated} official(s) photo");
                $updated = true;
            }

            // Update settings.logo_path (via Spatie Settings)
            try {
                $settings = app(GeneralSettings::class);
                if ($settings->logo_path === $oldPath) {
                    $settings->logo_path = $newPath;
                    $settings->save();
                    $this->line("  Updated settings logo_path");
                    $updated = true;
                }
            } catch (\Exception $e) {
                // Settings might not be initialized yet, skip
            }

            DB::commit();
            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("  Failed to update database: {$e->getMessage()}");
            return false;
        }
    }
}

