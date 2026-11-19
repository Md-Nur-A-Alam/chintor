@use('App\Enums\PublishStatus')
@use('App\Models\Page')

@php($isEditing = $page->exists)

<x-layouts.admin :title="$isEditing ? 'পেজ এডিট' : 'নতুন পেজ'">
    <x-admin.page-header :title="$isEditing ? $page->translate('title', 'bn') : 'নতুন পেজ'" />

    <form method="POST" action="{{ $isEditing ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.field name="slug" label="URL নাম" :value="$page->slug" help="যেমন: refund-policy" required />
                    <x-form.select name="type" label="ধরন" :value="$page->type" :options="Page::TYPES" />
                    <x-form.select name="status" label="অবস্থা" :value="$page->status?->value" :options="PublishStatus::options()" />
                </div>
                <x-form.translatable name="title" label="শিরোনাম" :value="$page->title" required />
                <x-form.translatable name="body" label="লেখা (Markdown)" :value="$page->body" :multiline="true" :rows="16" help="## শিরোনাম · **বোল্ড** · - তালিকা · [লিংক](https://...)" />
                <x-form.translatable name="seo_description" label="SEO বিবরণ" :value="$page->seo_description" :multiline="true" :rows="2" />
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">{{ $isEditing ? 'আপডেট করুন' : 'তৈরি করুন' }}</x-button>
            <x-button :href="route('admin.pages.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>

    @if ($isEditing)
        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('পেজটি মুছে ফেলবেন?')" class="mt-8">
            @csrf @method('DELETE')
            <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> মুছে ফেলুন</x-button>
        </form>
    @endif
</x-layouts.admin>
