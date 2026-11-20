@use('App\Support\Localization')

@php
    $refundParams = [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ];

    $words = $hero['rotating_words'];
    $glow = 'absolute -inset-4 rounded-[3rem] bg-[conic-gradient(from_200deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] blur-2xl';
@endphp

{{--
    Opening animation: text rises in line by line (--delay steps of 100 ms),
    the visual pops in, the ambient light drifts slowly and the mascot
    floats. CSS only, transform/opacity only, off for reduced motion.
--}}
<section class="relative overflow-hidden">
    <div aria-hidden="true" class="bg-aurora animate-aurora pointer-events-none absolute -inset-[10%]"></div>
    <div aria-hidden="true" class="bg-dots pointer-events-none absolute inset-0"></div>

    <div class="container-page relative grid gap-12 py-14 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:py-20">
        <div>
            @if ($hero['eyebrow'] !== '')
                <p class="animate-rise inline-flex items-center gap-2 rounded-full bg-brand-soft px-3 py-1 text-sm font-medium text-brand ring-1 ring-brand/20 ring-inset">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-sun-400 opacity-60"></span>
                        <span class="relative size-2 rounded-full bg-sun-400 shadow-[0_0_10px_var(--color-sun-400)]"></span>
                    </span>
                    {{ $hero['eyebrow'] }}
                </p>
            @endif

            <h1 class="animate-rise mt-5 text-4xl leading-tight font-bold tracking-tight text-balance text-ink sm:text-5xl lg:text-6xl" style="--delay: 1">
                {{ $hero['title'] }}
            </h1>

            {{-- Rotating words: the first word is real text (and the only one without JS); screen readers get the full list once. --}}
            @if ($words !== [])
                <p class="animate-rise mt-3 text-2xl font-bold text-accent-ink sm:text-3xl" style="--delay: 2">
                    <span class="sr-only">{{ implode(', ', $words) }}</span>
                    <span
                        aria-hidden="true"
                        class="relative inline-grid overflow-hidden align-bottom"
                        x-data="{
                            words: @js($words),
                            index: 0,
                            timer: null,
                            init() {
                                if (this.words.length < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                                this.timer = setInterval(() => { this.index = (this.index + 1) % this.words.length }, 2600);
                            },
                            destroy() { clearInterval(this.timer) },
                        }"
                    >
                        @foreach ($words as $wordIndex => $word)
                            <span
                                class="col-start-1 row-start-1 transition duration-500 ease-out"
                                x-bind:class="index === {{ $wordIndex }} ? 'translate-y-0 opacity-100' : ((index - 1 + words.length) % words.length === {{ $wordIndex }} ? '-translate-y-full opacity-0' : 'translate-y-full opacity-0')"
                                @if ($wordIndex > 0) x-cloak @endif
                            >{{ $word }}</span>
                        @endforeach
                    </span>
                </p>
            @endif

            @if ($hero['subtitle'] !== '')
                <p class="animate-rise mt-5 max-w-xl text-lg text-pretty text-ink-muted sm:text-xl" style="--delay: 3">{{ $hero['subtitle'] }}</p>
            @endif

            @if ($hero['buttons'] !== [])
                <div class="animate-rise mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap" style="--delay: 4">
                    @foreach ($hero['buttons'] as $button)
                        <x-button :href="$button['url']" :variant="$button['style']" size="lg" class="group">
                            {{ $button['label'] }}
                            @if ($loop->first)
                                <x-icon name="arrow-right" class="size-5 transition-transform duration-200 group-hover:translate-x-1" />
                            @endif
                        </x-button>
                    @endforeach
                </div>
            @endif

            {{-- Truthful trust chips: policies, never invented numbers. --}}
            @if ($hero['show_trust_chips'])
                <ul class="mt-8 flex flex-wrap gap-2 text-sm font-semibold">
                    @foreach (['live' => 'users', 'assessment' => 'target', 'refund' => 'shield'] as $chip => $icon)
                        <li class="animate-rise flex items-center gap-2 rounded-full bg-surface-raised px-3 py-2 text-ink shadow-card ring-1 ring-line" style="--delay: {{ 5 + $loop->index }}">
                            <x-icon :name="$icon" class="size-4 text-accent-ink" />
                            {{ __("site.home.chips.{$chip}", $refundParams) }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="animate-pop relative mx-auto w-full max-w-md lg:max-w-none" style="--delay: 2">
            @if ($heroCampaigns->isNotEmpty())
                @include('site.home.partials.campaign-slider', ['campaigns' => $heroCampaigns])
            @elseif ($hero['video_url'])
                <div aria-hidden="true" class="{{ $glow }} opacity-70"></div>
                <x-youtube :url="$hero['video_url']" :title="$hero['title']" class="relative" />
                <x-mascot mood="wave" class="animate-float absolute -bottom-10 -left-6 h-24 w-auto text-ink drop-shadow-xl sm:h-28" />
            @else
                <div class="relative mx-auto max-w-md">
                    <div aria-hidden="true" class="{{ $glow }}"></div>
                    <div class="relative aspect-square rounded-[2.5rem] bg-surface-raised p-10 shadow-card ring-1 ring-line">
                        <x-logo variant="stacked" class="h-full w-full" />
                    </div>
                    <x-mascot mood="wave" class="animate-float absolute -bottom-6 -left-6 h-28 w-auto text-ink drop-shadow-xl sm:h-32" />
                </div>
            @endif
        </div>
    </div>
</section>
