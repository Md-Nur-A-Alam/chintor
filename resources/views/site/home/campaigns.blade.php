{{-- Every running campaign or event, newest first by sort order. --}}
@if ($campaigns->isNotEmpty())
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']" id="campaigns">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($campaigns as $campaign)
                @php($link = $campaign->localizedLink())
                <article class="flex flex-col overflow-hidden rounded-card bg-surface shadow-card ring-1 ring-line">
                    @if ($campaign->image_url)
                        <img src="{{ $campaign->image_url }}" alt="" width="1080" height="1080" loading="lazy" decoding="async" class="aspect-square w-full object-cover">
                    @else
                        <div class="grid aspect-[2/1] place-items-center bg-teal-900 text-coral-300">
                            <x-icon :name="$campaign->channel->icon()" class="size-12" />
                        </div>
                    @endif

                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-surface-muted px-2.5 py-1 text-xs font-semibold text-ink-muted">
                            <x-icon :name="$campaign->channel->icon()" class="size-3.5" />
                            {{ __("site.home.channels.{$campaign->channel->value}") }}
                        </span>
                        <h3 class="text-xl font-bold text-ink">{{ $campaign->translate('title') }}</h3>
                        @if ($campaign->translate('summary'))
                            <p class="text-ink-muted">{{ $campaign->translate('summary') }}</p>
                        @endif
                        @if ($campaign->ends_at)
                            <x-site.countdown :ends-at="$campaign->ends_at" class="text-sm font-semibold text-accent-ink" />
                        @endif
                        @if ($link)
                            <div class="mt-auto pt-2">
                                <x-button :href="$link" size="sm" :target="str_starts_with($link, url('/')) ? null : '_blank'" :rel="str_starts_with($link, url('/')) ? null : 'noopener'">
                                    {{ $campaign->translate('button_label') ?: __('site.home.campaign.details') }}
                                    <x-icon name="arrow-right" class="size-4" />
                                </x-button>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </x-site.section>
@endif
