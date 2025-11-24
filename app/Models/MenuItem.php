<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'label',
        'url',
        'type',
        'route_name',
        'route_params',
        'order',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'parent_id' => 'integer',
        'route_params' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->autoCorrectType();
        });
    }

    // Relationship: Parent menu
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    // Relationship: Child menus (submenus)
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    // Get all descendants (recursive)
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    // Check if has children
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    // Get hierarchy level (0 = Menu Utama, 1 = Submenu, 2 = Sub-submenu)
    public function getLevel(): int
    {
        $level = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    // Get level label
    public function getLevelLabel(): string
    {
        return match ($this->getLevel()) {
            0 => 'Menu Utama',
            1 => 'Submenu',
            2 => 'Sub-submenu',
            default => 'Level ' . ($this->getLevel() + 1),
        };
    }

    // Get breadcrumb path (e.g., "Home > About > Team")
    public function getBreadcrumbPath(): string
    {
        $path = [];
        $item = $this;
        
        while ($item) {
            array_unshift($path, $item->label);
            $item = $item->parent;
        }
        
        return implode(' › ', $path);
    }

    // Check if this is a top-level menu (Menu Utama)
    public function isTopLevel(): bool
    {
        return $this->parent_id === null;
    }

    // Get all ancestors
    public function getAncestors(): array
    {
        $ancestors = [];
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors[] = $parent;
            $parent = $parent->parent;
        }
        
        return array_reverse($ancestors);
    }

    // Get count of children (submenus)
    public function submenuCount(): int
    {
        return $this->children()->count();
    }

    // Check if item violates hierarchy rules (orphan detection)
    public function isOrphan(): bool
    {
        $level = $this->getLevel();
        
        // Level > 2 is invalid
        if ($level > 2) {
            return true;
        }
        
        // Level 0 must have null parent
        if ($level === 0 && $this->parent_id !== null) {
            return true;
        }
        
        // Level 1 must have level 0 parent
        if ($level === 1) {
            if (!$this->parent || $this->parent->getLevel() !== 0) {
                return true;
            }
        }
        
        // Level 2 must have level 1 parent
        if ($level === 2) {
            if (!$this->parent || $this->parent->getLevel() !== 1) {
                return true;
            }
        }
        
        return false;
    }

    // Get Tailwind padding class based on level
    public function indentation(): string
    {
        $level = $this->getLevel();
        
        return match ($level) {
            0 => 'pl-0',
            1 => 'pl-5',
            2 => 'pl-10',
            default => 'pl-0',
        };
    }

    // Get badge configuration array (label, color)
    public function levelBadge(): array
    {
        $level = $this->getLevel();
        
        return match ($level) {
            0 => [
                'label' => 'Menu Utama',
                'color' => 'primary',
            ],
            1 => [
                'label' => 'Submenu',
                'color' => 'success',
            ],
            2 => [
                'label' => 'Sub-submenu',
                'color' => 'warning',
            ],
            default => [
                'label' => 'Level ' . ($level + 1),
                'color' => 'gray',
            ],
        };
    }

    // Check if URL is an anchor link
    public function isAnchor(): bool
    {
        if (empty($this->url)) {
            return false;
        }
        
        return str_starts_with($this->url, '#') || str_contains($this->url, '/#');
    }

    // Check if URL is a route (internal post)
    public function isRoute(): bool
    {
        return !empty($this->route_name) || $this->isInternalPost();
    }

    // Check if URL is external (starts with http)
    public function isExternalUrl(): bool
    {
        if (empty($this->url)) {
            return false;
        }
        
        return str_starts_with($this->url, 'http://') || str_starts_with($this->url, 'https://');
    }

    // Check if URL matches internal post pattern (/posts/{slug})
    public function isInternalPost(): bool
    {
        if (empty($this->url)) {
            return false;
        }
        
        // Pattern: /posts/{slug} or /posts/{slug}/... or /posts/{slug}-{id}
        return (bool) preg_match('/^\/posts\/([^\/?#]+)/', $this->url);
    }

    // Extract slug from internal post URL
    public function extractSlug(): ?string
    {
        if (!$this->isInternalPost()) {
            return null;
        }
        
        // Pattern: /posts/{slug} or /posts/{slug}/... or /posts/{slug}-{id}
        if (preg_match('/^\/posts\/([^\/?#]+)/', $this->url, $matches)) {
            $slug = $matches[1];
            // Handle slug with id suffix like /posts/slug-123
            // Remove numeric suffix if exists at the end (e.g., "slug-123" -> "slug")
            if (preg_match('/^(.+)-(\d+)$/', $slug, $slugMatches)) {
                return $slugMatches[1];
            }
            return $slug;
        }
        
        return null;
    }

    // Auto-correct type based on URL
    public function autoCorrectType(): void
    {
        // Parent with children should be dropdown (highest priority)
        if ($this->hasChildren() && $this->type !== 'dropdown') {
            $this->type = 'dropdown';
            $this->url = '#';
            $this->route_name = null;
            $this->route_params = null;
            return; // Skip other corrections for dropdown
        }

        // Dropdown must have URL = "#"
        if ($this->type === 'dropdown') {
            $this->url = '#';
            $this->route_name = null;
            $this->route_params = null;
            return; // Skip other corrections
        }

        // Auto-fix internal /posts/{slug} (should be route)
        if ($this->isInternalPost() && $this->type !== 'route') {
            $this->type = 'route';
            $slug = $this->extractSlug();
            if ($slug) {
                $this->route_name = 'post.show';
                $this->route_params = ['slug' => $slug];
                $this->url = null; // Clear URL when using route
            }
            return; // Skip other corrections
        }

        // Auto-fix anchor (only if URL starts with # and not route)
        if ($this->url && $this->isAnchor() && $this->type !== 'anchor' && !$this->isInternalPost()) {
            $this->type = 'anchor';
        }

        // Auto-fix external URL
        if ($this->url && $this->isExternalUrl() && $this->type !== 'url') {
            $this->type = 'url';
        }
    }
}
