@php
    /*
     * Hreflang tags for multilingual SEO.
     *
     * Currently the platform serves all languages from the same URL (cookie/DB-based
     * switching). When locale-prefixed URLs (/{locale}/…) are introduced, update this
     * partial to generate language-specific URLs per route.
     */
    $canonical      = \Artesaos\SEOTools\Facades\SEOMeta::getCanonical() ?: url()->current();
    $hreflangLocales = config('seo.hreflang_locales', ['en' => 'en', 'bn' => 'bn']);
@endphp

{{-- x-default points to the canonical URL --}}
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

{{-- Per-locale alternates (same URL, language served via cookie/preference) --}}
@foreach($hreflangLocales as $locale => $hreflangCode)
    <link rel="alternate" hreflang="{{ $hreflangCode }}" href="{{ $canonical }}">
@endforeach
