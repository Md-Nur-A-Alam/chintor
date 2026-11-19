@php($isEditing = $testimonial->exists)

<x-layouts.admin :title="$isEditing ? 'রিভিউ এডিট' : 'নতুন রিভিউ'">
    <x-admin.page-header :title="$isEditing ? $testimonial->author_name : 'নতুন রিভিউ'" />

    <form method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.field name="author_name" label="নাম" :value="$testimonial->author_name" required />
                    <x-form.select name="course_id" label="কোর্স" :value="$testimonial->course_id" placeholder="—" :options="$courses" />
                    <x-form.select name="rating" label="রেটিং" :value="$testimonial->rating" placeholder="—" :options="[5 => '৫', 4 => '৪', 3 => '৩', 2 => '২', 1 => '১']" />
                </div>
                <x-form.translatable name="author_role" label="পরিচয় (যেমন: অভিভাবক, ৫ম শ্রেণি)" :value="$testimonial->author_role" />
                <x-form.translatable name="body" label="রিভিউ" :value="$testimonial->body" :multiline="true" :rows="4" required />
                <x-form.field name="video_url" type="url" label="ভিডিও লিংক (ঐচ্ছিক)" :value="$testimonial->video_url" />
                <x-form.image name="photo" label="ছবি (ঐচ্ছিক)" :value="$testimonial->photo_url" />
                <div class="grid gap-3 rounded-2xl bg-surface-muted p-4">
                    <x-form.checkbox name="consent_confirmed" label="এই ব্যক্তির কাছ থেকে প্রকাশের লিখিত/মৌখিক সম্মতি নেওয়া হয়েছে" :checked="$testimonial->consent_confirmed" />
                    <x-form.checkbox name="is_published" label="ওয়েবসাইটে প্রকাশ করুন" :checked="$testimonial->is_published" />
                </div>
                <x-form.field name="sort_order" type="number" label="ক্রম" :value="$testimonial->sort_order ?? 0" />
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">সেভ করুন</x-button>
            <x-button :href="route('admin.testimonials.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>
</x-layouts.admin>
