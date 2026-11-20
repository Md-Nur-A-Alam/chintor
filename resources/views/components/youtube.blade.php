@use('App\Support\YouTube')

@props([
    'url',
    'title' => null,
    'vertical' => false,
])

@php
    $videoId = YouTube::videoId($url);
    $title ??= __('site.video.play');
@endphp

{{--
    Lite YouTube embed: only a thumbnail loads with the page; the player
    (≈1 MB of YouTube scripts) loads after the visitor presses play.
    Works with unlisted videos. Without JavaScript it links to YouTube.
--}}
@if ($videoId)
    <div
        x-data="{ playing: false }"
        {{ $attributes->class(['relative overflow-hidden rounded-card bg-teal-950 shadow-card ring-1 ring-line', $vertical ? 'aspect-[9/16]' : 'aspect-video']) }}
    >
        <template x-if="playing">
            <iframe
                src="{{ YouTube::embedUrl($videoId) }}"
                title="{{ $title }}"
                class="absolute inset-0 size-full"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
            ></iframe>
        </template>

        <a
            href="{{ YouTube::watchUrl($videoId) }}"
            x-show="! playing"
            x-on:click.prevent="playing = true"
            class="group absolute inset-0 grid cursor-pointer place-items-center"
            aria-label="{{ $title }}"
        >
            <img
                src="{{ YouTube::thumbnailUrl($videoId) }}"
                alt=""
                loading="lazy"
                decoding="async"
                width="480"
                height="360"
                class="absolute inset-0 size-full object-cover transition duration-300 group-hover:scale-[1.02]"
            >
            <span aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></span>
            <span class="relative grid size-16 place-items-center rounded-full bg-accent text-on-accent shadow-glow transition duration-200 group-hover:scale-110">
                <x-icon name="play" class="size-7 translate-x-0.5" />
            </span>
        </a>
    </div>
@else
    <div {{ $attributes->class(['grid aspect-video place-items-center rounded-card bg-surface-muted text-sm text-ink-subtle ring-1 ring-line']) }}>
        {{ __('site.video.invalid') }}
    </div>
@endif
