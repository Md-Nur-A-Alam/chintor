@use('App\Support\Localization')

@php
    $samplePages = $book->sample_pages ?? [];
    $contents = $book->translate('table_of_contents') ?? [];
@endphp

<x-layouts.app :title="$book->translate('seo_title') ?: $book->translate('title')" :description="$book->translate('seo_description') ?: $book->translate('description')" :image="$book->cover_url">
    <div class="container-page grid gap-10 py-10 lg:grid-cols-[22rem_1fr] lg:py-14">
        <div>
            <div class="overflow-hidden rounded-card bg-surface-muted shadow-card ring-1 ring-line">
                @if ($book->cover_url)
                    <img src="{{ $book->cover_url }}" alt="{{ $book->translate('title') }}" class="w-full object-cover" width="600" height="800">
                @else
                    <div class="grid aspect-[3/4] place-items-center bg-gradient-to-br from-teal-700 to-teal-950 p-8 text-center text-2xl font-bold text-white">{{ $book->translate('title') }}</div>
                @endif
            </div>

            @if ($samplePages !== [])
                <div class="mt-6">
                    <h2 class="font-semibold text-ink">{{ __('site.book.look_inside') }}</h2>
                    <div class="mt-3 grid grid-cols-3 gap-2">
                        @foreach ($samplePages as $pageUrl)
                            <a href="{{ $pageUrl }}" target="_blank" rel="noopener" class="block aspect-[3/4] overflow-hidden rounded-xl ring-1 ring-line">
                                <img src="{{ $pageUrl }}" alt="" loading="lazy" class="size-full object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div>
            @if ($book->program)
                <a href="{{ Localization::route('programs.show', ['program' => $book->program]) }}" class="text-sm font-semibold text-accent-ink hover:underline">{{ $book->program->translate('name') }}</a>
            @endif
            <h1 class="mt-2 text-4xl font-bold tracking-tight text-ink">{{ $book->translate('title') }}</h1>
            @if ($book->translate('subtitle'))
                <p class="mt-2 text-xl text-ink-muted">{{ $book->translate('subtitle') }}</p>
            @endif
            @if ($book->authors->isNotEmpty())
                <p class="mt-3 text-ink-muted">{{ __('site.book.by', ['authors' => $book->authors->map->translate('name')->join(', ')]) }}</p>
            @endif

            <div class="mt-6 flex flex-wrap items-center gap-4">
                <x-price :regular="$book->regular_price" :discounted="$book->discounted_price" size="lg" />
                <x-badge :tone="$book->isInStock() ? 'success' : 'danger'">{{ $book->isInStock() ? __('site.book.in_stock') : __('site.book.out_of_stock') }}</x-badge>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                @if ($book->isInStock())
                    <x-button :href="Localization::route('orders.create', ['book' => $book])" size="lg">{{ __('site.cta.order_now') }} <x-icon name="arrow-right" class="size-5" /></x-button>
                @endif
            </div>

            <p class="mt-4 flex items-start gap-2 text-sm text-ink-muted"><x-icon name="truck" class="mt-0.5 size-4 text-brand" />{{ __('site.book.delivery_note') }}</p>

            <ul class="mt-8 flex flex-wrap gap-2 text-sm">
                @if ($book->pages)
                    <li class="rounded-full bg-surface px-3 py-1.5 text-ink-muted ring-1 ring-line">{{ __('site.book.pages', ['count' => Localization::digits($book->pages)]) }}</li>
                @endif
                @if ($book->edition)
                    <li class="rounded-full bg-surface px-3 py-1.5 text-ink-muted ring-1 ring-line">{{ $book->edition }}</li>
                @endif
                @if ($book->isbn)
                    <li class="rounded-full bg-surface px-3 py-1.5 text-ink-muted ring-1 ring-line">ISBN {{ $book->isbn }}</li>
                @endif
            </ul>

            @if ($book->translate('description'))
                <section class="mt-10">
                    <h2 class="text-2xl font-bold text-ink">{{ __('site.book.about') }}</h2>
                    <div class="mt-3 leading-relaxed text-ink-muted">{!! nl2br(e($book->translate('description'))) !!}</div>
                </section>
            @endif

            @if ($contents !== [])
                <section class="mt-10">
                    <h2 class="text-2xl font-bold text-ink">{{ __('site.book.contents') }}</h2>
                    <ol class="mt-4 grid gap-2">
                        @foreach ($contents as $index => $chapter)
                            <li class="flex gap-3 rounded-xl bg-surface px-4 py-3 ring-1 ring-line">
                                <span class="font-bold text-accent-ink">{{ Localization::digits($index + 1) }}.</span>
                                <span class="text-ink">{{ $chapter }}</span>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
