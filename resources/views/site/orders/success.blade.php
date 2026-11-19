@use('App\Support\Localization')
@use('App\Support\Money')

<x-layouts.app :title="__('site.checkout.success_order_title')" :noindex="true">
    <div class="container-page grid place-items-center py-16 text-center">
        <x-mascot mood="wave" class="h-32 w-auto text-ink" />
        <h1 class="mt-6 text-3xl font-bold text-ink sm:text-4xl">{{ __('site.checkout.success_order_title') }}</h1>
        <p class="mt-3 max-w-xl text-lg text-ink-muted">{{ __('site.checkout.success_paid') }}</p>

        <div class="mt-8 w-full max-w-md rounded-card bg-surface p-6 text-left shadow-card ring-1 ring-line">
            <p class="text-sm text-ink-subtle">{{ __('site.checkout.save_reference') }}</p>
            <p class="mt-1 font-mono text-2xl font-bold tracking-wide text-ink">{{ $order->reference }}</p>

            <ul class="mt-5 grid gap-2 border-t border-line pt-4 text-sm">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4"><span class="text-ink">{{ $item->title_snapshot }} × {{ Localization::digits($item->quantity) }}</span><span class="font-medium">{{ Money::taka($item->line_total) }}</span></li>
                @endforeach
                <li class="flex justify-between gap-4"><span class="text-ink-subtle">{{ __('site.checkout.delivery') }}</span><span>{{ Money::taka($order->delivery_charge) }}</span></li>
                <li class="flex justify-between gap-4 border-t border-line pt-2"><span class="text-ink-subtle">{{ __('site.checkout.total') }}</span><span class="font-bold text-accent-ink">{{ Money::taka($order->total) }}</span></li>
            </ul>

            <x-button :href="Localization::route('orders.track', ['reference' => $order->reference])" variant="secondary" class="mt-5 w-full">{{ __('site.checkout.track_link') }}</x-button>
        </div>
    </div>
</x-layouts.app>
