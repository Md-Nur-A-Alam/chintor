@use('App\Enums\PaymentMethod')
@use('App\Http\Requests\Site\StoreOrderRequest')
@use('App\Support\Districts')
@use('App\Support\Localization')

@php
    // District → delivery charge, for the live total (the server recalculates on submit).
    $defaultZone = $deliveryZones->firstWhere('is_default', true) ?? $deliveryZones->first();
    $chargeByDistrict = [];
    foreach ($deliveryZones as $zone) {
        foreach ($zone->districts ?? [] as $district) {
            $chargeByDistrict[$district] = $zone->charge;
        }
    }
    $maxQuantity = min(StoreOrderRequest::MAX_QUANTITY, $book->stock_quantity);
@endphp

<x-layouts.app :title="__('site.checkout.order_title')" :noindex="true">
    <div class="container-page py-10">
        <a href="{{ Localization::route('books.show', ['book' => $book]) }}" class="text-sm font-medium text-accent-ink hover:underline">← {{ $book->translate('title') }}</a>
        <h1 class="mt-3 text-3xl font-bold text-ink sm:text-4xl">{{ __('site.checkout.order_title') }}</h1>
        <p class="mt-1 text-sm text-ink-subtle">{{ __('site.required_hint') }}</p>

        <form
            method="POST"
            action="{{ Localization::route('orders.store', ['book' => $book]) }}"
            x-data="{
                unitPrice: {{ $book->payablePrice() }},
                quantity: {{ (int) old('quantity', 1) }},
                district: @js(old('district', '')),
                charges: @js($chargeByDistrict),
                defaultCharge: {{ $defaultZone?->charge ?? 0 }},
                get delivery() { return this.district === '' ? null : (this.charges[this.district] ?? this.defaultCharge) },
                taka(amount) { return '৳' + Number(amount).toLocaleString(@js(app()->getLocale() === 'bn' ? 'bn-BD' : 'en-IN')) },
            }"
            class="mt-8 grid gap-8 lg:grid-cols-[1fr_22rem] lg:items-start"
        >
            @csrf
            <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

            <div class="grid gap-6">
                <x-card :title="__('site.lead.step_details')">
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-form.field name="customer_name" :label="__('site.checkout.customer_name')" autocomplete="name" required />
                        <x-form.field name="phone" type="tel" :label="__('site.checkout.phone')" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required />
                        <x-form.field name="alt_phone" type="tel" :label="__('site.checkout.alt_phone')" inputmode="tel" />
                        <x-form.select name="district" :label="__('site.checkout.district')" :options="Districts::options()" placeholder="—" x-model="district" required />
                        <x-form.field name="upazila" :label="__('site.checkout.upazila')" />
                        <div class="md:col-span-2">
                            <x-form.textarea name="shipping_address" :label="__('site.checkout.address')" rows="2" autocomplete="street-address" required />
                        </div>
                        <div class="md:col-span-2">
                            <x-form.field name="notes" :label="__('site.checkout.notes')" />
                        </div>
                    </div>
                </x-card>

                <x-card :title="__('site.checkout.payment')" :description="__('site.checkout.payment_steps')">
                    <x-site.payment-accounts :accounts="$paymentAccounts" />
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <x-form.select name="payment_method" :label="__('site.checkout.payment_method')" :options="PaymentMethod::options()" placeholder="—" required />
                        <x-form.field name="sender_number" type="tel" :label="__('site.checkout.sender_number')" placeholder="01XXXXXXXXX" required />
                        <x-form.field name="transaction_id" :label="__('site.checkout.transaction_id')" autocomplete="off" required />
                    </div>
                </x-card>
            </div>

            <aside class="lg:sticky lg:top-28">
                <x-card :title="__('site.checkout.summary')">
                    <div class="flex gap-4">
                        @if ($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="" class="h-24 w-18 rounded-lg object-cover ring-1 ring-line">
                        @endif
                        <div>
                            <p class="font-semibold text-ink">{{ $book->translate('title') }}</p>
                            <x-price :regular="$book->regular_price" :discounted="$book->discounted_price" size="sm" class="mt-1" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-form.field name="quantity" type="number" :label="__('site.checkout.quantity')" min="1" :max="$maxQuantity" x-model.number="quantity" :value="1" required />
                    </div>

                    <dl class="mt-4 grid gap-2 border-t border-line pt-4 text-sm">
                        <div class="flex justify-between"><dt class="text-ink-muted">{{ __('site.checkout.subtotal') }}</dt><dd class="font-medium text-ink" x-text="taka(unitPrice * quantity)"></dd></div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-ink-muted">{{ __('site.checkout.delivery') }}</dt>
                            <dd class="text-right font-medium text-ink" x-text="delivery === null ? @js(__('site.checkout.delivery_by_district')) : taka(delivery)"></dd>
                        </div>
                        <div class="flex items-baseline justify-between border-t border-line pt-3">
                            <dt class="text-ink-muted">{{ __('site.checkout.total') }}</dt>
                            <dd class="text-2xl font-bold text-accent-ink" x-text="taka(unitPrice * quantity + (delivery ?? 0))"></dd>
                        </div>
                    </dl>

                    <x-button type="submit" size="lg" class="mt-5 w-full">{{ __('site.checkout.submit_order') }}</x-button>
                </x-card>
            </aside>
        </form>
    </div>
</x-layouts.app>
