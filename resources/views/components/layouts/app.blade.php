@use('App\Support\Localization')

@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'noindex' => false,
])

@php
    $locale = app()->getLocale();
    $locales = Localization::supportedLocales();
    $brand = __('site.brand');
    $pageTitle = $title ? "{$title} | {$brand}" : "{$brand} — ".__('site.tagline');
    $pageDescription = $description ?? __('site.meta_description');
    $canonicalUrl = url()->current();
    $shareImage = $image ?? asset('assets/logo/square%20logo.png');

    $contact = $siteContact;
    $sameAs = array_values(array_filter([$contact['facebook_url'], $contact['youtube_url']]));
    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => __('site.brand', [], 'bn'),
        'alternateName' => __('site.brand_latin'),
        'url' => Localization::route('home', locale: Localization::defaultLocale()),
        'logo' => asset('assets/logo/square%20logo.png'),
        'sameAs' => $sameAs ?: null,
        'contactPoint' => $contact['hotline'] ? [
            '@type' => 'ContactPoint',
            'telephone' => $contact['hotline'],
            'contactType' => 'customer support',
            'availableLanguage' => ['Bengali', 'English'],
        ] : null,
    ]);
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <x-theme-script />

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">
        @if ($noindex)
            <meta name="robots" content="noindex, nofollow">
        @endif
        <meta name="theme-color" content="#fffbf6">

        <link rel="canonical" href="{{ $canonicalUrl }}">
        @foreach ($locales as $code => $details)
            <link rel="alternate" hreflang="{{ $details['hreflang'] }}" href="{{ Localization::currentUrlFor($code) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ Localization::currentUrlFor(Localization::defaultLocale()) }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $brand }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:image" content="{{ $shareImage }}">
        <meta property="og:locale" content="{{ $locales[$locale]['og_locale'] }}">
        <meta name="twitter:card" content="summary_large_image">

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('assets/logo/square%20logo.png') }}">

        <link rel="preload" href="{{ asset($locale === 'bn' ? 'fonts/anek-bangla-bengali.woff2' : 'fonts/anek-bangla-latin.woff2') }}" as="font" type="font/woff2" crossorigin>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>

        {{ $head ?? '' }}
    </head>
    <body class="flex min-h-dvh flex-col pb-20 md:pb-0">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-full focus:bg-surface focus:px-4 focus:py-2 focus:shadow-card">
            {{ __('site.skip_to_content') }}
        </a>

        <x-site.header />

        <main id="main" class="flex-1">
            {{ $slot }}
        </main>

        <x-site.footer />

        <x-site.contact-dock />
    </body>
</html>
