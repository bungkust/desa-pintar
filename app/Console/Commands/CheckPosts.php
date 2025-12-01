<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = \App\Models\Post::all();
        $now = now();

        $this->info("Total posts: " . $posts->count());
        $this->info("Current time: " . $now->format('Y-m-d H:i:s'));
        $this->line('');

        foreach ($posts as $post) {
            $status = $post->published_at ? 'published' : 'draft';
            $publishedDate = $post->published_at ? $post->published_at->format('Y-m-d H:i:s') : 'null';
            $isVisible = $post->published_at && $post->published_at <= $now ? 'visible' : 'not visible';
            $this->line("ID: {$post->id} | Title: {$post->title} | Status: {$status} | Published: {$publishedDate} | {$isVisible}");
        }

        $this->line('');
        $publishedPosts = \App\Models\Post::published()->count();
        $this->info("Posts that should be visible: " . $publishedPosts);

        // Test the cache
        $cachedPosts = \Illuminate\Support\Facades\Cache::get('all_posts');
        if ($cachedPosts) {
            $this->info("Cached posts count: " . $cachedPosts->count());
        } else {
            $this->info("No cached posts found");
        }

        // Fix the test post published date
        $testPost = \App\Models\Post::find(8);
        if ($testPost && $testPost->published_at->isFuture()) {
            $testPost->published_at = now()->subMinutes(5);
            $testPost->save();
            $this->info("✅ Fixed 'test' post published date to show immediately");
        }

        // Clear cache
        \Illuminate\Support\Facades\Cache::forget('all_posts');

        // Test the BeritaController directly
        $this->info("Testing BeritaController...");
        $controller = app(\App\Http\Controllers\BeritaController::class);
        $response = $controller->index();
        $viewData = $response->getData();
        $this->info("Posts in view data: " . $viewData['posts']->count());
        foreach ($viewData['posts'] as $post) {
            $this->line("  - " . $post->title);
        }

        // Test the scope directly
        $this->info("Testing published() scope directly...");
        $publishedPosts = \App\Models\Post::published()->get();
        $this->info("Published posts from scope: " . $publishedPosts->count());
        foreach ($publishedPosts as $post) {
            $this->line("  - " . $post->title . " (published: " . $post->published_at->format('Y-m-d H:i:s') . ")");
        }

        // Check database directly
        $this->info("Raw database check...");
        $rawPosts = \Illuminate\Support\Facades\DB::table('posts')->whereNotNull('published_at')->get();
        $this->info("Posts with published_at not null: " . $rawPosts->count());
        foreach ($rawPosts as $post) {
            $publishedAt = \Carbon\Carbon::parse($post->published_at);
            $isVisible = $publishedAt <= now();
            $this->line("  - {$post->title} | DB published_at: {$post->published_at} | Visible: " . ($isVisible ? 'YES' : 'NO'));
        }
    }
}
