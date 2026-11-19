@use('App\Support\Money')

@php
    $hasPayment = $enrollment->payments->isNotEmpty();
@endphp

<x-layouts.app :title="__('site.checkout.success_enroll_title')" :noindex="true">
    <div class="container-page grid place-items-center py-16 text-center">
        <x-mascot mood="wave" class="h-32 w-auto text-ink" />
        <h1 class="mt-6 text-3xl font-bold text-ink sm:text-4xl">{{ __('site.checkout.success_enroll_title') }}</h1>
        <p class="mt-3 max-w-xl text-lg text-ink-muted">{{ $hasPayment ? __('site.checkout.success_paid') : __('site.checkout.success_unpaid') }}</p>

        <div class="mt-8 w-full max-w-md rounded-card bg-surface p-6 text-left shadow-card ring-1 ring-line">
            <p class="text-sm text-ink-subtle">{{ __('site.checkout.save_reference') }}</p>
            <p class="mt-1 font-mono text-2xl font-bold tracking-wide text-ink">{{ $enrollment->reference }}</p>
            <dl class="mt-5 grid gap-2 border-t border-line pt-4 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-ink-subtle">{{ __('site.nav.courses') }}</dt><dd class="text-right font-medium text-ink">{{ $enrollment->course->translate('title') }}</dd></div>
                @if ($enrollment->variation)
                    <div class="flex justify-between gap-4"><dt class="text-ink-subtle">{{ __('site.checkout.plan') }}</dt><dd class="font-medium text-ink">{{ $enrollment->variation->translate('label') }}</dd></div>
                @endif
                @if ($enrollment->batch)
                    <div class="flex justify-between gap-4"><dt class="text-ink-subtle">{{ __('site.checkout.batch') }}</dt><dd class="font-medium text-ink">{{ $enrollment->batch->translate('name') ?: $enrollment->batch->code }}</dd></div>
                @endif
                <div class="flex justify-between gap-4"><dt class="text-ink-subtle">{{ __('site.checkout.total') }}</dt><dd class="font-bold text-accent-ink">{{ Money::taka($enrollment->payable_amount) }}</dd></div>
            </dl>
        </div>
    </div>
</x-layouts.app>
