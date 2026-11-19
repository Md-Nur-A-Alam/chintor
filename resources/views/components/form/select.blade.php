@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'help' => null,
    'required' => false,
])

@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'field-'.str_replace('.', '-', $key);
    $selected = (string) old($key, $value);
@endphp

<div class="grid gap-1.5">
    <label for="{{ $id }}" class="text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)
            <span class="text-accent-ink" aria-hidden="true">*</span>
        @endif
    </label>

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->class(['h-11 w-full rounded-xl bg-surface px-3 text-ink ring-1 ring-line-strong outline-none ring-inset focus:ring-2 focus:ring-focus']) }}
    >
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @if ($help)
        <p class="text-xs text-ink-subtle">{{ $help }}</p>
    @endif

    @error($key)
        <p class="text-sm text-accent-ink">{{ $message }}</p>
    @enderror
</div>
