@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'id' => null,
    'band' => false,
])

{{--
    A page section with a consistent heading: a small coral "eyebrow"
    label, a large title and an optional "view all" link. band=true gives
    the section a tinted full-width background so long pages have rhythm.
--}}
<section
    @if ($id) id="{{ $id }}" @endif
    {{ $attributes->class(['scroll-mt-24', 'border-y border-line bg-surface-muted/60' => $band]) }}
>
    <div class="container-page py-14 lg:py-20">
        <div data-reveal class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                @if ($eyebrow)
                    <p class="mb-3 inline-flex items-center gap-2 text-sm font-semibold tracking-wide text-accent-ink">
                        <span aria-hidden="true" class="h-0.5 w-6 rounded-full bg-accent"></span>
                        {{ $eyebrow }}
                    </p>
                @endif
                <h2 class="text-3xl leading-tight font-bold tracking-tight text-balance text-ink sm:text-4xl lg:text-[2.75rem]">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="mt-3 text-lg text-pretty text-ink-muted">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($actionUrl)
                <a href="{{ $actionUrl }}" class="group inline-flex min-h-11 shrink-0 items-center gap-1.5 self-start rounded-full px-4 font-semibold text-accent-ink ring-1 ring-line-strong transition hover:bg-accent-soft sm:self-auto">
                    {{ $actionLabel ?? __('site.view_all') }}
                    <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                </a>
            @endif
        </div>

        {{ $slot }}
    </div>
</section>
