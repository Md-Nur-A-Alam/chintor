@use('App\Support\Localization')

{{--
    Free video library. Filters are plain links (?subject=…&topic=…), so they
    work without JavaScript, keep the back button working and can be shared.
--}}
@php
    $chip = 'inline-flex min-h-11 shrink-0 items-center rounded-full px-5 text-sm font-semibold whitespace-nowrap transition';
    $activeChip = 'bg-ink text-canvas shadow-card';
    $idleChip = 'bg-surface text-ink-muted ring-1 ring-line hover:text-ink hover:ring-line-strong';
@endphp

<x-layouts.app :title="$subject ? $subject->translate('name').' — '.__('site.learn.title') : __('site.learn.title')" :description="__('site.learn.subtitle')">
    <x-site.page-hero :title="__('site.learn.title')" :subtitle="__('site.learn.subtitle')" />

    <section class="container-page py-10 lg:py-14">
        @if ($subjects->isNotEmpty())
            <nav aria-label="{{ __('site.learn.filter_label') }}" class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:px-0">
                <a href="{{ Localization::route('learn.index') }}" @class([$chip, $activeChip => ! $subject, $idleChip => (bool) $subject]) @if (! $subject) aria-current="page" @endif>{{ __('site.learn.all') }}</a>
                @foreach ($subjects as $item)
                    <a href="{{ Localization::route('learn.index', ['subject' => $item->slug]) }}" @class([$chip, $activeChip => $subject?->is($item), $idleChip => ! $subject?->is($item)]) @if ($subject?->is($item)) aria-current="page" @endif>{{ $item->translate('name') }}</a>
                @endforeach
            </nav>

            @if ($subject && $subject->children->isNotEmpty())
                <nav aria-label="{{ __('site.learn.topic_label') }}" class="mt-3 flex flex-wrap gap-2 text-sm">
                    <a href="{{ Localization::route('learn.index', ['subject' => $subject->slug]) }}" @class(['rounded-full px-3.5 py-2 font-medium', 'bg-accent-soft text-accent-ink' => ! $topic, 'text-ink-muted hover:text-ink' => (bool) $topic])>{{ __('site.learn.all_topics') }}</a>
                    @foreach ($subject->children as $child)
                        <a href="{{ Localization::route('learn.index', ['subject' => $subject->slug, 'topic' => $child->slug]) }}" @class(['rounded-full px-3.5 py-2 font-medium', 'bg-accent-soft text-accent-ink' => $topic?->is($child), 'text-ink-muted hover:text-ink' => ! $topic?->is($child)])>{{ $child->translate('name') }}</a>
                    @endforeach
                </nav>
            @endif
        @endif

        @if ($videos->isEmpty())
            <p class="mt-12 rounded-card bg-surface p-10 text-center text-ink-muted ring-1 ring-line">{{ __('site.learn.empty') }}</p>
        @else
            {{-- Shorts are tall cards; wide videos span two columns. --}}
            <div class="mt-8 grid grid-flow-dense grid-cols-2 gap-x-5 gap-y-8 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($videos as $video)
                    <x-reveal :index="$loop->index" @class(['col-span-2' => ! $video->isVertical()])>
                        <x-site.video-card :video="$video" />
                    </x-reveal>
                @endforeach
            </div>

            <div class="mt-10">{{ $videos->links() }}</div>
        @endif

        {{-- Next step: test what you learnt. --}}
        <div class="mt-16 flex flex-col items-start justify-between gap-6 rounded-[2rem] bg-teal-900 p-8 text-white shadow-card sm:flex-row sm:items-center sm:p-10">
            <div>
                <h2 class="text-2xl font-bold sm:text-3xl">{{ __('site.learn.arena_cta_title') }}</h2>
                <p class="mt-2 max-w-xl text-teal-100">{{ __('site.learn.arena_cta_body') }}</p>
            </div>
            <x-button :href="Localization::route('arena.index')" size="lg" class="shrink-0">
                {{ __('site.nav.arena') }} <x-icon name="arrow-right" class="size-5" />
            </x-button>
        </div>
    </section>
</x-layouts.app>
