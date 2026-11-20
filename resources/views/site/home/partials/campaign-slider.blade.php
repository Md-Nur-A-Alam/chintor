@use('App\Support\Localization')

{{--
    Hero banner slider for running campaigns (square images, the same size
    as Facebook/Instagram posts, so marketing can reuse one design).
    Auto-advances every 6 s unless the visitor prefers reduced motion,
    pauses on hover/focus, and has labelled previous/next/dot controls.
--}}
@php($count = $campaigns->count())

<div
    x-data="{
        active: 0,
        count: {{ $count }},
        timer: null,
        start() {
            if (this.count < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            this.stop();
            this.timer = setInterval(() => this.next(), 6000);
        },
        stop() { clearInterval(this.timer); },
        next() { this.active = (this.active + 1) % this.count; },
        previous() { this.active = (this.active - 1 + this.count) % this.count; },
    }"
    x-init="start()"
    x-on:mouseenter="stop()"
    x-on:mouseleave="start()"
    x-on:focusin="stop()"
    class="relative mx-auto max-w-md"
    role="region"
    aria-roledescription="carousel"
    aria-label="{{ __('site.home.sections.campaigns.title') }}"
>
    <div aria-hidden="true" class="absolute -inset-4 rounded-[3rem] bg-[conic-gradient(from_200deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] opacity-70 blur-2xl"></div>

    <div class="relative aspect-square overflow-hidden rounded-[2rem] bg-surface-raised shadow-card ring-1 ring-line">
        @foreach ($campaigns as $index => $campaign)
            @php($link = $campaign->localizedLink())
            <div
                x-show="active === {{ $index }}"
                x-transition:enter="transition-opacity duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                @if ($index > 0) x-cloak @endif
                class="absolute inset-0"
                role="group"
                aria-roledescription="slide"
                aria-label="{{ __('site.home.campaign.slide', ['number' => Localization::digits($index + 1)]) }}"
            >
                <img
                    src="{{ $campaign->image_url }}"
                    alt="{{ $campaign->translate('title') }}"
                    width="1080"
                    height="1080"
                    decoding="async"
                    @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
                    class="size-full object-cover"
                >

                <div class="absolute inset-x-0 bottom-0 flex flex-wrap items-end justify-between gap-3 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 pt-14 text-sm text-white">
                    <div class="grid gap-1">
                        <span class="inline-flex items-center gap-1.5 font-semibold">
                            <x-icon :name="$campaign->channel->icon()" class="size-4" />
                            {{ $campaign->translate('title') }}
                        </span>
                        @if ($campaign->ends_at)
                            <x-site.countdown :ends-at="$campaign->ends_at" class="text-white/85" />
                        @endif
                    </div>

                    @if ($link)
                        <a href="{{ $link }}" @if (! str_starts_with($link, url('/'))) target="_blank" rel="noopener" @endif class="inline-flex min-h-11 items-center gap-1.5 rounded-full bg-coral-500 px-4 font-semibold text-teal-950 hover:bg-coral-400">
                            {{ $campaign->translate('button_label') ?: __('site.home.campaign.details') }}
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($count > 1)
        <div class="relative mt-4 flex items-center justify-center gap-3">
            <button type="button" x-on:click="previous()" class="grid size-11 place-items-center rounded-full bg-surface-raised text-ink shadow-card ring-1 ring-line hover:bg-surface-muted" aria-label="{{ __('site.home.campaign.previous') }}">
                <x-icon name="chevron-left" class="size-5" />
            </button>
            <div class="flex gap-1">
                @foreach ($campaigns as $index => $campaign)
                    <button
                        type="button"
                        x-on:click="active = {{ $index }}"
                        class="grid size-11 place-items-center"
                        aria-label="{{ __('site.home.campaign.slide', ['number' => Localization::digits($index + 1)]) }}"
                        x-bind:aria-current="active === {{ $index }}"
                    >
                        <span class="h-2.5 rounded-full transition-all duration-300" x-bind:class="active === {{ $index }} ? 'w-6 bg-accent' : 'w-2.5 bg-line-strong'"></span>
                    </button>
                @endforeach
            </div>
            <button type="button" x-on:click="next()" class="grid size-11 place-items-center rounded-full bg-surface-raised text-ink shadow-card ring-1 ring-line hover:bg-surface-muted" aria-label="{{ __('site.home.campaign.next') }}">
                <x-icon name="chevron-right" class="size-5" />
            </button>
        </div>
    @endif
</div>
