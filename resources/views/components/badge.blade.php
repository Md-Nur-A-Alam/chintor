@props([
    'tone' => 'neutral',
])

@php
    $toneClasses = match ($tone) {
        'success' => 'bg-brand-soft text-brand ring-brand/20',
        'warning' => 'bg-sun-300/25 text-ink ring-sun-500/30',
        'danger' => 'bg-accent-soft text-accent-ink ring-accent-ink/20',
        'info' => 'bg-teal-500/10 text-brand ring-brand/15',
        default => 'bg-surface-muted text-ink-muted ring-line',
    };
@endphp

<span {{ $attributes->class(["inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap ring-1 ring-inset {$toneClasses}"]) }}>
    {{ $slot }}
</span>
