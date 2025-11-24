@props([
    'title' => null,
    'subtitle' => null,
    'background' => 'bg-white',
    'spacing' => 'py-12 md:py-16',
    'id' => null,
])

<section @if($id) id="{{ $id }}" @endif class="{{ $spacing }} {{ $background }}">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        @if($title)
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-8 text-gray-900">
            {{ $title }}
        </h2>
        @endif
        @if($subtitle)
        <p class="text-base md:text-lg text-gray-600 mb-6 max-w-3xl">
            {{ $subtitle }}
        </p>
        @endif
        
        {{ $slot }}
    </div>
</section>
