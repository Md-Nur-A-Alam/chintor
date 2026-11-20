@use('App\Support\Localization')

{{--
    Two "traffic lanes" moving in opposite directions right under the hero:
    lane 1 repeats what Chintor promises (policies, never invented numbers),
    lane 2 links to every program and course. Pauses on hover/focus.
--}}
@php
    // A lane must be wider than the screen to loop without a gap, so a short
    // list is repeated; the repeats are hidden from screen readers and Tab.
    $linkCopies = $highlightLinks === [] ? 0 : (int) ceil(8 / count($highlightLinks));

    $promises = __('site.home.highlights', [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ]);
@endphp

<section class="border-y border-line bg-surface py-5" aria-label="{{ $section['title'] }}">
    <div class="grid gap-3">
        <x-marquee :duration="50">
            @foreach ($promises as $promise)
                <span class="inline-flex shrink-0 items-center gap-2 rounded-full bg-brand-soft px-4 py-2 text-sm font-semibold whitespace-nowrap text-brand">
                    <x-icon :name="$promise['icon']" class="size-4" />
                    {{ $promise['text'] }}
                </span>
            @endforeach
        </x-marquee>

        @if ($highlightLinks !== [])
            <x-marquee :reverse="true" :duration="60">
                @for ($copy = 0; $copy < $linkCopies; $copy++)
                    @foreach ($highlightLinks as $link)
                    <a href="{{ $link['url'] }}" @if ($copy > 0) aria-hidden="true" tabindex="-1" @endif class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-full bg-surface-raised px-4 text-sm font-semibold whitespace-nowrap text-ink shadow-card ring-1 ring-line transition hover:ring-line-strong">
                        <span aria-hidden="true" class="size-1.5 rounded-full bg-accent"></span>
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                @endfor
            </x-marquee>
        @endif
    </div>
</section>
