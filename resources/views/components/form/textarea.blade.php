@props([
    'name',
    'label',
    'value' => null,
    'help' => null,
    'required' => false,
    'rows' => 4,
])

@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'field-'.str_replace('.', '-', $key);
@endphp

<div class="grid gap-1.5">
    <label for="{{ $id }}" class="text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)
            <span class="text-accent-ink" aria-hidden="true">*</span>
        @endif
    </label>

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        @error($key) aria-invalid="true" @enderror
        {{ $attributes->class(['w-full rounded-xl bg-surface px-3.5 py-2.5 text-ink ring-1 ring-line-strong outline-none ring-inset placeholder:text-ink-subtle focus:ring-2 focus:ring-focus']) }}
    >{{ old($key, $value) }}</textarea>

    @if ($help)
        <p class="text-xs text-ink-subtle">{{ $help }}</p>
    @endif

    @error($key)
        <p class="text-sm text-accent-ink">{{ $message }}</p>
    @enderror
</div>
