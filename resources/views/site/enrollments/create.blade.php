@use('App\Enums\PaymentMethod')
@use('App\Support\Localization')
@use('App\Support\Money')

@php
    $plans = $course->variations->mapWithKeys(fn ($variation) => [$variation->id => [
        'label' => $variation->translate('label'),
        'payable' => Money::taka($variation->payablePrice()),
    ]]);
    $openBatches = $course->openBatches->filter->isOpenForEnrollment();
@endphp

<x-layouts.app :title="__('site.checkout.enroll_title')" :noindex="true">
    <div class="container-page py-10">
        <a href="{{ Localization::route('courses.show', ['course' => $course]) }}" class="text-sm font-medium text-accent-ink hover:underline">← {{ $course->translate('title') }}</a>
        <h1 class="mt-3 text-3xl font-bold text-ink sm:text-4xl">{{ __('site.checkout.enroll_title') }}</h1>
        <p class="mt-1 text-sm text-ink-subtle">{{ __('site.required_hint') }}</p>

        <form
            method="POST"
            action="{{ Localization::route('enrollments.store', ['course' => $course]) }}"
            x-data="{ plans: @js($plans), plan: @js((string) old('course_variation_id', $selectedVariationId)), payLater: @js((bool) old('pay_later', false)) }"
            class="mt-8 grid gap-8 lg:grid-cols-[1fr_22rem] lg:items-start"
        >
            @csrf
            <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

            <div class="grid gap-6">
                <x-card :title="__('site.checkout.plan')">
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($course->variations as $variation)
                            <label class="flex cursor-pointer flex-col gap-1 rounded-2xl p-4 ring-1 ring-line ring-inset has-[:checked]:bg-accent-soft has-[:checked]:ring-2 has-[:checked]:ring-accent-ink/50">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="course_variation_id" value="{{ $variation->id }}" x-model="plan" class="size-4 accent-[var(--accent)]">
                                    <span class="font-semibold text-ink">{{ $variation->translate('label') }}</span>
                                </span>
                                <x-price :regular="$variation->regular_price" :discounted="$variation->discounted_price" size="sm" class="pl-6" />
                            </label>
                        @endforeach
                    </div>
                    @error('course_variation_id') <p class="mt-2 text-sm text-accent-ink">{{ $message }}</p> @enderror
                </x-card>

                @if ($openBatches->isNotEmpty())
                    <x-card :title="__('site.checkout.batch')">
                        <div class="grid gap-3">
                            @foreach ($openBatches as $batch)
                                <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl p-4 ring-1 ring-line ring-inset has-[:checked]:bg-accent-soft has-[:checked]:ring-2 has-[:checked]:ring-accent-ink/50">
                                    <span class="flex items-center gap-3">
                                        <input type="radio" name="course_batch_id" value="{{ $batch->id }}" @checked((int) old('course_batch_id', $selectedBatchId) === $batch->id) class="size-4 accent-[var(--accent)]">
                                        <span>
                                            <span class="block font-semibold text-ink">{{ $batch->translate('name') ?: $batch->code }}</span>
                                            <span class="block text-sm text-ink-muted">{{ $batch->daysLabel() }}@if ($batch->starts_on) · {{ __('site.course.batch_starts', ['date' => Localization::digits($batch->starts_on->translatedFormat('j F'))]) }}@endif</span>
                                        </span>
                                    </span>
                                    @if ($batch->seatsLeft() !== null)
                                        <x-badge tone="warning">{{ __('site.course.seats_left', ['count' => Localization::digits($batch->seatsLeft())]) }}</x-badge>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @error('course_batch_id') <p class="mt-2 text-sm text-accent-ink">{{ $message }}</p> @enderror
                    </x-card>
                @endif

                <x-card :title="__('site.lead.step_details')">
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-form.field name="student_name" :label="__('site.lead.student_name')" required />
                        <x-form.field name="class_level" type="number" :label="__('site.course.stat_level')" min="1" max="12" />
                        <x-form.field name="guardian_name" :label="__('site.lead.guardian_name')" autocomplete="name" required />
                        <x-form.field name="phone" type="tel" :label="__('site.lead.phone')" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required />
                    </div>
                </x-card>

                <x-card :title="__('site.checkout.payment')" :description="__('site.checkout.payment_steps')">
                    <label class="mb-4 flex items-center gap-2.5 text-sm font-medium text-ink">
                        <input type="checkbox" name="pay_later" value="1" x-model="payLater" class="size-4 accent-[var(--accent)]">
                        {{ __('site.checkout.pay_later') }}
                    </label>

                    <div x-show="! payLater" class="grid gap-4">
                        <x-site.payment-accounts :accounts="$paymentAccounts" />
                        <div class="grid gap-4 md:grid-cols-3">
                            <x-form.select name="payment_method" :label="__('site.checkout.payment_method')" :options="PaymentMethod::options()" :placeholder="'—'" />
                            <x-form.field name="sender_number" type="tel" :label="__('site.checkout.sender_number')" placeholder="01XXXXXXXXX" />
                            <x-form.field name="transaction_id" :label="__('site.checkout.transaction_id')" autocomplete="off" />
                        </div>
                    </div>
                </x-card>
            </div>

            <aside class="lg:sticky lg:top-28">
                <x-card :title="__('site.checkout.summary')">
                    <p class="font-semibold text-ink">{{ $course->translate('title') }}</p>
                    <p class="mt-1 text-sm text-ink-muted" x-text="plans[plan]?.label"></p>
                    <div class="mt-4 flex items-baseline justify-between border-t border-line pt-4">
                        <span class="text-ink-muted">{{ __('site.checkout.total') }}</span>
                        <span class="text-2xl font-bold text-accent-ink" x-text="plans[plan]?.payable"></span>
                    </div>

                    <label class="mt-5 flex items-start gap-2.5 text-sm text-ink-muted">
                        <input type="checkbox" name="agree_refund" value="1" required class="mt-1 size-4 shrink-0 accent-[var(--accent)]">
                        <span>{{ __('site.checkout.agree_refund') }} ({{ __('site.course.refund_line', ['percent' => Localization::digits($refundPolicy['percent']), 'window' => Localization::digits($refundPolicy['window'])]) }})</span>
                    </label>
                    @error('agree_refund') <p class="mt-1 text-sm text-accent-ink">{{ $message }}</p> @enderror

                    <x-button type="submit" size="lg" class="mt-5 w-full">{{ __('site.checkout.submit_enroll') }}</x-button>
                </x-card>
            </aside>
        </form>
    </div>
</x-layouts.app>
