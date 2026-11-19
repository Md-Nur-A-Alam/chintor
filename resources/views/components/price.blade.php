@use('App\Support\Money')
@use('App\Support\Localization')

@php
    $payableClasses = match ($size) {
        'sm' => 'text-lg',
        'lg' => 'text-3xl sm:text-4xl',
        default => 'text-2xl',
    };
    $regularClasses = match ($size) {
        'sm' => 'text-sm',
        'lg' => 'text-lg',
        default => 'text-base',
    };
@endphp

<div {{ $attributes->class(['flex flex-wrap items-baseline gap-x-2 gap-y-1']) }} data-price>
    @if ($hasDiscount())
        <span class="sr-only">{{ __('site.price.current') }}</span>
        <span class="{{ $payableClasses }} font-bold leading-none text-accent-ink" data-price-payable>
            {{ Money::taka($payable()) }}
        </span>

        <span class="sr-only">{{ __('site.price.regular') }}</span>
        <del class="{{ $regularClasses }} font-medium text-ink-subtle opacity-70" data-price-regular>
            {{ Money::taka($regular) }}
        </del>

        @if ($showSaving && $discountPercent() > 0)
            <span class="rounded-full bg-accent-soft px-2 py-0.5 text-xs font-semibold text-accent-ink">
                {{ __('site.price.percent_off', ['percent' => Localization::digits($discountPercent())]) }}
            </span>
        @endif
    @else
        <span class="{{ $payableClasses }} font-bold leading-none text-ink" data-price-payable>
            {{ Money::taka($regular) }}
        </span>
    @endif
</div>
