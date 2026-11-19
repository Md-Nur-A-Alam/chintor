@use('App\Enums\PublishStatus')

@php
    $isEditing = $program->exists;
    $icons = ['layers' => 'স্তর', 'target' => 'লক্ষ্য', 'star' => 'তারা', 'book' => 'বই', 'users' => 'দল', 'play' => 'প্লে', 'calendar' => 'ক্যালেন্ডার'];
@endphp

<x-layouts.admin :title="$isEditing ? 'প্রোগ্রাম এডিট' : 'নতুন প্রোগ্রাম'">
    <x-admin.page-header :title="$isEditing ? $program->translate('name', 'bn') : 'নতুন প্রোগ্রাম'" />

    <form method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.programs.update', $program) : route('admin.programs.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card title="তথ্য">
            <div class="grid gap-4">
                <x-form.translatable name="name" label="প্রোগ্রামের নাম" :value="$program->name" required />
                <x-form.translatable name="tagline" label="এক লাইনের পরিচিতি" :value="$program->tagline" />
                <x-form.translatable name="description" label="বিস্তারিত" :value="$program->description" :multiline="true" />
                <div class="grid gap-4 md:grid-cols-2">
                    <x-form.field name="slug" label="URL নাম (ইংরেজি)" :value="$program->slug" help="খালি রাখলে ইংরেজি নাম থেকে তৈরি হবে। যেমন: math-olympiad" />
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$program->sort_order ?? 0" />
                </div>
            </div>
        </x-card>

        <x-card title="দেখতে কেমন হবে">
            <div class="grid gap-4 md:grid-cols-3">
                <x-form.select name="icon" label="আইকন" :value="$program->icon" :options="$icons" />
                <x-form.select name="accent" label="রং" :value="$program->accent" :options="['teal' => 'টিল', 'coral' => 'কোরাল', 'sun' => 'হলুদ']" />
                <x-form.select name="status" label="অবস্থা" :value="$program->status?->value ?? $program->status" :options="PublishStatus::options()" />
            </div>
            <div class="mt-4">
                <x-form.image name="image" label="কভার ছবি (ঐচ্ছিক)" :value="$program->image_url" />
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">{{ $isEditing ? 'আপডেট করুন' : 'তৈরি করুন' }}</x-button>
            <x-button :href="route('admin.programs.index')" variant="secondary">বাতিল</x-button>
        </div>
    </form>
</x-layouts.admin>
