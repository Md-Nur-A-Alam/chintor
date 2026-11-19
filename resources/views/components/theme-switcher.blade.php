@props([
    'withLabels' => false,
])

@php
    $options = [
        'light' => 'sun',
        'dark' => 'moon',
        'system' => 'monitor',
    ];
@endphp

<div
    x-data="themeSwitcher"
    role="group"
    aria-label="{{ __('site.theme.label') }}"
    {{ $attributes->class(['inline-flex items-center gap-0.5 rounded-full bg-surface-muted p-1 ring-1 ring-line ring-inset']) }}
>
    @foreach ($options as $preference => $icon)
        <button
            type="button"
            x-on:click="choose('{{ $preference }}')"
            x-bind:aria-pressed="preference === '{{ $preference }}'"
            x-bind:class="preference === '{{ $preference }}' ? 'bg-surface-raised text-ink shadow-sm ring-1 ring-line' : 'text-ink-subtle hover:text-ink'"
            title="{{ __("site.theme.{$preference}") }}"
            @class([
                'inline-flex cursor-pointer items-center gap-1.5 rounded-full text-sm font-medium transition-colors',
                'h-8 px-3' => $withLabels,
                'size-8 justify-center' => ! $withLabels,
            ])
        >
            <x-icon :name="$icon" class="size-4" />
            <span @class(['sr-only' => ! $withLabels])>{{ __("site.theme.{$preference}") }}</span>
        </button>
    @endforeach
</div>
