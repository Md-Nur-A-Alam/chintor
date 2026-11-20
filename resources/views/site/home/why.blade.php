@use('App\Support\Localization')

{{--
    Why Chintor as a bento grid. The refund guarantee (risk reversal, the
    strongest reason for a hesitant parent) gets the large tile; live
    batches, the free assessment and familiar payment methods fill the rest.
--}}
@php
    $refundParams = [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ];
    $tile = 'group h-full rounded-[2rem] bg-surface p-7 shadow-card ring-1 ring-line transition duration-300 hover:-translate-y-1 hover:ring-line-strong';
    $iconTile = 'grid size-12 place-items-center rounded-2xl transition duration-300 group-hover:scale-110 group-hover:-rotate-6';
@endphp

<x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']">
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4 lg:grid-rows-2">
        <x-reveal class="md:col-span-2 lg:row-span-2">
            <div class="relative isolate flex h-full flex-col overflow-hidden rounded-[2rem] bg-gradient-to-br from-teal-700 via-teal-800 to-teal-950 p-8 text-white shadow-card sm:p-10">
                <div aria-hidden="true" class="absolute -right-20 -bottom-20 -z-10 size-72 rounded-full bg-coral-500/30 blur-3xl"></div>
                <svg aria-hidden="true" class="absolute top-8 right-8 -z-10 size-40 text-white/10" viewBox="0 0 100 100" fill="none">
                    <circle cx="50" cy="50" r="46" stroke="currentColor" stroke-width="2" stroke-dasharray="3 6" />
                    <circle cx="50" cy="50" r="30" stroke="currentColor" stroke-width="2" />
                </svg>

                <p class="inline-flex w-fit items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-sm font-semibold text-teal-50">
                    <x-icon name="shield" class="size-4" /> {{ __('site.home.bento.refund_kicker') }}
                </p>
                <p class="mt-8 text-7xl leading-none font-bold tracking-tight text-coral-300 sm:text-8xl">{{ $refundParams['percent'] }}%</p>
                <h3 class="mt-4 text-2xl font-bold sm:text-3xl">{{ __('site.home.bento.refund_title', $refundParams) }}</h3>
                <p class="mt-3 max-w-md text-teal-100">{{ __('site.home.bento.refund_body', $refundParams) }}</p>
                <div class="mt-auto pt-8">
                    <x-button :href="Localization::route('start', ['assessment' => 1])" class="group/cta">
                        {{ __('site.home.bento.cta') }}
                        <x-icon name="arrow-right" class="size-4 transition-transform group-hover/cta:translate-x-1" />
                    </x-button>
                </div>
            </div>
        </x-reveal>

        <x-reveal :index="1" class="md:col-span-2">
            <div class="{{ $tile }} flex flex-col gap-5 sm:flex-row sm:items-center">
                <span class="{{ $iconTile }} size-16 shrink-0 bg-accent-soft text-accent-ink"><x-icon name="users" class="size-8" /></span>
                <div>
                    <h3 class="text-xl font-bold text-ink">{{ __('site.home.bento.live_title') }}</h3>
                    <p class="mt-1.5 text-ink-muted">{{ __('site.home.bento.live_body') }}</p>
                </div>
            </div>
        </x-reveal>

        <x-reveal :index="2">
            <div class="{{ $tile }}">
                <span class="{{ $iconTile }} bg-brand-soft text-brand"><x-icon name="target" class="size-6" /></span>
                <h3 class="mt-5 text-lg font-bold text-ink">{{ __('site.home.bento.assessment_title') }}</h3>
                <p class="mt-1.5 text-sm text-ink-muted">{{ __('site.home.bento.assessment_body') }}</p>
            </div>
        </x-reveal>

        <x-reveal :index="3">
            <div class="{{ $tile }}">
                <span class="{{ $iconTile }} bg-sun-300/40 text-ink"><x-icon name="wallet" class="size-6" /></span>
                <h3 class="mt-5 text-lg font-bold text-ink">{{ __('site.home.bento.payment_title') }}</h3>
                <p class="mt-1.5 text-sm text-ink-muted">{{ __('site.home.bento.payment_body') }}</p>
            </div>
        </x-reveal>
    </div>
</x-site.section>
