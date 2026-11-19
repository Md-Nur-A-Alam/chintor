@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $variantClasses = match ($variant) {
        'secondary' => 'bg-surface-raised text-ink ring-1 ring-line-strong ring-inset hover:bg-surface-muted',
        'brand' => 'bg-brand text-brand-ink hover:opacity-90',
        'ghost' => 'text-ink hover:bg-surface-muted',
        default => 'bg-accent text-on-accent shadow-glow hover:bg-accent-hover active:translate-y-px',
    };

    $sizeClasses = match ($size) {
        'sm' => 'h-9 px-4 text-sm',
        'lg' => 'h-13 px-7 text-lg',
        default => 'h-11 px-5 text-base',
    };

    $classes = "inline-flex cursor-pointer items-center justify-center gap-2 rounded-full font-semibold whitespace-nowrap transition duration-200 ease-out disabled:pointer-events-none disabled:opacity-50 {$variantClasses} {$sizeClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</button>
@endif
