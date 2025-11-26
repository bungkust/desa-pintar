@props(['menuItems' => collect(), 'settings' => null])

@php
    // Generate unique navbar ID to prevent conflicts
    $navbarId = 'navbar-' . uniqid();
    
    // Handle null settings with fallbacks
    $siteName = $settings?->site_name ?? 'Desa Donoharjo';
    $logoPath = $settings?->logo_path ?? null;
    
    // Helper function to check if a menu item is active
    $isActiveUrl = function($url, $currentUrl) {
        if (empty($url) || $url === '#') return false;
        $menuUrl = rtrim($url, '/');
        return $currentUrl === $menuUrl || str_starts_with($currentUrl, $menuUrl . '/');
    };
    
    $currentUrl = request()->url();
@endphp

<nav class="bg-white shadow sticky top-0 z-50" data-navbar="{{ $navbarId }}">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center flex-shrink-0">
                <a href="/" class="flex items-center group">
                    @if($logoPath)
                        <img src="{{ str_starts_with($logoPath, 'http://') || str_starts_with($logoPath, 'https://') ? $logoPath : Storage::url($logoPath) }}" 
                             alt="{{ $siteName }}" 
                             width="56"
                             height="56"
                             class="h-10 md:h-12 transition-transform duration-300 group-hover:scale-105"
                             loading="eager"
                             decoding="async">
                    @else
                        <span class="text-xl md:text-2xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors duration-300">{{ $siteName }}</span>
                    @endif
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-1">
                @if(isset($menuItems) && $menuItems->count() > 0)
                    @foreach($menuItems as $item)
                        @php
                            $itemUrl = $item->url ?? '#';
                            $hasChildren = ($item->children && $item->children->count() > 0);
                            $isActive = $isActiveUrl($itemUrl, $currentUrl);
                            
                            // Check if any child/grandchild is active
                            $hasActiveChild = false;
                            if ($hasChildren) {
                                foreach ($item->children as $child) {
                                    $childUrl = $child->url ?? '#';
                                    if ($isActiveUrl($childUrl, $currentUrl)) {
                                        $hasActiveChild = true;
                                        break;
                                    }
                                    if ($child->children && $child->children->count() > 0) {
                                        foreach ($child->children as $grandchild) {
                                            $grandchildUrl = $grandchild->url ?? '#';
                                            if ($isActiveUrl($grandchildUrl, $currentUrl)) {
                                                $hasActiveChild = true;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            }
                            $isParentActive = $isActive || $hasActiveChild;
                        @endphp
                        
                        <div class="relative group">
                            @if($hasChildren)
                                <!-- Parent menu with children - use button -->
                                <button type="button" 
                                        class="flex items-center gap-1 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 {{ $isParentActive ? 'text-emerald-600 bg-emerald-50' : 'text-gray-700 hover:text-emerald-600 hover:bg-gray-100' }}">
                                    <span>{{ $item->label }}</span>
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180 duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Submenu with invisible padding area to prevent disappearing -->
                                <div class="absolute left-0 top-full hidden group-hover:block z-50 dropdown-container">
                                    <div class="pt-2">
                                        <div class="bg-white shadow-lg rounded-lg w-56 border border-gray-200">
                                    <div class="py-1">
                                        @foreach($item->children as $child)
                                            @php
                                                $childUrl = $child->url ?? '#';
                                                $childHasChildren = ($child->children && $child->children->count() > 0);
                                                $childIsActive = $isActiveUrl($childUrl, $currentUrl);
                                                
                                                // Check grandchildren for active state
                                                $childHasActiveGrandchild = false;
                                                if ($childHasChildren) {
                                                    foreach ($child->children as $grandchild) {
                                                        $grandchildUrl = $grandchild->url ?? '#';
                                                        if ($isActiveUrl($grandchildUrl, $currentUrl)) {
                                                            $childHasActiveGrandchild = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                                $childIsParentActive = $childIsActive || $childHasActiveGrandchild;
                                            @endphp
                                            
                                            @if($childHasChildren)
                                                <!-- Child with grandchildren - nested dropdown -->
                                                <div class="relative nested-dropdown-parent">
                                                    <a href="{{ $childUrl }}" 
                                                       class="nested-dropdown-trigger block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-emerald-600 transition-colors {{ $childIsParentActive ? 'bg-emerald-50 text-emerald-600 font-semibold' : '' }}">
                                                        <span class="flex items-center justify-between">
                                                            <span>{{ $child->label }}</span>
                                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                    
                                                    <!-- Nested Submenu (Level 2) - positioned to align with parent item -->
                                                    <div class="absolute left-full top-0 z-50 nested-dropdown-container">
                                                        <div class="bg-white shadow-lg rounded-lg w-56 border border-gray-200">
                                                            @foreach($child->children as $grandchild)
                                                                @php
                                                                    $grandchildUrl = $grandchild->url ?? '#';
                                                                    $grandchildIsActive = $isActiveUrl($grandchildUrl, $currentUrl);
                                                                @endphp
                                                                <a href="{{ $grandchildUrl }}" 
                                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-emerald-600 transition-colors {{ $grandchildIsActive ? 'bg-emerald-50 text-emerald-600 font-semibold' : '' }}">
                                                                    {{ $grandchild->label }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Child without grandchildren - simple link -->
                                                <a href="{{ $childUrl }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-emerald-600 transition-colors {{ $childIsActive ? 'bg-emerald-50 text-emerald-600 font-semibold' : '' }}">
                                                    {{ $child->label }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Menu without children - use link -->
                                <a href="{{ $itemUrl }}" 
                                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 {{ $isActive ? 'text-emerald-600 bg-emerald-50' : 'text-gray-700 hover:text-emerald-600 hover:bg-gray-100' }}">
                                    {{ $item->label }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                @endif
                
                <!-- Admin Button -->
                <a href="/admin" class="ml-2 bg-emerald-600 text-white px-5 py-2 rounded-lg hover:bg-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg font-semibold text-sm whitespace-nowrap">
                    Masuk Layanan
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button type="button"
                    id="{{ $navbarId }}-mobile-button"
                    class="md:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    aria-expanded="false"
                    aria-label="Toggle mobile menu"
                    aria-controls="{{ $navbarId }}-mobile-menu">
                <!-- Hamburger Icon -->
                <svg id="{{ $navbarId }}-mobile-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <!-- Close Icon (hidden by default) -->
                <svg id="{{ $navbarId }}-mobile-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="{{ $navbarId }}-mobile-menu" 
         class="hidden md:hidden border-t border-gray-200 bg-white">
        <div class="px-4 py-2 space-y-1">
            @if(isset($menuItems) && $menuItems->count() > 0)
                @foreach($menuItems as $item)
                    @php
                        $itemUrl = $item->url ?? '#';
                        $hasChildrenMobile = ($item->children && $item->children->count() > 0);
                        $isActive = $isActiveUrl($itemUrl, $currentUrl);
                    @endphp
                    
                    @if($hasChildrenMobile)
                        <!-- Parent menu with children - use button for mobile -->
                        <div>
                            <button type="button"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-gray-100 transition-all duration-200 font-semibold {{ $isActive ? 'text-emerald-600 bg-emerald-50' : '' }}"
                                    data-mobile-toggle="{{ $navbarId }}-submenu-{{ $item->id }}"
                                    aria-expanded="false">
                                <span>{{ $item->label }}</span>
                                <svg class="w-4 h-4 transition-transform" data-icon="{{ $navbarId }}-icon-{{ $item->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Mobile Submenu -->
                            <div id="{{ $navbarId }}-submenu-{{ $item->id }}" 
                                 class="hidden pl-4 mt-1 space-y-1">
                                @foreach($item->children as $child)
                                    @php
                                        $childUrl = $child->url ?? '#';
                                        $childHasChildrenMobile = ($child->children && $child->children->count() > 0);
                                        $childIsActive = $isActiveUrl($childUrl, $currentUrl);
                                    @endphp
                                    
                                    @if($childHasChildrenMobile)
                                        <!-- Child with grandchildren -->
                                        <div>
                                            <button type="button"
                                                    class="w-full flex items-center justify-between text-gray-600 hover:text-emerald-600 transition py-1 {{ $childIsActive ? 'text-emerald-600 font-semibold' : '' }}"
                                                    data-mobile-toggle="{{ $navbarId }}-submenu-{{ $child->id }}"
                                                    aria-expanded="false">
                                                <span>{{ $child->label }}</span>
                                                <svg class="w-4 h-4 transition-transform" data-icon="{{ $navbarId }}-icon-{{ $child->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Mobile Nested Submenu -->
                                            <div id="{{ $navbarId }}-submenu-{{ $child->id }}" 
                                                 class="hidden pl-4 mt-1 space-y-1">
                                                @foreach($child->children as $grandchild)
                                                    @php
                                                        $grandchildUrl = $grandchild->url ?? '#';
                                                        $grandchildIsActive = $isActiveUrl($grandchildUrl, $currentUrl);
                                                    @endphp
                                                    <a href="{{ $grandchildUrl }}" 
                                                       class="block text-gray-500 hover:text-emerald-600 transition py-1 {{ $grandchildIsActive ? 'text-emerald-600 font-semibold' : '' }}">
                                                        {{ $grandchild->label }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <!-- Child without grandchildren - simple link -->
                                        <a href="{{ $childUrl }}" 
                                           class="block text-gray-600 hover:text-emerald-600 transition py-1 {{ $childIsActive ? 'text-emerald-600 font-semibold' : '' }}">
                                            {{ $child->label }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Menu without children - use link -->
                        <a href="{{ $itemUrl }}" 
                           class="block px-4 py-2.5 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-gray-100 transition-all duration-200 font-semibold {{ $isActive ? 'text-emerald-600 bg-emerald-50' : '' }}">
                            {{ $item->label }}
                        </a>
                    @endif
                @endforeach
            @endif
            
            <!-- Admin Button Mobile -->
            <a href="/admin" class="block bg-emerald-600 text-white px-4 py-2.5 rounded-lg hover:bg-emerald-700 transition-all duration-200 text-center font-semibold shadow-md mt-2">
                Masuk Layanan
            </a>
        </div>
    </div>
</nav>

<script>
(function() {
    'use strict';
    
    const header = document.querySelector('[data-navbar="{{ $navbarId }}"]');
    if (!header) return;
    
    const navbarId = '{{ $navbarId }}';
    const mobileButton = document.getElementById(navbarId + '-mobile-button');
    const mobileMenu = document.getElementById(navbarId + '-mobile-menu');
    const iconOpen = document.getElementById(navbarId + '-mobile-icon-open');
    const iconClose = document.getElementById(navbarId + '-mobile-icon-close');
    
    if (!mobileButton || !mobileMenu) return;
    
    // Toggle mobile menu
    function toggleMobileMenu() {
        const isHidden = mobileMenu.classList.contains('hidden');
        
        if (isHidden) {
            mobileMenu.classList.remove('hidden');
            mobileButton.setAttribute('aria-expanded', 'true');
            if (iconOpen) iconOpen.classList.add('hidden');
            if (iconClose) iconClose.classList.remove('hidden');
        } else {
            mobileMenu.classList.add('hidden');
            mobileButton.setAttribute('aria-expanded', 'false');
            if (iconOpen) iconOpen.classList.remove('hidden');
            if (iconClose) iconClose.classList.add('hidden');
            
            // Close all submenus when closing main menu
            const submenus = mobileMenu.querySelectorAll('[id^="' + navbarId + '-submenu-"]');
            submenus.forEach(submenu => {
                submenu.classList.add('hidden');
                const toggleButton = mobileMenu.querySelector('[data-mobile-toggle="' + submenu.id + '"]');
                if (toggleButton) {
                    toggleButton.setAttribute('aria-expanded', 'false');
                    const icon = toggleButton.querySelector('svg[data-icon]');
                    if (icon) icon.classList.remove('rotate-180');
                }
            });
        }
    }
    
    // Toggle submenu
    function toggleSubmenu(toggleButton) {
        const submenuId = toggleButton.getAttribute('data-mobile-toggle');
        if (!submenuId) return;
        
        const submenu = document.getElementById(submenuId);
        if (!submenu) return;
        
        const icon = toggleButton.querySelector('svg[data-icon]');
        const isHidden = submenu.classList.contains('hidden');
        const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
        
        if (isHidden || !isExpanded) {
            submenu.classList.remove('hidden');
            toggleButton.setAttribute('aria-expanded', 'true');
            if (icon) icon.classList.add('rotate-180');
        } else {
            submenu.classList.add('hidden');
            toggleButton.setAttribute('aria-expanded', 'false');
            if (icon) icon.classList.remove('rotate-180');
        }
    }
    
    // Event listeners
    mobileButton.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMobileMenu();
    });
    
    // Handle submenu toggles using event delegation
    mobileMenu.addEventListener('click', function(e) {
        const toggleButton = e.target.closest('[data-mobile-toggle]');
        if (toggleButton) {
            e.preventDefault();
            e.stopPropagation();
            toggleSubmenu(toggleButton);
        }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!header.contains(e.target) && !mobileMenu.classList.contains('hidden')) {
            toggleMobileMenu();
        }
    });
    
    // Close menu when clicking on a link
    mobileMenu.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' && !e.target.closest('[data-mobile-toggle]')) {
            setTimeout(function() {
                toggleMobileMenu();
            }, 100);
        }
    });
    
    // Close menu on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768 && !mobileMenu.classList.contains('hidden')) {
            toggleMobileMenu();
        }
    });
})();
</script>
