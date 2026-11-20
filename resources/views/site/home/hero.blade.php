@use('App\Support\Localization')

@php
    $refundParams = [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ];

    $words = $hero['rotating_words'];
    $showsStage = $heroCampaigns->isEmpty() && ! $hero['video_url'];
    $glow = 'absolute -inset-4 rounded-[3rem] bg-[conic-gradient(from_200deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] blur-2xl';
@endphp

{{--
    Opening animation: text rises in line by line (--delay steps of 100 ms),
    the visual pops in, the ambient light drifts slowly. CSS only,
    transform/opacity only, off for reduced motion.

    Right side, in order of priority: running campaign banners → explainer
    video → the brand stage (mascot + floating policy cards).
--}}
<section class="relative overflow-hidden">
    <div aria-hidden="true" class="bg-aurora animate-aurora pointer-events-none absolute -inset-[10%]"></div>
    <div aria-hidden="true" class="bg-dots pointer-events-none absolute inset-0"></div>

    <div class="container-page relative grid items-center gap-14 py-14 lg:grid-cols-[1.05fr_0.95fr] lg:gap-10 lg:py-24">
        <div class="text-center lg:text-left">
            @if ($hero['eyebrow'] !== '')
                <p class="animate-rise inline-flex items-center gap-2 rounded-full bg-surface-raised px-3.5 py-1.5 text-sm font-semibold text-ink shadow-card ring-1 ring-line">
                    <span class="relative flex size-2.5">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-coral-500 opacity-60"></span>
                        <span class="relative size-2.5 rounded-full bg-coral-500"></span>
                    </span>
                    {{ $hero['eyebrow'] }}
                </p>
            @endif

            <h1 class="animate-rise mt-6 text-[2.5rem] leading-[1.15] font-bold tracking-tight text-balance text-ink sm:text-6xl lg:text-[4.25rem]" style="--delay: 1">
                {{ $hero['title'] }}
            </h1>

            {{-- Rotating words: the first word is real text (and the only one without JS); screen readers get the full list once. --}}
            @if ($words !== [])
                <p class="animate-rise mt-3 text-2xl font-bold sm:text-4xl" style="--delay: 2">
                    <span class="sr-only">{{ implode(', ', $words) }}</span>
                    <span
                        aria-hidden="true"
                        class="relative inline-grid overflow-hidden pb-2 align-bottom text-accent-ink"
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
                        <svg aria-hidden="true" class="absolute inset-x-0 bottom-0 h-2.5 w-full text-accent" viewBox="0 0 200 10" preserveAspectRatio="none" fill="none">
                            <path d="M2 7c40-5 90-6 196-2" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    </span>
                </p>
            @endif

            @if ($hero['subtitle'] !== '')
                <p class="animate-rise mx-auto mt-6 max-w-xl text-lg text-pretty text-ink-muted sm:text-xl lg:mx-0" style="--delay: 3">{{ $hero['subtitle'] }}</p>
            @endif

            @if ($hero['buttons'] !== [])
                <div class="animate-rise mt-9 flex flex-col justify-center gap-3 sm:flex-row sm:flex-wrap lg:justify-start" style="--delay: 4">
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

            {{-- Policy chips; the brand stage already shows them as floating cards. --}}
            @if ($hero['show_trust_chips'] && ! $showsStage)
                <ul class="mt-8 flex flex-wrap justify-center gap-2 text-sm font-semibold lg:justify-start">
                    @foreach (['live' => 'users', 'assessment' => 'target', 'refund' => 'shield'] as $chip => $icon)
                        <li class="animate-rise flex items-center gap-2 rounded-full bg-surface-raised px-3 py-2 text-ink shadow-card ring-1 ring-line" style="--delay: {{ 5 + $loop->index }}">
                            <x-icon :name="$icon" class="size-4 text-accent-ink" />
                            {{ __("site.home.chips.{$chip}", $refundParams) }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="animate-pop relative w-full" style="--delay: 2">
            @if ($heroCampaigns->isNotEmpty())
                @include('site.home.partials.campaign-slider', ['campaigns' => $heroCampaigns])
            @elseif ($hero['video_url'])
                <div class="relative mx-auto max-w-xl">
                    <div aria-hidden="true" class="{{ $glow }} opacity-70"></div>
                    <x-youtube :url="$hero['video_url']" :title="$hero['title']" class="relative" />
                    <x-mascot mood="wave" class="animate-float absolute -bottom-10 -left-6 h-24 w-auto text-ink drop-shadow-xl sm:h-28" />
                </div>
            @else
                @include('site.home.partials.hero-stage')
            @endif
        </div>
    </div>
</section>
