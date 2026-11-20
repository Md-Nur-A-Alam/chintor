@use('App\Support\HeroBanner')

{{--
    Full-width background-banner hero (see App\Support\HeroBanner).

    Expects:
      $banner   {desktop, mobile, align, overlay}
      $content  {eyebrow, title, subtitle, buttons: list<{label, url, style}>,
                 words: list<string>, endsAt: ?Carbon}

    The text block sits left, centre or right; a black-to-transparent
    overlay starts from that side on wide screens and covers evenly on
    phones, so white text stays readable on any artwork.
--}}
@php
    $align = $banner['align'];
    $blockClasses = match ($align) {
        'center' => 'mx-auto items-center text-center',
        'right' => 'ml-auto items-center text-center md:items-end md:text-right',
        default => 'items-center text-center md:items-start md:text-left',
    };
    $buttonRow = match ($align) {
        'center' => 'justify-center',
        'right' => 'justify-center md:justify-end',
        default => 'justify-center md:justify-start',
    };
@endphp

<section class="relative isolate overflow-hidden bg-teal-950 text-white">
    <picture>
        @if ($banner['mobile'])
            <source media="(max-width: 767px)" srcset="{{ $banner['mobile'] }}">
        @endif
        <img src="{{ $banner['desktop'] }}" alt="" width="1920" height="900" fetchpriority="high" decoding="async" class="absolute inset-0 -z-20 size-full object-cover">
    </picture>
    <div aria-hidden="true" class="absolute inset-0 -z-10 hidden md:block" style="background: {{ HeroBanner::overlayCss($align, $banner['overlay']) }}"></div>
    <div aria-hidden="true" class="absolute inset-0 -z-10 md:hidden" style="background: {{ HeroBanner::mobileOverlayCss($banner['overlay']) }}"></div>

    <div class="container-page flex min-h-[34rem] items-center py-20 sm:min-h-[38rem] lg:min-h-[42rem]">
        <div class="flex w-full max-w-2xl flex-col {{ $blockClasses }}">
            @if ($content['eyebrow'] !== '')
                <p class="animate-rise inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-sm font-semibold backdrop-blur ring-1 ring-white/20">
                    <span class="relative flex size-2.5">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-coral-400 opacity-70"></span>
                        <span class="relative size-2.5 rounded-full bg-coral-400"></span>
                    </span>
                    {{ $content['eyebrow'] }}
                </p>
            @endif

            <h1 class="animate-rise mt-6 text-[2.5rem] leading-[1.15] font-bold tracking-tight text-balance drop-shadow-[0_2px_12px_rgb(0_0_0/0.35)] sm:text-6xl lg:text-[4.25rem]" style="--delay: 1">
                {{ $content['title'] }}
            </h1>

            @if ($content['words'] !== [])
                <p class="animate-rise mt-3 text-2xl font-bold text-coral-300 sm:text-4xl" style="--delay: 2">{{ implode(' · ', $content['words']) }}</p>
            @endif

            @if ($content['subtitle'] !== '')
                <p class="animate-rise mt-6 max-w-xl text-lg text-pretty text-white/90 sm:text-xl" style="--delay: 3">{{ $content['subtitle'] }}</p>
            @endif

            @if ($content['endsAt'])
                <x-site.countdown :ends-at="$content['endsAt']" class="animate-rise mt-5 rounded-full bg-black/30 px-4 py-2 text-sm font-semibold backdrop-blur" style="--delay: 3" />
            @endif

            @if ($content['buttons'] !== [])
                <div class="animate-rise mt-9 flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap {{ $buttonRow }}" style="--delay: 4">
                    @foreach ($content['buttons'] as $button)
                        <x-button
                            :href="$button['url']"
                            :variant="$button['style'] === 'secondary' ? 'ghost' : $button['style']"
                            size="lg"
                            @class(['group', 'bg-white/10 text-white ring-1 ring-white/40 backdrop-blur hover:bg-white/20' => $button['style'] === 'secondary'])
                        >
                            {{ $button['label'] }}
                            @if ($loop->first)
                                <x-icon name="arrow-right" class="size-5 transition-transform duration-200 group-hover:translate-x-1" />
                            @endif
                        </x-button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
