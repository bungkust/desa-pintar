@php
    $siteName = ($settings->site_name ?? null) ?: 'Desa Donoharjo';
    $siteUrl = url('/');
    $logoUrl = ($settings && $settings->logo_path) ? Storage::url($settings->logo_path) : asset('favicon.ico');
@endphp

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "{{ $siteName }}",
    "url": "{{ $siteUrl }}",
    "logo": "{{ $logoUrl }}",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ ($settings && $settings->village_address) ? $settings->village_address : '' }}",
        "addressCountry": "ID"
    },
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ ($settings && $settings->whatsapp) ? $settings->whatsapp : '' }}",
        "contactType": "customer service",
        "availableLanguage": ["Indonesian"]
    },
    "sameAs": [
        @php
            $sameAs = [];
            if ($settings && $settings->instagram) {
                $sameAs[] = $settings->instagram;
            }
            if ($settings && $settings->whatsapp) {
                $sameAs[] = 'https://wa.me/' . str_replace(['+', ' ', '-'], '', $settings->whatsapp);
            }
        @endphp
        @foreach($sameAs as $url)
        "{{ $url }}"@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ $siteName }}",
    "url": "{{ $siteUrl }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": {
            "@type": "EntryPoint",
            "urlTemplate": "{{ $siteUrl }}/search?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>

@if(isset($post) && $post)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $post->title }}",
    "description": "{{ Str::limit(strip_tags($post->content), 160) }}",
    "image": "{{ $post->thumbnail ? (str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : Storage::url($post->thumbnail)) : $logoUrl }}",
    "datePublished": "{{ $post->published_at?->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "author": {
        "@type": "Organization",
        "name": "{{ $siteName }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ $siteName }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ $logoUrl }}"
        }
    }
}
</script>
@endif

