@use('App\Models\LearningVideo')

@php($isEditing = $video->exists)

<x-layouts.admin :title="$isEditing ? 'ভিডিও এডিট' : 'নতুন ভিডিও'">
    <x-admin.page-header :title="$isEditing ? $video->translate('title', 'bn') : 'নতুন ভিডিও'" />

    @if ($subjects->isEmpty())
        <x-card><p class="text-ink-muted">আগে <a href="{{ route('admin.learning-topics.index') }}" class="font-semibold text-accent-ink underline">বিষয় ও টপিক</a> যোগ করুন।</p></x-card>
    @else
        <form method="POST" action="{{ $isEditing ? route('admin.learning-videos.update', $video) : route('admin.learning-videos.store') }}" class="grid gap-6">
            @csrf
            @if ($isEditing) @method('PUT') @endif

            <x-card>
                <div class="grid gap-4">
                    <x-form.field name="youtube_url" label="YouTube লিংক" :value="$video->youtube_url" required placeholder="https://youtube.com/shorts/…" help="Shorts, সাধারণ ভিডিও বা unlisted — সব চলবে। Shorts লিংক হলে খাড়া (রিল) কার্ডে দেখাবে।" />
                    <x-form.translatable name="title" label="শিরোনাম" :value="$video->title" required />
                    <x-form.translatable name="description" label="সংক্ষেপ (ঐচ্ছিক)" :value="$video->description" :multiline="true" :rows="2" />
                    @include('admin.partials.subject-topic-fields', ['subjectId' => $video->subject_id, 'topicId' => $video->topic_id])
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-form.select name="orientation" label="ধরন" :value="$video->orientation" placeholder="লিংক দেখে নিজে থেকে" :options="[LearningVideo::Vertical => 'খাড়া (Shorts/রিল)', LearningVideo::Horizontal => 'চওড়া (সাধারণ ভিডিও)']" />
                        <x-form.field name="sort_order" type="number" label="ক্রম" :value="$video->sort_order ?? 0" />
                    </div>
                    <div class="grid gap-3 rounded-2xl bg-surface-muted p-4">
                        <x-form.checkbox name="is_published" label="ওয়েবসাইটে প্রকাশ করুন" :checked="$video->is_published" />
                        <x-form.checkbox name="is_featured" :label="'হোমপেজে ফিচার করুন (সর্বোচ্চ '.LearningVideo::MAX_FEATURED.'টি)'" :checked="$video->is_featured" />
                    </div>
                </div>
            </x-card>

            <div class="flex gap-3">
                <x-button type="submit">সেভ করুন</x-button>
                <x-button :href="route('admin.learning-videos.index')" variant="secondary">ফিরে যান</x-button>
            </div>
        </form>
    @endif
</x-layouts.admin>
