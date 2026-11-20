@use('App\Support\Localization')

@props([
    'video',
])

{{--
    One learning video: a lite YouTube player (only the thumbnail loads
    until play; vertical for Shorts) with the subject/topic and title.
--}}
<article {{ $attributes->class(['flex flex-col']) }}>
    <x-youtube :url="$video->youtube_url" :title="$video->translate('title')" :vertical="$video->isVertical()" class="rounded-[1.5rem]" />

    <div class="mt-3 px-1">
        <p class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
            <a href="{{ Localization::route('learn.index', ['subject' => $video->subject->slug]) }}" class="rounded-full bg-brand-soft px-2.5 py-1 text-brand hover:underline">{{ $video->subject->translate('name') }}</a>
            @if ($video->topic)
                <a href="{{ Localization::route('learn.index', ['subject' => $video->subject->slug, 'topic' => $video->topic->slug]) }}" class="rounded-full bg-surface-muted px-2.5 py-1 text-ink-muted hover:text-ink">{{ $video->topic->translate('name') }}</a>
            @endif
        </p>
        <h3 class="mt-2 line-clamp-2 font-bold text-ink">{{ $video->translate('title') }}</h3>
    </div>
</article>
