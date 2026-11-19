@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
])

{{-- Compact hero for inner pages, with the same ambient brand light as the home page. --}}
<section class="relative overflow-hidden border-b border-line">
    <div aria-hidden="true" class="bg-aurora pointer-events-none absolute inset-0"></div>
    <div aria-hidden="true" class="bg-dots pointer-events-none absolute inset-0"></div>

    <div class="container-page relative py-12 lg:py-16">
        @if ($eyebrow)
            <p class="mb-3 text-sm font-semibold text-accent-ink">{{ $eyebrow }}</p>
        @endif
        <h1 class="max-w-3xl text-4xl font-bold tracking-tight text-ink sm:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-lg text-ink-muted">{{ $subtitle }}</p>
        @endif

        {{ $slot }}
    </div>
</section>
