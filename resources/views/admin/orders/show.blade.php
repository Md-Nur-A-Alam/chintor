@use('App\Enums\OrderStatus')
@use('App\Models\Order')
@use('App\Support\Districts')
@use('App\Support\Money')

<x-layouts.admin :title="$order->reference">
    <x-admin.page-header :title="$order->reference" :description="$order->customer_name.' · '.$order->created_at->format('d M Y, h:i A')">
        <x-slot:actions>
            <x-badge :tone="$order->status->tone()">{{ $order->status->label() }}</x-badge>
            <x-button :href="'tel:'.$order->phone" variant="brand" size="sm"><x-icon name="phone" class="size-4" /> কল</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="grid content-start gap-6">
            <x-card title="বই">
                <ul class="grid gap-2 text-sm">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-3"><span class="text-ink">{{ $item->title_snapshot }} × {{ $item->quantity }}</span><span class="font-medium">{{ Money::taka($item->line_total, 'bn') }}</span></li>
                    @endforeach
                    <li class="flex justify-between gap-3 text-ink-muted"><span>ডেলিভারি ({{ $order->deliveryZone?->translate('name', 'bn') }})</span><span>{{ Money::taka($order->delivery_charge, 'bn') }}</span></li>
                    <li class="flex justify-between gap-3 border-t border-line pt-2 font-bold"><span>মোট</span><span class="text-accent-ink">{{ Money::taka($order->total, 'bn') }}</span></li>
                </ul>
            </x-card>

            <x-card title="ডেলিভারি ঠিকানা">
                <p class="text-ink">{{ $order->customer_name }} · {{ $order->phone }}@if ($order->alt_phone) · {{ $order->alt_phone }}@endif</p>
                <p class="mt-1 text-ink-muted">{{ $order->shipping_address }}, {{ $order->upazila ? $order->upazila.', ' : '' }}{{ Districts::label($order->district) }}</p>
                @if ($order->notes)<p class="mt-2 text-sm text-ink-subtle">নির্দেশনা: {{ $order->notes }}</p>@endif
            </x-card>

            <x-card title="পেমেন্ট">
                <ul class="grid gap-3">
                    @foreach ($order->payments as $payment)
                        @include('admin.payments.partials.payment-row', ['payment' => $payment])
                    @endforeach
                </ul>
            </x-card>
        </div>

        <aside>
            <x-card title="অর্ডার আপডেট">
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="grid gap-3">
                    @csrf @method('PUT')
                    <x-form.select name="status" label="অবস্থা" :value="$order->status->value" :options="OrderStatus::options()" />
                    <x-form.select name="courier" label="কুরিয়ার" :value="$order->courier" placeholder="—" :options="Order::COURIERS" />
                    <x-form.field name="tracking_code" label="ট্র্যাকিং কোড" :value="$order->tracking_code" />
                    <x-form.textarea name="staff_notes" label="ভেতরের নোট" :value="$order->staff_notes" rows="3" />
                    <x-button type="submit" size="sm">সেভ করুন</x-button>
                </form>
                <p class="mt-3 text-xs text-ink-subtle">গ্রাহক “অর্ডার ট্র্যাক” পেজে অবস্থা ও ট্র্যাকিং কোড দেখতে পান।</p>
            </x-card>
        </aside>
    </div>
</x-layouts.admin>
