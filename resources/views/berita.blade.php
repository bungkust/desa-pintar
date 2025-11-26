@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <x-layouts.page-layout
        title="Berita & Informasi"
        description="Berita terkini dan informasi terbaru dari {{ $settings->site_name ?? 'Desa Donoharjo' }}"
        page-header-gradient="from-blue-50 via-emerald-50 to-teal-50">
    
    <x-sections.section spacing="py-12 md:py-16 lg:py-20">
        @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-6 lg:gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                @if($post->thumbnail)
                <a href="{{ route('post.show', $post->slug) }}">
                    <img src="{{ str_starts_with($post->thumbnail, 'http://') || str_starts_with($post->thumbnail, 'https://') ? $post->thumbnail : Storage::url($post->thumbnail) }}" 
                         alt="{{ $post->title }}" 
                         class="w-full aspect-video object-cover"
                         width="800"
                         height="450"
                         loading="lazy"
                         decoding="async">
                </a>
                @else
                <div class="w-full aspect-video bg-gray-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                @endif
                <div class="p-4 md:p-6">
                    <time class="text-sm text-gray-500 mb-2 block">
                        {{ $post->published_at ? $post->published_at->locale('id')->isoFormat('D MMMM YYYY') : '' }}
                    </time>
                    <h3 class="text-lg md:text-xl lg:text-2xl font-bold mb-2 text-gray-900 line-clamp-2">
                        <a href="{{ route('post.show', $post->slug) }}" class="hover:text-emerald-600 transition">{{ $post->title }}</a>
                    </h3>
                    <p class="text-sm md:text-base text-gray-600 line-clamp-3">
                        {{ Str::limit(strip_tags($post->content), 150) }}
                    </p>
                    <a href="{{ route('post.show', $post->slug) }}" class="inline-flex items-center mt-4 text-emerald-600 hover:text-emerald-700 font-semibold transition">
                        Baca selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 md:mt-12">
            {{ $posts->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            <p class="text-gray-600 text-lg">Belum ada berita yang dipublikasikan.</p>
        </div>
        @endif
    </x-sections.section>
    </x-layouts.page-layout>
@endsection

