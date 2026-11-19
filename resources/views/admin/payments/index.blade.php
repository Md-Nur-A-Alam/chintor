@use('App\Enums\PaymentStatus')
@use('App\Models\Enrollment')
@use('App\Models\Order')

<x-layouts.admin title="পেমেন্ট যাচাই">
    <x-admin.page-header title="পেমেন্ট যাচাই" description="মার্চেন্ট অ্যাপ/স্টেটমেন্টের সাথে TrxID ও টাকা মিলিয়ে যাচাই করুন। যাচাই হলে অর্ডার কনফার্ম ও এনরোলমেন্ট সক্রিয় হয়।" />

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (PaymentStatus::cases() as $case)
            <a href="{{ route('admin.payments.index', ['status' => $case->value]) }}" @class([
                'rounded-full px-4 py-1.5 text-sm font-medium ring-1 ring-line ring-inset',
                'bg-accent text-on-accent' => $status === $case->value,
                'text-ink-muted hover:bg-surface-muted' => $status !== $case->value,
            ])>{{ $case->label() }}</a>
        @endforeach
    </div>

    <ul class="grid gap-3">
        @forelse ($payments as $payment)
            <li class="rounded-card bg-surface shadow-card ring-1 ring-line">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-2 text-sm">
                    @if ($payment->payable instanceof Order)
                        <a href="{{ route('admin.orders.show', $payment->payable) }}" class="font-semibold text-ink hover:text-accent-ink">বইয়ের অর্ডার {{ $payment->payable->reference }} · {{ $payment->payable->customer_name }}</a>
                    @elseif ($payment->payable instanceof Enrollment)
                        <a href="{{ route('admin.enrollments.show', $payment->payable) }}" class="font-semibold text-ink hover:text-accent-ink">এনরোলমেন্ট {{ $payment->payable->reference }}</a>
                    @endif
                    <span class="text-ink-subtle">{{ $payment->submitted_by === 'staff' ? 'স্টাফ যোগ করেছেন' : 'গ্রাহক জমা দিয়েছেন' }}</span>
                </div>
                <ul class="p-2">@include('admin.payments.partials.payment-row', ['payment' => $payment])</ul>
            </li>
        @empty
            <li class="rounded-card bg-surface p-10 text-center text-ink-subtle ring-1 ring-line">এই তালিকায় কোনো পেমেন্ট নেই।</li>
        @endforelse
    </ul>
    <div class="mt-6">{{ $payments->links() }}</div>
</x-layouts.admin>
