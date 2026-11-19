@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'help' => null,
    'required' => false,
])

@php
    // "title[bn]" → "title.bn" for old() and error lookups.
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'field-'.str_replace('.', '-', $key);
    $currentValue = $type === 'password' ? null : old($key, $value);
    $inputClasses = 'h-11 w-full rounded-xl bg-surface px-3.5 text-ink ring-1 ring-line-strong outline-none ring-inset placeholder:text-ink-subtle focus:ring-2 focus:ring-focus';
@endphp

<div class="grid gap-1.5">
    <label for="{{ $id }}" class="text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)
            <span class="text-accent-ink" aria-hidden="true">*</span>
        @endif
    </label>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if (! is_null($currentValue)) value="{{ $currentValue }}" @endif
        @if ($required) required @endif
        @error($key) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
        {{ $attributes->class([$inputClasses, 'ring-accent-ink' => $errors->has($key)]) }}
    >

    @if ($help)
        <p class="text-xs text-ink-subtle">{{ $help }}</p>
    @endif

    @error($key)
        <p id="{{ $id }}-error" class="text-sm text-accent-ink">{{ $message }}</p>
    @enderror
</div>
