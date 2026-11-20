@use('App\Support\Localization')

@php
    $refundParams = [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ];
@endphp

<section class="relative overflow-hidden">
    <div aria-hidden="true" class="bg-aurora pointer-events-none absolute inset-0"></div>
    <div aria-hidden="true" class="bg-dots pointer-events-none absolute inset-0"></div>

    <div class="container-page relative grid gap-12 py-14 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:py-20">
        <div>
            @if ($hero['eyebrow'] !== '')
                <p class="inline-flex items-center gap-2 rounded-full bg-brand-soft px-3 py-1 text-sm font-medium text-brand ring-1 ring-brand/20 ring-inset">
                    <span class="size-2 rounded-full bg-sun-400 shadow-[0_0_10px_var(--color-sun-400)]"></span>
                    {{ $hero['eyebrow'] }}
                </p>
            @endif

            <h1 class="mt-5 text-4xl leading-tight font-bold tracking-tight text-balance text-ink sm:text-5xl lg:text-6xl">
                {{ $hero['title'] }}
            </h1>

            @if ($hero['subtitle'] !== '')
                <p class="mt-5 max-w-xl text-lg text-pretty text-ink-muted sm:text-xl">{{ $hero['subtitle'] }}</p>
            @endif

            @if ($hero['buttons'] !== [])
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    @foreach ($hero['buttons'] as $button)
                        <x-button :href="$button['url']" :variant="$button['style']" size="lg">
                            {{ $button['label'] }}
                            @if ($loop->first)
                                <x-icon name="arrow-right" class="size-5" />
                            @endif
                        </x-button>
                    @endforeach
                </div>
            @endif

            {{-- Truthful trust chips: policies, never invented numbers. --}}
            @if ($hero['show_trust_chips'])
                <ul class="mt-8 flex flex-wrap gap-2 text-sm font-semibold">
                    @foreach (['live' => 'users', 'assessment' => 'target', 'refund' => 'shield'] as $chip => $icon)
                        <li class="flex items-center gap-2 rounded-full bg-surface-raised px-3 py-2 text-ink shadow-card ring-1 ring-line">
                            <x-icon :name="$icon" class="size-4 text-accent-ink" />
                            {{ __("site.home.chips.{$chip}", $refundParams) }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="relative mx-auto w-full max-w-md lg:max-w-none">
            @if ($heroCampaigns->isNotEmpty())
                @include('site.home.partials.campaign-slider', ['campaigns' => $heroCampaigns])
            @elseif ($hero['video_url'])
                <div aria-hidden="true" class="absolute -inset-4 rounded-[3rem] bg-[conic-gradient(from_200deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] opacity-70 blur-2xl"></div>
                <x-youtube :url="$hero['video_url']" :title="$hero['title']" class="relative" />
                <x-mascot mood="wave" class="absolute -bottom-10 -left-6 h-24 w-auto text-ink drop-shadow-xl sm:h-28" />
            @else
                <div class="relative mx-auto max-w-md">
                    <div aria-hidden="true" class="absolute -inset-4 rounded-[3rem] bg-[conic-gradient(from_200deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] blur-2xl"></div>
                    <div class="relative aspect-square rounded-[2.5rem] bg-surface-raised p-10 shadow-card ring-1 ring-line">
                        <x-logo variant="stacked" class="h-full w-full" />
                    </div>
                    <x-mascot mood="wave" class="absolute -bottom-6 -left-6 h-28 w-auto text-ink drop-shadow-xl sm:h-32" />
                </div>
            @endif
        </div>
    </div>
</section>
