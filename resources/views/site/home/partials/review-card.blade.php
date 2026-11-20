{{-- One review: optional video, stars, quote, and who wrote it. --}}
<figure class="flex w-full flex-col rounded-card bg-surface p-6 shadow-card ring-1 ring-line">
    @if ($withVideo)
        <x-youtube :url="$review->video_url" :title="__('site.home.video_review').' — '.$review->author_name" class="mb-4" />
    @endif

    @if ($review->rating)
        <div class="flex gap-0.5 text-sun-500" role="img" aria-label="{{ $review->rating }}/5">
            @for ($star = 1; $star <= 5; $star++)
                <x-icon name="star" @class(['size-4', 'fill-current' => $star <= $review->rating]) />
            @endfor
        </div>
    @endif

    <blockquote class="mt-3 flex-1 leading-relaxed text-ink">“{{ $review->translate('body') }}”</blockquote>

    <figcaption class="mt-5 flex items-center gap-3 text-sm">
        @if ($review->photo_url)
            <img src="{{ $review->photo_url }}" alt="" width="44" height="44" loading="lazy" class="size-11 rounded-full object-cover ring-1 ring-line">
        @else
            <span aria-hidden="true" class="grid size-11 shrink-0 place-items-center rounded-full bg-brand-soft font-bold text-brand">{{ mb_substr($review->author_name, 0, 1) }}</span>
        @endif
        <span>
            <span class="block font-semibold text-ink">{{ $review->author_name }}</span>
            <span class="text-ink-subtle">
                {{ $review->translate('author_role') ?: __("site.home.audience.{$review->audience->value}") }}
                @if ($review->course)
                    · {{ $review->course->translate('title') }}
                @endif
            </span>
        </span>
    </figcaption>
</figure>
