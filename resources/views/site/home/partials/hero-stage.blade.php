@use('App\Support\Localization')

{{--
    The hero visual when no campaign banner or video is set: a brand-teal
    stage with the mascot and floating cards that state real policies and
    the next real batch. On a mouse, the cards drift with the pointer
    (parallax via the CSS `translate` property, so it composes with the
    float animation's `transform`). Off for touch and reduced motion.
--}}
@php
    $depthStyle = fn (int $depth): string => "translate: calc(var(--px, 0) * {$depth}px) calc(var(--py, 0) * {$depth}px)";
    $cardClasses = 'absolute flex items-center gap-3 rounded-2xl bg-surface-raised/95 p-3 pr-4 text-left shadow-card ring-1 ring-line backdrop-blur transition-[translate] duration-300 ease-out';
@endphp

<div
    x-data="{
        x: 0,
        y: 0,
        move(event) {
            if (event.pointerType !== 'mouse' || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const rect = this.$el.getBoundingClientRect();
            this.x = ((event.clientX - rect.left) / rect.width - 0.5).toFixed(3);
            this.y = ((event.clientY - rect.top) / rect.height - 0.5).toFixed(3);
        },
    }"
    x-on:pointermove="move($event)"
    x-on:pointerleave="x = 0; y = 0"
    x-bind:style="`--px: ${x}; --py: ${y}`"
    class="relative mx-auto w-full max-w-[30rem] px-4 sm:px-8"
>
    <div aria-hidden="true" class="absolute inset-6 rounded-[3rem] bg-[conic-gradient(from_200deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] blur-3xl"></div>

    {{-- Stage --}}
    <div class="relative aspect-[4/5] overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-teal-700 via-teal-800 to-teal-950 shadow-card ring-1 ring-white/10 sm:aspect-square">
        <svg aria-hidden="true" class="absolute inset-0 size-full text-white/10" viewBox="0 0 400 400" fill="none" preserveAspectRatio="xMidYMid slice">
            <circle cx="200" cy="215" r="70" stroke="currentColor" stroke-width="1.5" />
            <circle cx="200" cy="215" r="120" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 8" />
            <circle cx="200" cy="215" r="175" stroke="currentColor" stroke-width="1.5" />
            <path d="M60 70l4 10 10 4-10 4-4 10-4-10-10-4 10-4z" fill="currentColor" />
            <path d="M330 300l3 8 8 3-8 3-3 8-3-8-8-3 8-3z" fill="currentColor" />
        </svg>
        <div aria-hidden="true" class="absolute -right-16 -bottom-16 size-56 rounded-full bg-coral-500/30 blur-3xl"></div>
        <div aria-hidden="true" class="absolute -top-10 -left-10 size-44 rounded-full bg-teal-300/20 blur-3xl"></div>

        <div class="absolute inset-0 grid place-items-center" style="{{ $depthStyle(-10) }}">
            <x-mascot mood="wave" class="animate-float h-44 w-auto text-white drop-shadow-[0_20px_30px_rgb(0_0_0/0.35)] sm:h-56" />
        </div>
    </div>

    {{-- Floating cards --}}
    <div class="{{ $cardClasses }} top-8 left-0 sm:-left-2" style="{{ $depthStyle(22) }}">
        <span class="relative grid size-10 shrink-0 place-items-center rounded-xl bg-accent-soft text-accent-ink">
            <x-icon name="play" class="size-5" />
            <span class="absolute -top-0.5 -right-0.5 flex size-3">
                <span class="absolute inline-flex size-full animate-ping rounded-full bg-coral-500 opacity-70"></span>
                <span class="relative size-3 rounded-full bg-coral-500 ring-2 ring-surface-raised"></span>
            </span>
        </span>
        <span>
            <span class="block text-sm font-bold text-ink">{{ __('site.home.stage.live_title') }}</span>
            <span class="block text-xs text-ink-muted">{{ __('site.home.stage.live_body') }}</span>
        </span>
    </div>

    <div class="{{ $cardClasses }} top-[56%] right-0 hidden sm:-right-4 sm:flex" style="{{ $depthStyle(30) }}">
        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-soft text-brand"><x-icon name="target" class="size-5" /></span>
        <span>
            <span class="block text-sm font-bold text-ink">{{ __('site.home.stage.assessment_title') }}</span>
            <span class="block text-xs text-ink-muted">{{ __('site.home.stage.assessment_body') }}</span>
        </span>
    </div>

    <div class="{{ $cardClasses }} bottom-24 left-0 hidden sm:-left-6 sm:flex" style="{{ $depthStyle(18) }}">
        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-sun-300/40 text-ink"><x-icon name="shield" class="size-5" /></span>
        <span>
            <span class="block text-sm font-bold text-ink">{{ __('site.home.stage.refund_title', $refundParams) }}</span>
            <span class="block text-xs text-ink-muted">{{ __('site.home.stage.refund_body', $refundParams) }}</span>
        </span>
    </div>

    @if ($nextBatch)
        @php($seatsLeft = $nextBatch->seatsLeft())
        <a
            href="{{ Localization::route('courses.show', ['course' => $nextBatch->course]) }}#batches"
            class="{{ $cardClasses }} right-0 -bottom-4 max-w-[17rem] hover:ring-line-strong sm:right-2"
            style="{{ $depthStyle(14) }}"
        >
            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-accent text-on-accent shadow-glow"><x-icon name="calendar" class="size-5" /></span>
            <span class="min-w-0">
                <span class="block text-xs font-semibold text-accent-ink">{{ __('site.home.stage.next_batch') }} · {{ __('site.home.stage.starts', ['date' => Localization::digits($nextBatch->starts_on->translatedFormat('j F'))]) }}</span>
                <span class="block truncate text-sm font-bold text-ink">{{ $nextBatch->course->translate('title') }}</span>
                @if ($seatsLeft !== null)
                    <span class="block text-xs text-ink-muted">{{ __('site.course.seats_left', ['count' => Localization::digits($seatsLeft)]) }}</span>
                @endif
            </span>
        </a>
    @endif
</div>
