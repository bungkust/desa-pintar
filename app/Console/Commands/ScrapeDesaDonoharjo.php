<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\ImageConversionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class ScrapeDesaDonoharjo extends Command
{
    protected $signature = 'scrape:desadonoharjo {--limit=50 : Limit number of posts to scrape}';
    protected $description = 'Scrape data and assets from desadonoharjo.com';

    protected ImageConversionService $conversionService;

    public function __construct(ImageConversionService $conversionService)
    {
        parent::__construct();
        $this->conversionService = $conversionService;
    }

    public function handle()
    {
        $this->info('Starting scrape from desadonoharjo.com...');
        
        $baseUrl = 'https://desadonoharjo.com';
        $limit = (int) $this->option('limit');
        
        // Scrape posts/news
        $this->scrapePosts($baseUrl, $limit);
        
        // Scrape images
        $this->scrapeImages($baseUrl);
        
        $this->info('Scraping completed!');
    }

    protected function scrapePosts($baseUrl, $limit)
    {
        $this->info('Scraping posts...');
        
        $postLinks = [];
        
        // Scrape from homepage
        $this->extractLinksFromPage($baseUrl, $postLinks);
        
        // Scrape from category/archive pages
        $categories = ['/pengumuman', '/kegiatan', '/agenda', '/berita-donoharjo'];
        foreach ($categories as $category) {
            $this->extractLinksFromPage($baseUrl . $category, $postLinks);
            // Try pagination
            for ($page = 2; $page <= 5; $page++) {
                $this->extractLinksFromPage($baseUrl . $category . '/page/' . $page, $postLinks);
            }
        }
        
        $postLinks = array_unique($postLinks);
        $this->info("Found " . count($postLinks) . " unique post links");
        
        $scraped = 0;
        $skipped = 0;
        foreach (array_slice($postLinks, 0, $limit) as $url) {
            if ($this->scrapeSinglePost($url)) {
                $scraped++;
                $this->line("✓ Scraped: " . $url);
            } else {
                $skipped++;
            }
            usleep(500000); // 0.5 second delay
        }
        
        $this->info("Successfully scraped {$scraped} posts, skipped {$skipped}");
    }
    
    protected function extractLinksFromPage($url, &$postLinks)
    {
        try {
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return;
            }
            
            $html = $response->body();
            $dom = new DOMDocument();
            @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            $xpath = new DOMXPath($dom);
            
            // Find all post/article links
            $links = $xpath->query("//article//a[@href] | //div[contains(@class, 'post')]//a[@href] | //h2//a[@href] | //h3//a[@href] | //div[contains(@class, 'entry-title')]//a[@href]");
            
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if ($href && 
                    strpos($href, 'desadonoharjo.com') !== false && 
                    !in_array($href, $postLinks) &&
                    strpos($href, '#') === false &&
                    strpos($href, 'mailto:') === false &&
                    strpos($href, 'javascript:') === false &&
                    strpos($href, '/category/') === false &&
                    strpos($href, '/author/') === false &&
                    strpos($href, '/tag/') === false &&
                    strpos($href, '/page/') === false &&
                    strpos($href, '/?hal=') === false &&
                    strpos($href, '/wp-admin') === false &&
                    strpos($href, '/wp-content') === false &&
                    strpos($href, '/wp-json') === false) {
                    $postLinks[] = $href;
                }
            }
        } catch (\Exception $e) {
            // Silently continue
        }
    }

    protected function scrapeSinglePost($url)
    {
        try {
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return false;
            }
            
            $html = $response->body();
            $dom = new DOMDocument();
            @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            $xpath = new DOMXPath($dom);
            
            // Extract title - try multiple selectors
            $title = '';
            
            // Try h1 first (most common)
            $h1Nodes = $xpath->query("//h1[not(contains(@class, 'site-title'))]");
            if ($h1Nodes->length > 0) {
                $title = trim($h1Nodes->item(0)->textContent);
            }
            
            // Try entry-title class
            if (empty($title)) {
                $entryTitleNodes = $xpath->query("//h1[contains(@class, 'entry-title')] | //h2[contains(@class, 'entry-title')]");
                if ($entryTitleNodes->length > 0) {
                    $title = trim($entryTitleNodes->item(0)->textContent);
                }
            }
            
            // Try article title
            if (empty($title)) {
                $articleTitleNodes = $xpath->query("//article//h1 | //article//h2");
                if ($articleTitleNodes->length > 0) {
                    $title = trim($articleTitleNodes->item(0)->textContent);
                }
            }
            
            // Fallback to page title (but clean it)
            if (empty($title)) {
                $titleNodes = $xpath->query("//title");
                if ($titleNodes->length > 0) {
                    $title = trim($titleNodes->item(0)->textContent);
                }
            }
            
            // Clean title
            $title = str_replace(' | Desa Donoharjo', '', $title);
            $title = str_replace(' - Desa Donoharjo', '', $title);
            $title = str_replace('Desa Donoharjo', '', $title);
            $title = trim($title, ' | -');
            $title = trim($title);
            
            // Validate title
            if (empty($title) || strlen($title) < 5) {
                return false;
            }
            
            // Skip generic titles
            $genericTitles = ['Berita Donoharjo', 'Agenda', 'Kegiatan Desa', 'Pengumuman', 'Uncategorized'];
            if (in_array($title, $genericTitles)) {
                return false;
            }
            
            // Check if post already exists
            $slug = Str::slug($title);
            if (Post::where('slug', $slug)->exists()) {
                return false;
            }
            
            // Extract content
            $contentNodes = $xpath->query("//div[contains(@class, 'entry-content')] | //div[contains(@class, 'post-content')] | //article//div[contains(@class, 'content')] | //main//div");
            $content = '';
            
            if ($contentNodes->length > 0) {
                $contentNode = $contentNodes->item(0);
                $content = $this->getInnerHtml($contentNode);
            } else {
                // Fallback: get all paragraphs
                $paragraphs = $xpath->query("//p");
                $contentParts = [];
                foreach ($paragraphs as $p) {
                    $text = trim($p->textContent);
                    if (strlen($text) > 20) {
                        $contentParts[] = '<p>' . htmlspecialchars($text) . '</p>';
                    }
                }
                $content = implode("\n", $contentParts);
            }
            
            if (empty($content) || strlen($content) < 50) {
                return false;
            }
            
            // Extract thumbnail/image
            $thumbnail = null;
            $imgNodes = $xpath->query("//img[contains(@class, 'wp-image')] | //img[contains(@class, 'attachment')] | //article//img[1] | //div[contains(@class, 'featured-image')]//img | //div[contains(@class, 'post-thumbnail')]//img");
            
            foreach ($imgNodes as $imgNode) {
                $imgSrc = $imgNode->getAttribute('src');
                if (!$imgSrc) {
                    $imgSrc = $imgNode->getAttribute('data-src'); // Lazy loaded images
                }
                
                if ($imgSrc) {
                    // Convert relative URLs to absolute
                    if (strpos($imgSrc, 'http') !== 0) {
                        if (strpos($imgSrc, '/') === 0) {
                            $parsedUrl = parse_url($url);
                            $imgSrc = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $imgSrc;
                        } else {
                            $imgSrc = dirname($url) . '/' . $imgSrc;
                        }
                    }
                    
                    $thumbnail = $this->downloadImage($imgSrc, 'posts/thumbnails');
                    if ($thumbnail) {
                        break;
                    }
                }
            }
            
            // Extract date
            $publishedAt = null;
            $dateNodes = $xpath->query("//time | //span[contains(@class, 'date')] | //div[contains(@class, 'date')]");
            foreach ($dateNodes as $dateNode) {
                $dateText = trim($dateNode->textContent);
                $dateAttr = $dateNode->getAttribute('datetime');
                
                if ($dateAttr) {
                    try {
                        $publishedAt = \Carbon\Carbon::parse($dateAttr);
                        break;
                    } catch (\Exception $e) {
                        // Continue
                    }
                } elseif (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{2,4})/', $dateText, $matches)) {
                    try {
                        $publishedAt = \Carbon\Carbon::createFromFormat('d/m/Y', $matches[0]);
                        break;
                    } catch (\Exception $e) {
                        // Continue
                    }
                }
            }
            
            if (!$publishedAt) {
                $publishedAt = now();
            }
            
            // Create post
            Post::create([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'thumbnail' => $thumbnail,
                'published_at' => $publishedAt,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            $this->warn("Error scraping {$url}: " . $e->getMessage());
            return false;
        }
    }

    protected function scrapeImages($baseUrl)
    {
        $this->info('Scraping hero images and assets...');
        
        try {
            $response = Http::timeout(30)->get($baseUrl);
            
            if (!$response->successful()) {
                return;
            }
            
            $html = $response->body();
            $dom = new DOMDocument();
            @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            $xpath = new DOMXPath($dom);
            
            // Find banner/hero images - look for large images that could be banners
            $images = $xpath->query("//img[@src]");
            $downloaded = 0;
            $downloadedUrls = [];
            
            foreach ($images as $img) {
                $src = $img->getAttribute('src') ?: $img->getAttribute('data-src');
                if (!$src) continue;
                
                $alt = strtolower($img->getAttribute('alt') ?: '');
                $class = strtolower($img->getAttribute('class') ?: '');
                
                // Convert relative URLs
                if (strpos($src, 'http') !== 0) {
                    if (strpos($src, '/') === 0) {
                        $src = $baseUrl . $src;
                    } else {
                        continue;
                    }
                }
                
                // Skip if already downloaded
                if (in_array($src, $downloadedUrls)) continue;
                
                // Look for banner/hero images or large featured images
                $isBanner = false;
                
                if (strpos($alt, 'banner') !== false || 
                    strpos($alt, 'hero') !== false || 
                    strpos($alt, 'donoharjo') !== false ||
                    strpos($alt, 'web banner') !== false ||
                    strpos($src, 'banner') !== false ||
                    strpos($src, 'hero') !== false ||
                    strpos($class, 'banner') !== false ||
                    strpos($class, 'hero') !== false ||
                    strpos($class, 'featured') !== false) {
                    $isBanner = true;
                }
                
                // Also check image dimensions if available
                $width = $img->getAttribute('width');
                $height = $img->getAttribute('height');
                if ($width && $height && (int)$width > 800 && (int)$height > 400) {
                    $isBanner = true;
                }
                
                if ($isBanner) {
                    $path = $this->downloadImage($src, 'hero-slides');
                    if ($path) {
                        $downloaded++;
                        $downloadedUrls[] = $src;
                        $this->line("Downloaded banner image: " . basename($path));
                    }
                }
            }
            
            $this->info("Downloaded {$downloaded} hero/banner images");
            
        } catch (\Exception $e) {
            $this->warn("Error scraping images: " . $e->getMessage());
        }
    }

    protected function downloadImage($url, $directory)
    {
        try {
            if (strpos($url, 'http') !== 0) {
                return null;
            }
            
            // Skip placeholder images
            if (strpos($url, 'placeholder') !== false || strpos($url, 'via.placeholder') !== false) {
                return null;
            }
            
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return null;
            }
            
            $contentType = $response->header('Content-Type');
            $extension = 'jpg';
            
            if (strpos($contentType, 'image/') === 0) {
                $extension = str_replace('image/', '', explode(';', $contentType)[0]);
            } else {
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            }
            
            // Validate extension
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($extension), $allowed)) {
                $extension = 'jpg';
            }
            
            $filename = Str::random(40) . '.' . $extension;
            $path = $directory . '/' . $filename;
            
            // Create directory if it doesn't exist
            Storage::disk('public')->makeDirectory($directory);
            
            Storage::disk('public')->put($path, $response->body());
            
            // Convert to WebP
            $webpPath = $this->conversionService->convertToWebP($path);
            
            if ($webpPath && $webpPath !== $path) {
                // Delete original file
                Storage::disk('public')->delete($path);
                return $webpPath;
            }
            
            return $path;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getInnerHtml($node)
    {
        $innerHTML = '';
        $children = $node->childNodes;
        
        foreach ($children as $child) {
            $innerHTML .= $node->ownerDocument->saveHTML($child);
        }
        
        return $innerHTML;
    }
}

