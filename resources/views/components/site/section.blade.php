@props([
    'title',
    'subtitle' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'id' => null,
])

{{-- A page section with a consistent heading row. --}}
<section @if ($id) id="{{ $id }}" @endif {{ $attributes->class(['container-page scroll-mt-24 py-12 lg:py-16']) }}>
    <div data-reveal class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-ink sm:text-4xl">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-2 text-lg text-ink-muted">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($actionUrl)
            <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 font-semibold text-accent-ink hover:underline">
                {{ $actionLabel ?? __('site.view_all') }} <x-icon name="arrow-right" class="size-4" />
            </a>
        @endif
    </div>

    {{ $slot }}
</section>
