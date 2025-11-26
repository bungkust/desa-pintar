@props([
    'title',
    'meta' => null,
    'image' => null,
    'imageAlt' => null,
    'backUrl' => null,
    'backText' => 'Kembali',
])

@push('styles')
<style>
    /* Article Layout - Medium-style reading experience */
    .article-layout-container {
        display: grid;
        grid-template-columns: 1fr minmax(680px, 740px) 1fr;
        width: 100%;
        background: #fff;
        min-height: 100vh;
    }
    
    .article-layout-container > * {
        grid-column: 2;
    }
    
    /* Typography - Medium style */
    .article-layout-content {
        font-family: 'Charter', 'Georgia', 'Times New Roman', serif;
        font-size: 21px;
        line-height: 1.58;
        letter-spacing: -0.003em;
        color: rgba(0, 0, 0, 0.84);
    }
    
    /* Title - Large and bold */
    .article-layout-title {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        font-size: 42px;
        font-weight: 700;
        line-height: 1.04;
        letter-spacing: -0.015em;
        color: rgba(0, 0, 0, 0.84);
        margin-bottom: 0.5em;
        margin-top: 0;
        padding-top: 32px;
    }
    
    /* Meta information */
    .article-layout-meta {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        font-size: 14px;
        color: rgba(0, 0, 0, 0.54);
        margin-bottom: 29px;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    /* Paragraph spacing */
    .article-layout-content p {
        margin-bottom: 1.58em;
    }
    
    /* Headings */
    .article-layout-content h2 {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        font-size: 34px;
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: -0.015em;
        color: rgba(0, 0, 0, 0.84);
        margin-top: 56px;
        margin-bottom: 0.5em;
    }
    
    .article-layout-content h3 {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.22;
        letter-spacing: -0.012em;
        color: rgba(0, 0, 0, 0.84);
        margin-top: 39px;
        margin-bottom: 0.5em;
    }
    
    .article-layout-content h4 {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        font-size: 22px;
        font-weight: 600;
        line-height: 1.25;
        color: rgba(0, 0, 0, 0.84);
        margin-top: 32px;
        margin-bottom: 0.5em;
    }
    
    /* Blockquotes - Medium style */
    .article-layout-content blockquote {
        border-left: 4px solid rgba(0, 0, 0, 0.84);
        padding-left: 20px;
        margin: 29px 0;
        font-style: italic;
        color: rgba(0, 0, 0, 0.68);
        font-size: 1em;
    }
    
    /* Lists */
    .article-layout-content ul, .article-layout-content ol {
        margin: 29px 0;
        padding-left: 30px;
    }
    
    .article-layout-content li {
        margin-bottom: 0.5em;
    }
    
    /* Links */
    .article-layout-content a {
        color: rgba(0, 0, 0, 0.84);
        text-decoration: underline;
        text-decoration-color: rgba(0, 0, 0, 0.2);
        transition: text-decoration-color 0.2s;
    }
    
    .article-layout-content a:hover {
        text-decoration-color: rgba(0, 0, 0, 0.68);
    }
    
    /* Images - follow content width */
    .article-layout-image {
        margin-top: 0;
        margin-bottom: 43px;
    }
    
    .article-layout-image img {
        width: 100%;
        max-width: 100%;
        height: auto;
        display: block;
        border-radius: 4px;
    }
    
    /* Images inside content */
    .article-layout-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 29px 0;
    }
    
    .article-layout-content figure {
        margin: 29px 0;
    }
    
    .article-layout-content figure img {
        width: 100%;
        margin: 0;
    }
    
    /* Strong/Bold text */
    .article-layout-content strong {
        font-weight: 600;
        color: rgba(0, 0, 0, 0.84);
    }
    
    /* Code blocks */
    .article-layout-content pre {
        background: #f7f7f7;
        padding: 20px;
        border-radius: 4px;
        overflow-x: auto;
        margin: 29px 0;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.9em;
        line-height: 1.5;
    }
    
    .article-layout-content code {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.9em;
        background: rgba(0, 0, 0, 0.05);
        padding: 2px 6px;
        border-radius: 3px;
    }
    
    .article-layout-content pre code {
        background: transparent;
        padding: 0;
    }
    
    /* Tables */
    .article-layout-content table {
        width: 100%;
        margin: 29px 0;
        border-collapse: collapse;
    }
    
    .article-layout-content table th,
    .article-layout-content table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .article-layout-content table th {
        font-weight: 600;
        color: rgba(0, 0, 0, 0.84);
    }
    
    /* HR */
    .article-layout-content hr {
        border: none;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        margin: 43px 0;
    }
    
    /* Back Button */
    .article-layout-back {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        padding-top: 32px;
        margin-top: 56px;
        margin-bottom: 64px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .article-layout-container {
            grid-template-columns: 1fr;
            padding: 0 20px;
        }
        
        .article-layout-container > * {
            grid-column: 1;
        }
        
        .article-layout-title {
            font-size: 32px;
            line-height: 1.125;
        }
        
        .article-layout-content {
            font-size: 18px;
            line-height: 1.6;
        }
        
        .article-layout-content h2 {
            font-size: 28px;
            margin-top: 39px;
        }
        
        .article-layout-content h3 {
            font-size: 24px;
            margin-top: 32px;
        }
        
        .article-layout-content h4 {
            font-size: 20px;
            margin-top: 24px;
        }
    }
    
    @media (max-width: 480px) {
        .article-layout-container {
            padding: 0 16px;
        }
        
        .article-layout-title {
            font-size: 28px;
        }
    }
</style>
@endpush

<div class="article-layout-container pt-40 md:pt-48 pb-32 md:pb-40">
    <!-- Title -->
    @if(isset($title))
        <h1 class="article-layout-title">{{ $title }}</h1>
    @endif
    
    <!-- Meta Information -->
    @if(isset($meta))
        <div class="article-layout-meta">
            {{ $meta }}
        </div>
    @endif
    
    <!-- Featured Image -->
    @if(isset($image))
        <figure class="article-layout-image">
            <img src="{{ $image }}" 
                 alt="{{ $imageAlt ?? $title ?? '' }}" 
                 loading="lazy"
                 decoding="async">
        </figure>
    @endif
    
    <!-- Article Content -->
    <div class="article-layout-content">
        {{ $slot }}
    </div>
    
    <!-- Back Button -->
    @if(isset($backUrl))
        <div class="article-layout-back">
            <a href="{{ $backUrl }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ $backText }}
            </a>
        </div>
    @endif
</div>

