@use('App\Models\LearningVideo')
@use('App\Support\YouTube')

<x-layouts.admin title="শেখার ভিডিও">
    <x-admin.page-header title="শেখার ভিডিও" description="YouTube-এর ছোট ব্যাখ্যামূলক ভিডিও — ওয়েবসাইটের “ফ্রি ভিডিও” পেজে বিষয়-টপিক অনুযায়ী দেখায়।">
        <x-slot:actions>
            <x-button :href="route('admin.learning-topics.index')" variant="secondary" size="sm"><x-icon name="layers" class="size-4" /> বিষয় ও টপিক</x-button>
            <x-button :href="route('admin.learning-videos.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন ভিডিও</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <p class="mb-4 text-sm text-ink-muted">হোমপেজে ফিচার্ড: <strong class="text-ink">{{ $featuredCount }}/{{ LearningVideo::MAX_FEATURED }}</strong></p>

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">ভিডিও</th><th class="px-4 py-3">বিষয় › টপিক</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($videos as $video)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if ($video->videoId())
                            <img src="{{ YouTube::thumbnailUrl($video->videoId()) }}" alt="" loading="lazy" @class(['rounded-lg object-cover ring-1 ring-line', 'h-16 w-9' => $video->isVertical(), 'h-12 w-20' => ! $video->isVertical()])>
                        @endif
                        <span class="font-semibold text-ink">{{ $video->translate('title', 'bn') }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-ink-muted">{{ $video->subject->translate('name', 'bn') }}@if ($video->topic) › {{ $video->topic->translate('name', 'bn') }}@endif</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        <x-badge :tone="$video->is_published ? 'success' : 'neutral'">{{ $video->is_published ? 'প্রকাশিত' : 'খসড়া' }}</x-badge>
                        @if ($video->is_featured)<x-badge tone="warning">ফিচার্ড</x-badge>@endif
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.learning-videos.edit', $video)" variant="ghost" size="sm" aria-label="এডিট"><x-icon name="pencil" class="size-4" /></x-button>
                        <form method="POST" action="{{ route('admin.learning-videos.destroy', $video) }}" onsubmit="return confirm('মুছে ফেলবেন?')">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm" class="text-accent-ink" aria-label="মুছুন"><x-icon name="trash" class="size-4" /></x-button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো ভিডিও নেই। আগে “বিষয় ও টপিক” যোগ করুন, তারপর ভিডিও।</td></tr>
        @endforelse
    </x-admin.table>

    <div class="mt-6">{{ $videos->links() }}</div>
</x-layouts.admin>
