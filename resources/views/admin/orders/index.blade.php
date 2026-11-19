@use('App\Enums\OrderStatus')
@use('App\Support\Districts')
@use('App\Support\Money')

<x-layouts.admin title="বইয়ের অর্ডার">
    <x-admin.page-header title="বইয়ের অর্ডার" description="পেমেন্ট যাচাই → প্যাক → কুরিয়ার → ডেলিভারি।" />

    <form method="GET" class="mb-4 grid max-w-2xl gap-3 sm:grid-cols-[1fr_14rem_auto] sm:items-end">
        <x-form.field name="search" label="খুঁজুন" :value="$search" placeholder="অর্ডার নম্বর, ফোন বা নাম" />
        <x-form.select name="status" label="অবস্থা" :value="$status" placeholder="সব" :options="OrderStatus::options()" />
        <x-button type="submit" variant="brand" size="sm">ফিল্টার</x-button>
    </form>

    <x-admin.table :paginator="$orders">
        <x-slot:head>
            <tr><th class="px-4 py-3">অর্ডার</th><th class="px-4 py-3">গ্রাহক</th><th class="px-4 py-3">বই</th><th class="px-4 py-3">মোট</th><th class="px-4 py-3">অবস্থা</th></tr>
        </x-slot:head>
        @forelse ($orders as $order)
            <tr class="hover:bg-surface-muted/50">
                <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-semibold text-ink hover:text-accent-ink">{{ $order->reference }}</a><p class="text-xs text-ink-subtle">{{ $order->created_at->format('d M, h:i A') }}</p></td>
                <td class="px-4 py-3"><p class="font-medium text-ink">{{ $order->customer_name }}</p><p class="text-xs text-ink-subtle">{{ $order->phone }} · {{ Districts::label($order->district) }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ $order->items->map(fn ($item) => $item->title_snapshot.' ×'.$item->quantity)->join(', ') }}</td>
                <td class="px-4 py-3 font-medium">{{ Money::taka($order->total, 'bn') }}</td>
                <td class="px-4 py-3"><x-badge :tone="$order->status->tone()">{{ $order->status->label() }}</x-badge></td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">কোনো অর্ডার নেই।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
