@use('App\Enums\PublishStatus')

@php
    $isEditing = $book->exists;
    $samplePagesText = implode("\n", $book->sample_pages ?? []);
@endphp

<x-layouts.admin :title="$isEditing ? 'বই এডিট' : 'নতুন বই'">
    <x-admin.page-header :title="$isEditing ? $book->translate('title', 'bn') : 'নতুন বই'" />

    <form method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.books.update', $book) : route('admin.books.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card title="বইয়ের তথ্য">
            <div class="grid gap-4">
                <x-form.translatable name="title" label="বইয়ের নাম" :value="$book->title" required />
                <x-form.translatable name="subtitle" label="সাবটাইটেল" :value="$book->subtitle" />
                <x-form.field name="authors" label="লেখক (কমা দিয়ে আলাদা করুন)" :value="$book->authors?->map->translate('name', 'bn')->join(', ')" />
                <x-form.translatable name="description" label="বই সম্পর্কে" :value="$book->description" :multiline="true" :rows="5" />
                <x-form.lines name="table_of_contents" label="সূচিপত্র" :value="$book->table_of_contents" />
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-form.select name="program_id" label="প্রোগ্রাম (ঐচ্ছিক)" :value="$book->program_id" placeholder="—" :options="$programs->mapWithKeys(fn ($program) => [$program->id => $program->translate('name', 'bn')])->all()" />
                    <x-form.field name="pages" type="number" label="পৃষ্ঠা" :value="$book->pages" />
                    <x-form.field name="edition" label="সংস্করণ" :value="$book->edition" />
                    <x-form.field name="isbn" label="ISBN" :value="$book->isbn" />
                </div>
            </div>
        </x-card>

        <x-card title="দাম ও স্টক">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-form.field name="regular_price" type="number" label="মেইন প্রাইস (৳)" :value="$book->regular_price" required />
                <x-form.field name="discounted_price" type="number" label="ডিসকাউন্টেড প্রাইস (৳)" :value="$book->discounted_price" />
                <x-form.field name="stock_quantity" type="number" label="স্টক" :value="$book->stock_quantity" required help="পেমেন্ট যাচাই হলে স্টক নিজে থেকে কমবে।" />
            </div>
        </x-card>

        <x-card title="ছবি">
            <div class="grid gap-4">
                <x-form.image name="cover" label="কভার" :value="$book->cover_url" />
                <x-form.textarea name="sample_pages" label="ভেতরের পাতার ছবি (প্রতি লাইনে একটি URL)" :value="$samplePagesText" rows="4" help="মিডিয়া লাইব্রেরিতে ছবি আপলোড করে URL কপি করে এখানে দিন।" />
            </div>
        </x-card>

        <x-card title="প্রকাশ ও SEO">
            <div class="grid gap-4">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.select name="status" label="অবস্থা" :value="$book->status?->value" :options="PublishStatus::options()" />
                    <x-form.field name="slug" label="URL নাম (ইংরেজি)" :value="$book->slug" />
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$book->sort_order ?? 0" />
                </div>
                <x-form.checkbox name="is_featured" label="হোম পেজে ফিচার করুন" :checked="$book->is_featured" />
                <x-form.translatable name="seo_title" label="SEO শিরোনাম" :value="$book->seo_title" />
                <x-form.translatable name="seo_description" label="SEO বিবরণ" :value="$book->seo_description" :multiline="true" :rows="2" />
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">{{ $isEditing ? 'আপডেট করুন' : 'যোগ করুন' }}</x-button>
            <x-button :href="route('admin.books.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>

    @if ($isEditing)
        <form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('বইটি মুছে ফেলবেন?')" class="mt-8">
            @csrf @method('DELETE')
            <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> মুছে ফেলুন</x-button>
        </form>
    @endif
</x-layouts.admin>
