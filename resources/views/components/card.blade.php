@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->class(['rounded-card bg-surface p-5 shadow-card ring-1 ring-line sm:p-6']) }}>
    @if ($title)
        <header class="mb-5">
            <h2 class="text-lg font-semibold text-ink">{{ $title }}</h2>
            @if ($description)
                <p class="mt-1 text-sm text-ink-muted">{{ $description }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
