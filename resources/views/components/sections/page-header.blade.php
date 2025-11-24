@props([
    'title',
    'description' => null,
    'gradient' => 'from-blue-50 via-emerald-50 to-teal-50',
    'actions' => null,
])

<section class="py-12 md:py-16 bg-gradient-to-br {{ $gradient }}">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">
                {{ $title }}
            </h1>
            @if($description)
            <p class="text-base md:text-lg text-gray-600 max-w-3xl mx-auto mb-6">
                {!! $description !!}
            </p>
            @endif
            @if($actions)
            <div class="mt-6">
                {{ $actions }}
            </div>
            @endif
        </div>
    </div>
</section>
