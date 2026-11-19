@php($isEditing = $faq->exists)

<x-layouts.admin :title="$isEditing ? 'FAQ এডিট' : 'নতুন FAQ'">
    <x-admin.page-header :title="$isEditing ? 'প্রশ্নোত্তর এডিট' : 'নতুন প্রশ্নোত্তর'" />

    <form method="POST" action="{{ $isEditing ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4">
                <x-form.select name="course_id" label="কোথায় দেখাবে" :value="$faq->faqable_id" placeholder="হোম পেজ (সাধারণ প্রশ্ন)" :options="$courses" />
                <x-form.translatable name="question" label="প্রশ্ন" :value="$faq->question" required />
                <x-form.translatable name="answer" label="উত্তর" :value="$faq->answer" :multiline="true" :rows="4" required />
                <div class="grid gap-4 md:grid-cols-2">
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$faq->sort_order ?? 0" />
                    <div class="self-end pb-2"><x-form.checkbox name="is_active" label="সক্রিয়" :checked="$faq->is_active" /></div>
                </div>
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">সেভ করুন</x-button>
            <x-button :href="route('admin.faqs.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>
</x-layouts.admin>
