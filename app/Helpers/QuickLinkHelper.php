<?php

namespace App\Helpers;

use App\Models\Apbdes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class QuickLinkHelper
{
    /**
     * Resolve quick link URL and determine if it should open in new tab
     * 
     * @param object $link QuickLink model instance
     * @return array [url, openInNewTab]
     */
    public static function resolveUrl($link): array
    {
        $url = trim($link->url ?? '');
        $openInNewTab = false;
        
        // Map quick link labels to routes (for backward compatibility)
        $linkRouteMap = [
            'Layanan Surat' => 'layanan-surat',
            'Produk Hukum' => 'peraturan-desa',
            'Potensi Desa' => 'potensi-desa',
            'Pengaduan' => 'complaints.index',
            'Berita' => 'berita',
            'APBDes' => 'apbdes.show',
            'Statistik' => 'statistik-lengkap',
        ];
        
        // If URL is empty or #, try to map from label
        if (empty($url) || $url === '#' || $url === '/#') {
            $routeName = $linkRouteMap[$link->label] ?? null;
            if ($routeName && Route::has($routeName)) {
                // Special handling for apbdes.show which needs year parameter
                if ($routeName === 'apbdes.show') {
                    $latestYear = Apbdes::max('year');
                    if ($latestYear) {
                        return [route($routeName, ['year' => $latestYear]), false];
                    }
                }
                return [route($routeName), false];
            }
            // Fallback to generic quick link route
            return [route('quick-link.show', ['label' => strtolower(str_replace(' ', '-', $link->label))]), false];
        }
        
        // External URL
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return [$url, true];
        }
        
        // Internal path starting with /
        if (str_starts_with($url, '/')) {
            return [$url, false];
        }
        
        // Try as route name
        if (Route::has($url)) {
            try {
                // Special handling for apbdes.show which needs year parameter
                if ($url === 'apbdes.show') {
                    $latestYear = Apbdes::max('year');
                    if ($latestYear) {
                        return [route($url, ['year' => $latestYear]), false];
                    }
                }
                return [route($url), false];
            } catch (\Exception $e) {
                // Route exists but needs parameters - use dummy page
                $routeName = $linkRouteMap[$link->label] ?? null;
                if ($routeName && Route::has($routeName)) {
                    if ($routeName === 'apbdes.show') {
                        $latestYear = Apbdes::max('year');
                        if ($latestYear) {
                            return [route($routeName, ['year' => $latestYear]), false];
                        }
                    }
                    return [route($routeName), false];
                }
                return [route('quick-link.show', ['label' => strtolower(str_replace(' ', '-', $link->label))]), false];
            }
        }
        
        // Unknown format - try to map from label or use dummy page
        $routeName = $linkRouteMap[$link->label] ?? null;
        if ($routeName && Route::has($routeName)) {
            if ($routeName === 'apbdes.show') {
                $latestYear = Apbdes::max('year');
                if ($latestYear) {
                    return [route($routeName, ['year' => $latestYear]), false];
                }
            }
            return [route($routeName), false];
        }
        return [route('quick-link.show', ['label' => strtolower(str_replace(' ', '-', $link->label))]), false];
    }
}
