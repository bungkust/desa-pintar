@props([
    'title' => null,
    'subtitle' => null,
    'background' => 'bg-white',
    'spacing' => 'py-12 md:py-16',
    'id' => null,
])

<section @if($id) id="{{ $id }}" @endif class="{{ $spacing }} {{ $background }}">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        @if($title || $subtitle)
        <div class="text-center mb-8 md:mb-10">
        @if($title)
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">
            {{ $title }}
        </h2>
        @endif
        @if($subtitle)
            <p class="text-lg md:text-xl text-gray-600 text-center leading-relaxed">
            {{ $subtitle }}
        </p>
            @endif
        </div>
        @endif
        
        {{ $slot }}
    </div>
</section>
