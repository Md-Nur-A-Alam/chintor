@use('App\Enums\PaymentMethod')

@php
    // Course → plans and batches, for the dependent selects.
    $catalog = $courses->map(fn ($course) => [
        'id' => $course->id,
        'title' => $course->translate('title', 'bn'),
        'plans' => $course->variations->map(fn ($variation) => [
            'id' => $variation->id,
            'label' => $variation->translate('label', 'bn').' — ৳'.$variation->payablePrice(),
            'price' => $variation->payablePrice(),
        ])->values(),
        'batches' => $course->openBatches->map(fn ($batch) => ['id' => $batch->id, 'label' => $batch->code.' · '.$batch->daysLabel()])->values(),
    ])->values();
    $interestedCourseId = $lead->interests->firstWhere('course_id', '!=', null)?->course_id;
@endphp

<x-layouts.admin title="লিড থেকে ভর্তি">
    <x-admin.page-header title="লিড থেকে ভর্তি" :description="$lead->displayName().' · '.$lead->phone" />

    <form
        method="POST"
        action="{{ route('admin.enrollments.store') }}"
        x-data="{
            catalog: @js($catalog),
            courseId: @js((int) old('course_id', $interestedCourseId ?? ($catalog[0]['id'] ?? null))),
            planId: @js((int) old('course_variation_id')),
            discount: {{ (int) old('discount_amount', 0) }},
            maxPercent: {{ $maxDiscountPercent }},
            get course() { return this.catalog.find(c => c.id === this.courseId) },
            get plan() { return this.course?.plans.find(p => p.id === this.planId) ?? this.course?.plans[0] },
            get maxDiscount() { return Math.floor((this.plan?.price ?? 0) * this.maxPercent / 100) },
        }"
        class="grid gap-6 lg:grid-cols-[1fr_20rem] lg:items-start"
    >
        @csrf
        <input type="hidden" name="lead_id" value="{{ $lead->id }}">

        <div class="grid gap-6">
            <x-card title="কোর্স">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="grid gap-1.5">
                        <label class="text-sm font-medium text-ink">কোর্স</label>
                        <select name="course_id" x-model.number="courseId" class="h-11 rounded-xl bg-surface px-3 ring-1 ring-line-strong ring-inset">
                            <template x-for="course in catalog" :key="course.id"><option :value="course.id" x-text="course.title" :selected="course.id === courseId"></option></template>
                        </select>
                    </div>
                    <div class="grid gap-1.5">
                        <label class="text-sm font-medium text-ink">পেমেন্ট প্ল্যান</label>
                        <select name="course_variation_id" x-model.number="planId" class="h-11 rounded-xl bg-surface px-3 ring-1 ring-line-strong ring-inset">
                            <template x-for="plan in course?.plans ?? []" :key="plan.id"><option :value="plan.id" x-text="plan.label"></option></template>
                        </select>
                        @error('course_variation_id') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label class="text-sm font-medium text-ink">ব্যাচ</label>
                        <select name="course_batch_id" class="h-11 rounded-xl bg-surface px-3 ring-1 ring-line-strong ring-inset">
                            <option value="">— পরে ঠিক হবে</option>
                            <template x-for="batch in course?.batches ?? []" :key="batch.id"><option :value="batch.id" x-text="batch.label"></option></template>
                        </select>
                    </div>
                </div>
            </x-card>

            <x-card title="শিক্ষার্থী">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.field name="student_name" label="শিক্ষার্থীর নাম" :value="$lead->student_name" required />
                    <x-form.field name="guardian_name" label="অভিভাবকের নাম" :value="$lead->guardian_name" required />
                    <x-form.field name="class_level" type="number" label="শ্রেণি" :value="$lead->class_level" />
                </div>
            </x-card>

            <x-card title="পেমেন্ট (পাওয়া গেলে)" description="TrxID দিলে পেমেন্ট যাচাইয়ের তালিকায় যাবে; যাচাই হলেই এনরোলমেন্ট সক্রিয় হবে।">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.select name="payment_method" label="মাধ্যম" :options="PaymentMethod::options()" placeholder="—" />
                    <x-form.field name="sender_number" label="প্রেরকের নম্বর" />
                    <x-form.field name="transaction_id" label="TrxID" />
                </div>
            </x-card>
        </div>

        <aside class="lg:sticky lg:top-24">
            <x-card title="মূল্য">
                <p class="text-sm text-ink-muted">প্ল্যানের মূল্য: <strong class="text-ink" x-text="'৳' + (plan?.price ?? 0)"></strong></p>
                <div class="mt-3">
                    <x-form.field name="discount_amount" type="number" label="বাড়তি ডিসকাউন্ট (৳)" x-model.number="discount" min="0" />
                    <p class="mt-1 text-xs text-ink-subtle">আপনার সীমা: সর্বোচ্চ {{ $maxDiscountPercent }}% (<span x-text="'৳' + maxDiscount"></span>)</p>
                    <p x-show="discount > maxDiscount" x-cloak class="mt-1 text-xs font-semibold text-accent-ink">সীমার বেশি ডিসকাউন্ট দেওয়া যাবে না।</p>
                </div>
                <p class="mt-4 flex items-baseline justify-between border-t border-line pt-3">
                    <span class="text-ink-muted">পরিশোধযোগ্য</span>
                    <span class="text-2xl font-bold text-accent-ink" x-text="'৳' + Math.max(0, (plan?.price ?? 0) - discount)"></span>
                </p>
                <x-button type="submit" class="mt-4 w-full">এনরোলমেন্ট তৈরি করুন</x-button>
            </x-card>
        </aside>
    </form>
</x-layouts.admin>
