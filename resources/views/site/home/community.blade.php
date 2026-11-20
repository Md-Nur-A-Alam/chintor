{{--
    Social channels from admin → Settings → Contact. An active Facebook page
    and YouTube channel are everyday proof for Bangladeshi parents that a
    company is real and alive, so they get their own section.
--}}
@php
    $networks = array_filter([
        'facebook' => $siteContact['facebook_url'],
        'youtube' => $siteContact['youtube_url'],
        'instagram' => $siteContact['instagram_url'],
    ]);
@endphp

@if ($networks !== [])
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']">
        <ul @class([
            'grid gap-4',
            'sm:grid-cols-2' => count($networks) >= 2,
            'lg:grid-cols-3' => count($networks) === 3,
        ])>
            @foreach ($networks as $network => $url)
                <li data-reveal style="--reveal-index: {{ $loop->index }}">
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="group flex min-h-20 items-center gap-4 rounded-card bg-surface p-5 shadow-card ring-1 ring-line transition duration-200 hover:-translate-y-0.5 hover:ring-line-strong">
                        <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-brand-soft text-brand ring-1 ring-brand/15 ring-inset">
                            <x-icon :name="$network" class="size-6" />
                        </span>
                        <span class="flex-1 font-semibold text-ink">{{ __("site.home.community_cta.{$network}") }}</span>
                        <x-icon name="external" class="size-4 text-ink-subtle transition group-hover:text-ink" />
                    </a>
                </li>
            @endforeach
        </ul>
    </x-site.section>
@endif
