<x-layouts.admin title="বিষয় ও টপিক">
    <x-admin.page-header title="বিষয় ও টপিক" description="শেখার ভিডিও আর প্রশ্ন ব্যাংক এই তালিকা ব্যবহার করে। বিষয় = মূল ভাগ (যেমন গণিত), টপিক = তার ভেতরের অংশ (যেমন জ্যামিতি)। “লিংক-নাম” ওয়েবসাইটের ফিল্টার-লিংকে বসে, তাই ইংরেজিতে লিখুন।" />

    <div class="grid gap-4">
        @foreach ($subjects as $subject)
            <details class="rounded-card bg-surface shadow-card ring-1 ring-line" @if ($loop->first) open @endif>
                <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 p-5">
                    <span class="text-lg font-bold text-ink">{{ $subject->translate('name', 'bn') }} <span class="text-sm font-normal text-ink-subtle">/{{ $subject->slug }}</span></span>
                    <span class="flex gap-2 text-sm">
                        <x-badge>{{ $subject->children->count() }} টপিক</x-badge>
                        <x-badge tone="info">{{ $subject->videos_as_subject_count }} ভিডিও</x-badge>
                        <x-badge tone="success">{{ $subject->questions_as_subject_count }} প্রশ্ন</x-badge>
                    </span>
                </summary>

                <div class="grid gap-5 border-t border-line p-5">
                    @include('admin.learning-topics.partials.form', ['topic' => $subject, 'parentId' => null, 'action' => route('admin.learning-topics.update', $subject), 'method' => 'PUT'])

                    <div class="grid gap-2">
                        <p class="text-sm font-semibold text-ink">টপিক</p>
                        @forelse ($subject->children as $child)
                            <details class="rounded-2xl bg-surface-muted">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm">
                                    <span class="font-semibold text-ink">{{ $child->translate('name', 'bn') }} <span class="font-normal text-ink-subtle">/{{ $child->slug }}</span></span>
                                    <x-icon name="pencil" class="size-4 text-ink-subtle" />
                                </summary>
                                <div class="border-t border-line p-4">
                                    @include('admin.learning-topics.partials.form', ['topic' => $child, 'parentId' => $subject->id, 'action' => route('admin.learning-topics.update', $child), 'method' => 'PUT'])
                                </div>
                            </details>
                        @empty
                            <p class="text-sm text-ink-subtle">এখনো কোনো টপিক নেই।</p>
                        @endforelse
                    </div>

                    <details class="rounded-2xl ring-1 ring-line ring-inset">
                        <summary class="flex cursor-pointer list-none items-center gap-2 px-4 py-3 text-sm font-semibold text-accent-ink"><x-icon name="plus" class="size-4" /> “{{ $subject->translate('name', 'bn') }}”-এ নতুন টপিক</summary>
                        <div class="border-t border-line p-4">
                            @include('admin.learning-topics.partials.form', ['topic' => new App\Models\LearningTopic, 'parentId' => $subject->id, 'action' => route('admin.learning-topics.store'), 'method' => 'POST'])
                        </div>
                    </details>
                </div>
            </details>
        @endforeach

        <x-card title="নতুন বিষয়" description="যেমন: গণিত (math), বিজ্ঞান (science), আইকিউ (iq), কমার্স (commerce), আইটি (it)।">
            @include('admin.learning-topics.partials.form', ['topic' => new App\Models\LearningTopic, 'parentId' => null, 'action' => route('admin.learning-topics.store'), 'method' => 'POST'])
        </x-card>
    </div>
</x-layouts.admin>
