@use('App\Support\Localization')

@php
    $currentLocale = app()->getLocale();
    $otherLocales = collect(Localization::supportedLocales())->except($currentLocale);
@endphp

<nav aria-label="{{ __('site.locale.label') }}" {{ $attributes->class(['flex items-center gap-1']) }}>
    @foreach ($otherLocales as $locale => $details)
        <a
            href="{{ Localization::currentUrlFor($locale) }}"
            hreflang="{{ $details['hreflang'] }}"
            lang="{{ $locale }}"
            class="inline-flex h-9 items-center gap-1.5 rounded-full px-3 text-sm font-semibold text-ink-muted ring-1 ring-line ring-inset transition-colors hover:bg-surface-muted hover:text-ink"
            data-locale-switch="{{ $locale }}"
        >
            <x-icon name="globe" class="size-4" />
            {{ $details['name'] }}
        </a>
    @endforeach
</nav>
