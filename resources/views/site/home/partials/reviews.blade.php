@use('App\Support\YouTube')

{{--
    Reviews. Video reviews (a YouTube link — the most convincing proof for
    Bangladeshi parents) sit in a grid, since a playing video must not move.
    Written reviews run in two opposite "traffic lanes" once there are at
    least six (three per lane, so a lane is wider than the screen); with
    fewer they stay in a simple grid.
--}}
@php
    $videoReviews = $reviews->filter(fn ($review) => $review->video_url && YouTube::videoId($review->video_url))->values();
    $textReviews = $reviews->diff($videoReviews)->values();
    $usesLanes = $textReviews->count() >= 6;
@endphp

@if ($reviews->isNotEmpty())
    <x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']">
        @if ($videoReviews->isNotEmpty())
            <div class="mb-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($videoReviews as $review)
                    <x-reveal :index="$loop->index">
                        @include('site.home.partials.review-card', ['review' => $review, 'withVideo' => true])
                    </x-reveal>
                @endforeach
            </div>
        @endif

        @if ($usesLanes)
            <div data-reveal class="-mx-4 grid gap-5 sm:-mx-6 lg:-mx-8">
                @foreach ($textReviews->split(2) as $laneIndex => $lane)
                    <x-marquee :reverse="$laneIndex === 1" :duration="max(40, $lane->count() * 12)" :label="$section['title']">
                        @foreach ($lane as $review)
                            <div class="flex w-80 shrink-0 sm:w-96">
                                @include('site.home.partials.review-card', ['review' => $review, 'withVideo' => false])
                            </div>
                        @endforeach
                    </x-marquee>
                @endforeach
            </div>
        @elseif ($textReviews->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($textReviews as $review)
                    <x-reveal :index="$loop->index">
                        @include('site.home.partials.review-card', ['review' => $review, 'withVideo' => false])
                    </x-reveal>
                @endforeach
            </div>
        @endif
    </x-site.section>
@endif
