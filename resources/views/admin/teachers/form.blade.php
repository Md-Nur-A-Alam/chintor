@php
    $isEditing = $teacher->exists;
    $canEditHtml = auth()->user()->isSuperAdmin();
@endphp

<x-layouts.admin :title="$isEditing ? 'শিক্ষক এডিট' : 'নতুন শিক্ষক'">
    <x-admin.page-header :title="$isEditing ? $teacher->translate('name', 'bn') : 'নতুন শিক্ষক'" />

    <form
        method="POST"
        enctype="multipart/form-data"
        action="{{ $isEditing ? route('admin.teachers.update', $teacher) : route('admin.teachers.store') }}"
        x-data="{ mode: @js(old('details_mode', $teacher->details_mode)) }"
        class="grid gap-6"
    >
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card title="পরিচিতি">
            <div class="grid gap-4">
                <x-form.translatable name="name" label="নাম" :value="$teacher->name" required />
                <x-form.translatable name="designation" label="পদবি / বিষয়" :value="$teacher->designation" />
                <x-form.translatable name="summary" label="সংক্ষিপ্ত পরিচিতি (কার্ডে দেখাবে)" :value="$teacher->summary" :multiline="true" :rows="2" />
                <x-form.image name="photo" label="ছবি" :value="$teacher->photo_url" />
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.field name="slug" label="URL নাম (ইংরেজি)" :value="$teacher->slug" />
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$teacher->sort_order ?? 0" />
                    <div class="self-end pb-2"><x-form.checkbox name="is_active" label="ওয়েবসাইটে দেখাবে" :checked="$teacher->is_active" /></div>
                </div>
            </div>
        </x-card>

        <x-card title="বিস্তারিত প্রোফাইল" description="প্রত্যেক শিক্ষকের যোগ্যতা আলাদা — তাই সাধারণ লেখা অথবা নিজের মতো সাজানো HTML পেজ, দুটোই চলবে।">
            <div class="mb-4 inline-flex rounded-full bg-surface-muted p-1 ring-1 ring-line ring-inset">
                @foreach (['rich' => 'সাধারণ লেখা', 'html' => 'কাস্টম HTML পেজ'] as $mode => $modeLabel)
                    <label class="cursor-pointer rounded-full px-4 py-1.5 text-sm font-medium has-[:checked]:bg-surface-raised has-[:checked]:shadow-sm">
                        <input type="radio" name="details_mode" value="{{ $mode }}" x-model="mode" class="sr-only"> {{ $modeLabel }}
                    </label>
                @endforeach
            </div>

            <div x-show="mode === 'rich'">
                <x-form.translatable name="details_rich" label="প্রোফাইলের লেখা" :value="$teacher->details_rich" :multiline="true" :rows="8" />
            </div>

            <div x-show="mode === 'html'" x-cloak class="grid gap-3">
                <x-form.textarea
                    name="details_html"
                    label="HTML (inline CSS / JavaScript সহ সম্পূর্ণ পেজ দেওয়া যাবে)"
                    :value="$teacher->details_html"
                    rows="14"
                    class="font-mono text-xs"
                    :readonly="! $canEditHtml"
                    help="নিরাপত্তার জন্য এটি আলাদা sandboxed ফ্রেমে দেখানো হয় — সাইট বা লগইনে প্রভাব ফেলতে পারে না। শুধু অ্যাডমিন বদলাতে পারেন।"
                />
                @if ($teacher->details_html)
                    <p class="text-sm font-semibold text-ink">প্রিভিউ</p>
                    <iframe title="Preview" sandbox="allow-scripts" srcdoc="{{ $teacher->details_html }}" class="h-96 w-full rounded-2xl bg-white ring-1 ring-line"></iframe>
                @endif
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">{{ $isEditing ? 'আপডেট করুন' : 'তৈরি করুন' }}</x-button>
            <x-button :href="route('admin.teachers.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>

    @if ($isEditing)
        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('প্রোফাইলটি মুছে ফেলবেন?')" class="mt-8">
            @csrf @method('DELETE')
            <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> মুছে ফেলুন</x-button>
        </form>
    @endif
</x-layouts.admin>
