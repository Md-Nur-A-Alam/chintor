@use('App\Enums\OrderStatus')
@use('App\Models\Order')
@use('App\Support\Localization')
@use('App\Support\Money')

@php
    // Customer-facing progress steps (cancelled/returned are shown as a badge instead).
    $steps = [OrderStatus::PendingVerification, OrderStatus::Confirmed, OrderStatus::Packed, OrderStatus::Shipped, OrderStatus::Delivered];
    $currentIndex = $order ? array_search($order->status, $steps, true) : false;
@endphp

<x-layouts.app :title="__('site.track.title')" :noindex="true">
    <x-site.page-hero :title="__('site.track.title')" :subtitle="__('site.track.subtitle')">
        <form method="GET" class="mt-6 grid max-w-2xl gap-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            <x-form.field name="reference" :label="__('site.track.reference')" :value="$reference" placeholder="CH-O-2611-0001" required />
            <x-form.field name="phone" type="tel" :label="__('site.track.phone')" :value="request('phone')" placeholder="01XXXXXXXXX" required />
            <x-button type="submit" variant="brand">{{ __('site.track.submit') }}</x-button>
        </form>
    </x-site.page-hero>

    <div class="container-page py-10">
        @if ($searched && ! $order)
            <p class="rounded-2xl bg-accent-soft px-5 py-4 text-accent-ink">{{ __('site.track.not_found') }}</p>
        @elseif ($order)
            <div class="max-w-3xl rounded-card bg-surface p-6 shadow-card ring-1 ring-line">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="font-mono text-xl font-bold text-ink">{{ $order->reference }}</p>
                    <x-badge :tone="$order->status->tone()">{{ $order->status->label() }}</x-badge>
                </div>

                @if ($currentIndex !== false)
                    <ol class="mt-6 grid grid-cols-5 gap-2 text-center text-xs">
                        @foreach ($steps as $index => $step)
                            <li class="grid justify-items-center gap-2">
                                <span @class([
                                    'grid size-9 place-items-center rounded-full font-bold',
                                    'bg-accent text-on-accent' => $index <= $currentIndex,
                                    'bg-surface-muted text-ink-subtle' => $index > $currentIndex,
                                ])>{{ Localization::digits($index + 1) }}</span>
                                <span @class(['text-ink font-semibold' => $index === $currentIndex, 'text-ink-subtle' => $index !== $currentIndex])>{{ $step->label() }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif

                @if ($order->tracking_code)
                    <p class="mt-6 rounded-xl bg-brand-soft px-4 py-3 text-sm text-brand">
                        {{ __('site.track.courier', ['courier' => Order::COURIERS[$order->courier] ?? $order->courier, 'code' => $order->tracking_code]) }}
                    </p>
                @endif

                <ul class="mt-6 grid gap-2 border-t border-line pt-4 text-sm">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between"><span>{{ $item->title_snapshot }} × {{ Localization::digits($item->quantity) }}</span><span>{{ Money::taka($item->line_total) }}</span></li>
                    @endforeach
                    <li class="flex justify-between font-bold"><span>{{ __('site.checkout.total') }}</span><span class="text-accent-ink">{{ Money::taka($order->total) }}</span></li>
                </ul>
            </div>
        @endif
    </div>
</x-layouts.app>
