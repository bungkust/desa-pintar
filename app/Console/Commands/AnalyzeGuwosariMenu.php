<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class AnalyzeGuwosariMenu extends Command
{
    protected $signature = 'analyze:guwosari-menu';
    protected $description = 'Analyze menu structure from guwosari.id header';

    public function handle()
    {
        $this->info('Analyzing menu structure from guwosari.id...');
        $this->newLine();
        
        $baseUrl = 'https://guwosari.id';
        
        try {
            $response = Http::timeout(30)->get($baseUrl);
            
            if (!$response->successful()) {
                $this->error('Failed to fetch website. Status: ' . $response->status());
                return 1;
            }
            
            $html = $response->body();
            $dom = new DOMDocument();
            @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            $xpath = new DOMXPath($dom);
            
            // Find navigation/menu elements - try multiple selectors
            $menuStructures = $this->findMenuStructures($xpath);
            
            if (empty($menuStructures)) {
                $this->warn('No menu structure found. Trying alternative methods...');
                $menuStructures = $this->findMenuStructuresAlternative($xpath);
            }
            
            if (empty($menuStructures)) {
                $this->error('Could not find menu structure in the website.');
                return 1;
            }
            
            // Analyze and display menu structure
            $this->displayMenuStructure($menuStructures);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
    
    protected function findMenuStructures($xpath)
    {
        $menus = [];
        
        // Try to find main navigation - guwosari.id uses nav.main-nav with ul.navigation
        $mainNav = $xpath->query("//nav[contains(@class, 'main-nav')]//ul[contains(@class, 'navigation')] | //ul[contains(@class, 'navigation')]");
        
        if ($mainNav->length > 0) {
            $menuData = $this->extractMenuFromElement($xpath, $mainNav->item(0));
            if (!empty($menuData['level1'])) {
                $menus[] = $menuData;
                return $menus;
            }
        }
        
        // Fallback: try to find nav elements
        $headerNav = $xpath->query("//nav | //header//nav");
        
        foreach ($headerNav as $nav) {
            $menuData = $this->extractMenuFromElement($xpath, $nav);
            if (!empty($menuData['level1'])) {
                $menus[] = $menuData;
                break;
            }
        }
        
        return $menus;
    }
    
    protected function findMenuStructuresAlternative($xpath)
    {
        $menus = [];
        
        // Try to find any ul/li structure in header
        $header = $xpath->query("//header | //div[contains(@class, 'header')] | //div[contains(@id, 'header')]");
        
        foreach ($header as $headerElement) {
            // Get the first ul that looks like a menu (has multiple li children with links)
            $menuLists = $xpath->query(".//ul[count(li) > 1]", $headerElement);
            foreach ($menuLists as $menuList) {
                $menuData = $this->extractMenuFromElement($xpath, $menuList);
                if (!empty($menuData['level1']) && count($menuData['level1']) > 2) {
                    $menus[] = $menuData;
                    break 2; // Use first valid menu found
                }
            }
        }
        
        return $menus;
    }
    
    protected function extractMenuFromElement($xpath, $element)
    {
        $menuData = [
            'level1' => [],
            'level2' => [],
            'level3' => [],
        ];
        
        // Find all top-level menu items (li.navigation-item that are direct children)
        // Skip items that are just dividers or have no text
        $topLevelItems = $xpath->query("./li[contains(@class, 'navigation-item')] | ./li[.//a[@href]]", $element);
        
        $seen = [];
        foreach ($topLevelItems as $item) {
            // Skip if it's just a divider or label
            $textContent = trim($item->textContent);
            if (empty($textContent) || strlen($textContent) < 2) {
                continue;
            }
            
            $menuItem = $this->extractMenuItem($xpath, $item, 1, $seen);
            if ($menuItem && !in_array($menuItem['text'], $seen)) {
                $menuData['level1'][] = $menuItem;
                $seen[] = $menuItem['text'];
            }
        }
        
        return $menuData;
    }
    
    protected function extractMenuItem($xpath, $item, $level, &$seen = [])
    {
        // Get link text - guwosari.id uses a.navigation-link or a.navigation-dropdown-link
        $link = $xpath->query(".//a[contains(@class, 'navigation-link') or contains(@class, 'navigation-dropdown-link')][1] | .//a[@href][1]", $item)->item(0);
        if (!$link) {
            return null;
        }
        
        $text = trim($link->textContent);
        $href = $link->getAttribute('href');
        
        // Remove icon text and clean
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/\s*<[^>]+>\s*/', '', $text); // Remove any HTML tags
        $text = trim($text);
        
        // Remove common icon/chevron text
        $text = preg_replace('/\s*(chevron|icon|home|menu).*/i', '', $text);
        $text = trim($text);
        
        if (empty($text) || strlen($text) < 1) {
            return null;
        }
        
        // Skip if already seen (avoid duplicates)
        if (in_array($text, $seen)) {
            return null;
        }
        
        $menuItem = [
            'text' => $text,
            'href' => $href,
            'level' => $level,
            'children' => [],
        ];
        
        // Find submenu items - guwosari.id uses ul.navigation-dropdown for level 2
        // and ul.navigation-subtree for level 3
        $subMenuUl = $xpath->query("./ul[contains(@class, 'navigation-dropdown')] | ./ul[contains(@class, 'navigation-subtree')] | ./ul", $item);
        if ($subMenuUl->length > 0) {
            $subItems = $xpath->query("./li[contains(@class, 'navigation-dropdown-item')] | ./li[.//a[@href]]", $subMenuUl->item(0));
            $subSeen = [];
            foreach ($subItems as $subItem) {
                $subMenuItem = $this->extractMenuItem($xpath, $subItem, $level + 1, $subSeen);
                if ($subMenuItem && !in_array($subMenuItem['text'], $subSeen)) {
                    $menuItem['children'][] = $subMenuItem;
                    $subSeen[] = $subMenuItem['text'];
                }
            }
        }
        
        return $menuItem;
    }
    
    protected function displayMenuStructure($menuStructures)
    {
        if (empty($menuStructures)) {
            $this->error('No menu structure found.');
            return;
        }
        
        $menu = $menuStructures[0]; // Use first menu found
        $totalLevel1 = 0;
        $totalLevel2 = 0;
        $totalLevel3 = 0;
        
        $this->info('MENU LEVEL 1 (Menu Utama di Header):');
        $this->line(str_repeat('=', 70));
        $this->newLine();
        
        foreach ($menu['level1'] as $i => $item) {
            $totalLevel1++;
            $this->line(($i + 1) . '. ' . $item['text']);
            if (!empty($item['href'])) {
                $this->line('   URL: ' . $item['href']);
            }
            
            if (!empty($item['children'])) {
                $this->line('   └─ SUBMENU (Level 2):');
                foreach ($item['children'] as $j => $child) {
                    $totalLevel2++;
                    $this->line('      ' . ($j + 1) . '. ' . $child['text']);
                    if (!empty($child['href'])) {
                        $this->line('         URL: ' . $child['href']);
                    }
                    
                    if (!empty($child['children'])) {
                        $this->line('         └─ SUB-SUBMENU (Level 3):');
                        foreach ($child['children'] as $k => $subChild) {
                            $totalLevel3++;
                            $this->line('            ' . ($k + 1) . '. ' . $subChild['text']);
                            if (!empty($subChild['href'])) {
                                $this->line('               URL: ' . $subChild['href']);
                            }
                        }
                    }
                }
            }
            $this->newLine();
        }
        
        // Summary
        $this->newLine();
        $this->info('RINGKASAN:');
        $this->line(str_repeat('=', 70));
        $this->line('Total Menu Level 1: ' . $totalLevel1);
        $this->line('Total Submenu Level 2: ' . $totalLevel2);
        $this->line('Total Sub-submenu Level 3: ' . $totalLevel3);
        $this->line('Total Semua Menu Items: ' . ($totalLevel1 + $totalLevel2 + $totalLevel3));
    }
}

