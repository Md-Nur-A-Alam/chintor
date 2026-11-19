@use('App\Enums\PaymentStatus')
@use('App\Support\Money')

{{-- One payment with verify / reject controls (shown to staff with payments.verify). --}}
<li class="rounded-2xl p-4 ring-1 ring-line ring-inset" x-data="{ rejecting: false }">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="font-semibold text-ink">{{ Money::taka($payment->amount, 'bn') }} · {{ $payment->method->label() }}</p>
            <p class="text-xs text-ink-subtle">
                {{ $payment->reference }}
                @if ($payment->transaction_id) · TrxID <span class="font-mono font-semibold text-ink">{{ $payment->transaction_id }}</span>@endif
                @if ($payment->sender_number) · {{ $payment->sender_number }}@endif
                · {{ $payment->created_at->format('d M, h:i A') }}
            </p>
            @if ($payment->status === PaymentStatus::Rejected && $payment->rejection_reason)
                <p class="mt-1 text-xs text-accent-ink">কারণ: {{ $payment->rejection_reason }}</p>
            @endif
            @if ($payment->verifier)
                <p class="mt-1 text-xs text-ink-subtle">{{ $payment->verifier->name }} · {{ $payment->verified_at?->format('d M, h:i A') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <x-badge :tone="$payment->status->tone()">{{ $payment->status->label() }}</x-badge>
            @if ($payment->status === PaymentStatus::Pending)
                @can('payments.verify')
                    <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" onsubmit="return confirm('স্টেটমেন্টে এই টাকা পেয়েছেন নিশ্চিত?')">
                        @csrf
                        <x-button type="submit" variant="brand" size="sm"><x-icon name="check" class="size-4" /> যাচাই</x-button>
                    </form>
                    <x-button type="button" variant="ghost" size="sm" class="text-accent-ink" x-on:click="rejecting = ! rejecting">বাতিল</x-button>
                @endcan
            @endif
        </div>
    </div>

    @can('payments.verify')
        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" x-show="rejecting" x-cloak class="mt-3 flex gap-2">
            @csrf
            <input type="text" name="rejection_reason" required placeholder="কেন বাতিল (যেমন: TrxID মেলেনি)" class="h-9 flex-1 rounded-lg bg-surface px-3 text-sm ring-1 ring-line-strong ring-inset">
            <x-button type="submit" size="sm">বাতিল নিশ্চিত</x-button>
        </form>
    @endcan
</li>
