@use('App\Support\Localization')
@use('App\Support\YouTube')

@props([
    'course',
])

@php
    $startingVariation = $course->startingVariation();
    $nextBatch = $course->openBatches->first();
    $videoId = YouTube::videoId($course->explainer_video_url);
    $imageUrl = $course->thumbnail_url ?: ($videoId ? YouTube::thumbnailUrl($videoId) : null);
    $url = Localization::route('courses.show', ['course' => $course]);
@endphp

<article class="group flex flex-col overflow-hidden rounded-card bg-surface shadow-card ring-1 ring-line transition duration-200 hover:-translate-y-1 hover:ring-line-strong">
    <a href="{{ $url }}" class="relative block aspect-video overflow-hidden bg-brand-soft">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" loading="lazy" decoding="async" width="480" height="270" class="size-full object-cover transition duration-300 group-hover:scale-[1.03]">
        @else
            <div class="grid size-full place-items-center"><x-mascot mood="think" class="h-24 w-auto text-brand" /></div>
        @endif

        <span class="absolute top-3 left-3 rounded-full bg-surface/95 px-2.5 py-1 text-xs font-semibold text-brand shadow-sm">
            {{ $course->program->translate('name') }}
        </span>

        @if ($videoId)
            <span class="absolute right-3 bottom-3 grid size-10 place-items-center rounded-full bg-accent text-on-accent shadow-glow">
                <x-icon name="play" class="size-4 translate-x-px" />
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-3 p-5">
        @if ($course->classRangeLabel())
            <p class="text-sm font-medium text-ink-subtle">{{ $course->classRangeLabel() }}</p>
        @endif

        <h3 class="text-xl leading-snug font-bold text-ink">
            <a href="{{ $url }}" class="hover:text-accent-ink">{{ $course->translate('title') }}</a>
        </h3>

        @if ($course->translate('summary'))
            <p class="line-clamp-2 text-ink-muted">{{ $course->translate('summary') }}</p>
        @endif

        <ul class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-ink-muted">
            @if ($course->live_class_count)
                <li class="flex items-center gap-1.5"><x-icon name="users" class="size-4 text-brand" />{{ __('site.course.live_classes', ['count' => Localization::digits($course->live_class_count)]) }}</li>
            @endif
            @if ($nextBatch?->starts_on)
                <li class="flex items-center gap-1.5"><x-icon name="calendar" class="size-4 text-brand" />{{ __('site.course.batch_starts', ['date' => Localization::digits($nextBatch->starts_on->translatedFormat('j M'))]) }}</li>
            @endif
        </ul>

        <div class="mt-auto flex items-end justify-between gap-3 border-t border-line pt-4">
            @if ($startingVariation)
                <div>
                    <p class="text-xs text-ink-subtle">{{ $startingVariation->translate('label') }}</p>
                    <x-price :regular="$startingVariation->regular_price" :discounted="$startingVariation->discounted_price" size="sm" :show-saving="false" />
                </div>
            @else
                <span></span>
            @endif
            <x-button :href="$url" variant="brand" size="sm">{{ __('site.details') }}</x-button>
        </div>
    </div>
</article>
