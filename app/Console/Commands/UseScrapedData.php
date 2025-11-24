<?php

namespace App\Console\Commands;

use App\Models\HeroSlide;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UseScrapedData extends Command
{
    protected $signature = 'data:use-scraped {--update-hero : Update hero slides with post images}';
    protected $description = 'Use all scraped data to populate the website';

    public function handle()
    {
        $this->info('Using scraped data to populate website...');
        
        if ($this->option('update-hero')) {
            $this->updateHeroSlides();
        } else {
            $this->updateHeroSlides();
            $this->verifyPosts();
            $this->info('All scraped data is now being used!');
        }
    }

    protected function updateHeroSlides()
    {
        $this->info('Updating hero slides with images from posts...');
        
        // Get posts with thumbnails, ordered by published date (newest first)
        $postsWithImages = Post::whereNotNull('thumbnail')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();
        
        if ($postsWithImages->isEmpty()) {
            $this->warn('No posts with images found');
            return;
        }
        
        // Delete old placeholder hero slides
        HeroSlide::where('image', 'like', '%placeholder%')->delete();
        
        $created = 0;
        $order = 1;
        
        foreach ($postsWithImages as $post) {
            // Check if hero slide already exists for this post
            $existing = HeroSlide::where('title', $post->title)->first();
            
            if ($existing) {
                // Update existing
                $existing->update([
                    'subtitle' => $this->extractSubtitle($post->content),
                    'image' => $post->thumbnail,
                    'is_active' => true,
                    'order' => $order,
                ]);
                $this->line("Updated hero slide: {$post->title}");
            } else {
                // Create new
                HeroSlide::create([
                    'title' => $post->title,
                    'subtitle' => $this->extractSubtitle($post->content),
                    'image' => $post->thumbnail,
                    'is_active' => true,
                    'order' => $order,
                ]);
                $created++;
                $this->line("Created hero slide: {$post->title}");
            }
            
            $order++;
        }
        
        // Ensure first slide is a welcome slide with order 0
        $firstPost = $postsWithImages->first();
        if ($firstPost) {
            $welcomeSlide = HeroSlide::firstOrCreate(
                ['title' => 'Selamat Datang di Desa Donoharjo'],
                [
                    'subtitle' => 'Website resmi Pemerintah Kalurahan Donoharjo',
                    'image' => $firstPost->thumbnail,
                    'is_active' => true,
                    'order' => 0,
                ]
            );
            
            // Update order of other slides
            HeroSlide::where('id', '!=', $welcomeSlide->id)
                ->where('is_active', true)
                ->increment('order');
        }
        
        $this->info("Hero slides updated! Total active slides: " . HeroSlide::where('is_active', true)->count());
    }

    protected function extractSubtitle($content)
    {
        // Extract first paragraph or sentence from content
        $content = strip_tags($content);
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        $content = trim($content);
        
        // Get first sentence or first 150 characters
        $sentences = preg_split('/([.!?]+)/', $content, 2, PREG_SPLIT_DELIM_CAPTURE);
        if (count($sentences) >= 2) {
            $subtitle = $sentences[0] . $sentences[1];
            if (strlen($subtitle) > 200) {
                $subtitle = substr($subtitle, 0, 197) . '...';
            }
            return trim($subtitle);
        }
        
        // Fallback to first 150 characters
        return substr($content, 0, 150) . (strlen($content) > 150 ? '...' : '');
    }

    protected function verifyPosts()
    {
        $this->info('Verifying posts...');
        
        $total = Post::count();
        $published = Post::published()->count();
        $withThumbnails = Post::whereNotNull('thumbnail')->count();
        
        $this->line("Total posts: {$total}");
        $this->line("Published posts: {$published}");
        $this->line("Posts with thumbnails: {$withThumbnails}");
        
        // Check for posts without thumbnails
        $withoutThumbnails = Post::whereNull('thumbnail')->count();
        if ($withoutThumbnails > 0) {
            $this->warn("{$withoutThumbnails} posts without thumbnails");
        }
        
        // Check for draft posts
        $drafts = Post::whereNull('published_at')->count();
        if ($drafts > 0) {
            $this->warn("{$drafts} draft posts (not published)");
        }
    }
}

