@props([
    'name',
    'label',
    'checked' => false,
    'value' => '1',
    'help' => null,
])

@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $isChecked = old() ? (bool) old($key) : (bool) $checked;
@endphp

<div>
    {{-- Unchecked boxes send nothing, so send an explicit "0" first. --}}
    <input type="hidden" name="{{ $name }}" value="0">
    <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm font-medium text-ink">
        <input
            type="checkbox"
            name="{{ $name }}"
            value="{{ $value }}"
            @checked($isChecked)
            {{ $attributes->class(['size-4 rounded accent-[var(--accent)]']) }}
        >
        {{ $label }}
    </label>

    @if ($help)
        <p class="mt-1 text-xs text-ink-subtle">{{ $help }}</p>
    @endif

    @error($key)
        <p class="mt-1 text-sm text-accent-ink">{{ $message }}</p>
    @enderror
</div>
