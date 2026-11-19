@use('App\Enums\EnrollmentStatus')
@use('App\Enums\PaymentMethod')
@use('App\Support\Money')

<x-layouts.admin :title="$enrollment->reference">
    <x-admin.page-header :title="$enrollment->reference" :description="$enrollment->course->translate('title', 'bn').' · '.$enrollment->student->name">
        <x-slot:actions>
            <x-badge :tone="$enrollment->status->tone()">{{ $enrollment->status->label() }}</x-badge>
            @if ($enrollment->lead)
                <x-button :href="route('admin.leads.show', $enrollment->lead)" variant="secondary" size="sm">লিড দেখুন</x-button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="grid content-start gap-6">
            <x-card title="বিবরণ">
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    @foreach ([
                        'শিক্ষার্থী' => $enrollment->student->name.($enrollment->student->class_level ? " ({$enrollment->student->class_level} শ্রেণি)" : ''),
                        'অভিভাবক' => $enrollment->guardian->name.' · '.$enrollment->guardian->phone,
                        'প্ল্যান' => $enrollment->variation?->translate('label', 'bn'),
                        'ব্যাচ' => $enrollment->batch ? $enrollment->batch->code.' · '.$enrollment->batch->daysLabel() : 'এখনো ঠিক হয়নি',
                        'তালিকা মূল্য' => Money::taka($enrollment->list_price, 'bn'),
                        'ডিসকাউন্ট' => Money::taka($enrollment->discount_amount, 'bn'),
                        'পরিশোধযোগ্য' => Money::taka($enrollment->payable_amount, 'bn'),
                        'বিক্রেতা' => $enrollment->seller?->name,
                        'প্রথম ক্লাস' => $enrollment->first_class_at?->format('d M Y'),
                        'রিফান্ডের শেষ সময়' => $enrollment->refund_eligible_until ? $enrollment->refund_eligible_until->format('d M Y').' ('.($enrollment->refund_policy['percent'] ?? '—').'%)' : null,
                    ] as $label => $value)
                        <div><dt class="text-ink-subtle">{{ $label }}</dt><dd class="font-medium text-ink">{{ $value ?? '—' }}</dd></div>
                    @endforeach
                </dl>
            </x-card>

            <x-card title="পেমেন্ট">
                <ul class="grid gap-3">
                    @forelse ($enrollment->payments as $payment)
                        @include('admin.payments.partials.payment-row', ['payment' => $payment])
                    @empty
                        <li class="text-sm text-ink-subtle">এখনো কোনো পেমেন্ট আসেনি।</li>
                    @endforelse
                </ul>

                <details class="mt-4 rounded-2xl bg-surface-muted ring-1 ring-line ring-inset">
                    <summary class="cursor-pointer p-3 text-sm font-semibold text-accent-ink">+ পেমেন্ট যোগ করুন (ফোন/অফিসে পাওয়া)</summary>
                    <form method="POST" action="{{ route('admin.enrollments.payments.store', $enrollment) }}" class="grid gap-3 border-t border-line p-3 sm:grid-cols-2">
                        @csrf
                        <x-form.select name="method" label="মাধ্যম" :options="PaymentMethod::options()" />
                        <x-form.field name="amount" type="number" label="টাকা" :value="$enrollment->payable_amount" required />
                        <x-form.field name="sender_number" label="প্রেরকের নম্বর" />
                        <x-form.field name="transaction_id" label="TrxID" />
                        <div class="sm:col-span-2"><x-button type="submit" size="sm">যোগ করুন</x-button></div>
                    </form>
                </details>
            </x-card>
        </div>

        <aside>
            <x-card title="অবস্থা বদলান">
                <form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}" class="grid gap-3">
                    @csrf @method('PUT')
                    <x-form.select name="status" label="অবস্থা" :value="$enrollment->status->value" :options="EnrollmentStatus::options()" />
                    <x-form.field name="first_class_at" type="datetime-local" label="প্রথম ক্লাস" :value="$enrollment->first_class_at?->format('Y-m-d\TH:i')" />
                    <x-form.textarea name="notes" label="নোট" :value="$enrollment->notes" rows="3" />
                    <x-button type="submit" size="sm">সেভ করুন</x-button>
                </form>
            </x-card>
        </aside>
    </div>
</x-layouts.admin>
