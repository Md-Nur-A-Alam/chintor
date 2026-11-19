@use('App\Support\Localization')

@props([
    'book',
])

@php
    $url = Localization::route('books.show', ['book' => $book]);
@endphp

<article class="group flex flex-col">
    <a href="{{ $url }}" class="relative block aspect-[3/4] overflow-hidden rounded-2xl bg-surface-muted shadow-card ring-1 ring-line transition duration-200 group-hover:-translate-y-1">
        @if ($book->cover_url)
            <img src="{{ $book->cover_url }}" alt="{{ $book->translate('title') }}" loading="lazy" decoding="async" width="300" height="400" class="size-full object-cover">
        @else
            <div class="grid size-full place-items-center bg-gradient-to-br from-teal-700 to-teal-950 p-6 text-center">
                <span class="text-lg font-bold text-white">{{ $book->translate('title') }}</span>
            </div>
        @endif

        @unless ($book->isInStock())
            <span class="absolute top-3 left-3 rounded-full bg-surface px-2.5 py-1 text-xs font-semibold text-accent-ink">{{ __('site.book.out_of_stock') }}</span>
        @endunless
    </a>

    <div class="mt-4 flex flex-1 flex-col gap-1">
        <h3 class="font-bold text-ink"><a href="{{ $url }}" class="hover:text-accent-ink">{{ $book->translate('title') }}</a></h3>
        @if ($book->authors->isNotEmpty())
            <p class="text-sm text-ink-subtle">{{ $book->authors->map->translate('name')->join(', ') }}</p>
        @endif
        <x-price class="mt-2" :regular="$book->regular_price" :discounted="$book->discounted_price" size="sm" />
    </div>
</article>
