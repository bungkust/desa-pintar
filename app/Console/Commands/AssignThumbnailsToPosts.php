<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AssignThumbnailsToPosts extends Command
{
    protected $signature = 'posts:assign-thumbnails';
    protected $description = 'Assign available WebP images to posts without thumbnails';

    public function handle(): int
    {
        $this->info('Assigning thumbnails to posts...');
        $this->newLine();

        // Get all posts without thumbnails
        $postsWithoutThumbnail = Post::whereNull('thumbnail')->orderBy('id')->get();
        
        if ($postsWithoutThumbnail->isEmpty()) {
            $this->info('No posts without thumbnails found.');
            return Command::SUCCESS;
        }

        // Get all WebP files in thumbnails directory
        $allFiles = collect(Storage::disk('public')->files('posts/thumbnails'))
            ->filter(fn($file) => str_ends_with($file, '.webp'))
            ->values();

        // Get already used thumbnails
        $usedThumbnails = Post::whereNotNull('thumbnail')
            ->pluck('thumbnail')
            ->map(fn($path) => $path)
            ->toArray();

        // Get unused files
        $unusedFiles = $allFiles->reject(fn($file) => in_array($file, $usedThumbnails))->values();

        $this->info("Posts without thumbnails: {$postsWithoutThumbnail->count()}");
        $this->info("Available unused WebP files: {$unusedFiles->count()}");
        $this->newLine();

        if ($unusedFiles->isEmpty()) {
            $this->warn('No unused WebP files available.');
            return Command::FAILURE;
        }

        $assigned = 0;
        $progressBar = $this->output->createProgressBar($postsWithoutThumbnail->count());
        $progressBar->start();

        foreach ($postsWithoutThumbnail as $post) {
            if ($unusedFiles->isEmpty()) {
                break;
            }

            // Get random unused file
            $thumbnail = $unusedFiles->random();
            $unusedFiles = $unusedFiles->reject(fn($file) => $file === $thumbnail)->values();

            // Assign to post
            $post->thumbnail = $thumbnail;
            $post->saveQuietly();

            $assigned++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Successfully assigned {$assigned} thumbnails to posts.");
        
        if ($postsWithoutThumbnail->count() > $assigned) {
            $remaining = $postsWithoutThumbnail->count() - $assigned;
            $this->warn("{$remaining} posts still don't have thumbnails (not enough files available).");
        }

        return Command::SUCCESS;
    }
}


