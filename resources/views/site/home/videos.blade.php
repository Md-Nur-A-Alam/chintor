@use('App\Support\Localization')

{{--
    Featured learning videos (up to six, ticked in admin → Learning →
    Videos) in a swipeable row like reels; the library has the rest.
--}}
@if ($featuredVideos->isNotEmpty())
    <x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']" :action-url="Localization::route('learn.index')">
        <div @class([
            '-mx-4 flex snap-x snap-mandatory gap-5 overflow-x-auto px-4 pb-4 [scrollbar-width:none] sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0',
            'lg:justify-center' => $featuredVideos->count() <= 4,
        ])>
            @foreach ($featuredVideos as $video)
                <x-reveal :index="$loop->index" @class([
                    'shrink-0 snap-start',
                    'w-[62%] sm:w-64' => $video->isVertical(),
                    'w-[88%] sm:w-[30rem]' => ! $video->isVertical(),
                ])>
                    <x-site.video-card :video="$video" />
                </x-reveal>
            @endforeach
        </div>
    </x-site.section>
@endif
