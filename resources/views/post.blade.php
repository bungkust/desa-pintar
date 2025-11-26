@extends('layouts.app')

@push('styles')
<style>
    /* Post Content Styling */
    .prose {
        color: #374151;
        line-height: 1.75;
    }
    
    .prose h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #111827;
    }
    
    .prose h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: #1f2937;
    }
    
    .prose p {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    
    .prose ul, .prose ol {
        margin-top: 1rem;
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }
    
    .prose ul {
        list-style-type: disc;
    }
    
    .prose ol {
        list-style-type: decimal;
    }
    
    .prose li {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .prose strong {
        font-weight: 600;
        color: #111827;
    }
    
    .prose a {
        color: #2563eb;
        text-decoration: underline;
    }
    
    .prose a:hover {
        color: #1d4ed8;
    }
</style>
@endpush

@section('content')
    <!-- Page Header -->
    <x-sections.page-header 
        title="{{ $post->title }}"
        description="{{ $post->published_at ? 'Dipublikasikan pada ' . $post->published_at->locale('id')->isoFormat('dddd, D MMMM YYYY') : 'Informasi terbaru dari ' . ($settings->site_name ?? 'Desa Donoharjo') }}"
        gradient="from-blue-50 via-emerald-50 to-teal-50"
    />

    <!-- Post Content -->
    <x-sections.section>
        <article class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8 lg:p-10">

            <!-- Post Thumbnail -->
            @if($post->thumbnail)
                <div class="mb-6">
                    <img src="{{ str_starts_with($post->thumbnail, 'http://') || str_starts_with($post->thumbnail, 'https://') ? $post->thumbnail : Storage::url($post->thumbnail) }}" 
                         alt="{{ $post->title }}" 
                         width="1200"
                         height="675"
                         class="w-full rounded-lg shadow-md"
                         loading="lazy"
                         decoding="async">
                </div>
            @endif

            <!-- Post Content -->
            <div class="prose prose-lg max-w-none">
                {!! str($post->content)->sanitizeHtml() !!}
            </div>

            <!-- Back Button -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="/" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </article>
    </x-sections.section>
@endsection
