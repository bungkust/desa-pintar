@props([
    // Page Header
    'title' => null,
    'description' => null,
    'showPageHeader' => true,
    'pageHeaderGradient' => 'from-blue-50 via-emerald-50 to-teal-50',
    'pageHeaderActions' => null, // Slot untuk buttons di header
    
    // Toolbar/Navigator (untuk year selector, filter, dll)
    'showToolbar' => false,
    'toolbarContent' => null, // Slot untuk year selector, filter, dll
    'toolbarNegativeMargin' => true, // untuk -mt-8 overlap dengan page header
    
    // Back Button (optional, bisa juga di slot)
    'showBackButton' => false,
    'backUrl' => null,
    'backText' => 'Kembali',
    
    // Sticky CTA (mobile)
    'showStickyCTA' => false,
    'stickyCTAContent' => null, // Slot untuk sticky button
    
    // Bottom Actions (untuk tombol kembali di bagian bawah konten)
    'showBottomBackButton' => false,
    'bottomBackUrl' => null,
    'bottomBackText' => 'Kembali',
    'bottomBackVariant' => 'outline',
    'bottomActions' => null, // Slot untuk custom actions di bagian bawah
])

<!-- Page Header Section -->
@if($showPageHeader && ($title || $description))
    <x-sections.page-header 
        :title="$title"
        :description="$description"
        :gradient="$pageHeaderGradient">
        @if($pageHeaderActions)
            <x-slot name="actions">
                {{ $pageHeaderActions }}
            </x-slot>
        @endif
    </x-sections.page-header>
@endif

<!-- Toolbar/Navigator (Year Selector, Filter, dll) -->
@if($showToolbar && $toolbarContent)
    <section class="py-0 {{ $toolbarNegativeMargin ? '-mt-8' : '' }}">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            {{ $toolbarContent }}
        </div>
    </section>
@endif

<!-- Back Button (Optional) -->
@if($showBackButton && $backUrl)
    <x-sections.section spacing="py-4 md:py-6">
        <div class="max-w-5xl mx-auto">
            @include('components.buttons.back-button', [
                'href' => $backUrl,
                'label' => $backText,
            ])
        </div>
    </x-sections.section>
@endif

<!-- Main Content Sections -->
<div class="max-w-5xl mx-auto">
    {{ $slot }}
    
    <!-- Bottom Actions (Back Button di bagian bawah) -->
    @if($showBottomBackButton && $bottomBackUrl)
        <div class="flex items-center justify-start pt-6 border-t mt-6">
            @include('components.buttons.back-button', [
                'href' => $bottomBackUrl,
                'label' => $bottomBackText,
                'variant' => $bottomBackVariant,
            ])
        </div>
    @endif
    
    @if($bottomActions)
        <div class="flex items-center justify-start pt-6 border-t mt-6">
            {{ $bottomActions }}
        </div>
    @endif
</div>

<!-- Sticky CTA (Mobile) -->
@if($showStickyCTA && $stickyCTAContent)
    <div class="fixed bottom-4 right-4 z-50 md:hidden">
        {{ $stickyCTAContent }}
    </div>
@endif

