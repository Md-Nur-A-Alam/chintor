@use('App\Support\Localization')

{{--
    Thin announcement bar above the header on every public page. Shows the
    first running campaign marked "show in announcement bar" (admin →
    Website → Campaigns). Visitors can dismiss it for the browser session.
    $announcement comes from a view composer in AppServiceProvider.
--}}
@if ($announcement)
    @php
        $dismissKey = "chintor-bar-{$announcement->id}-{$announcement->updated_at?->timestamp}";
        $link = $announcement->localizedLink();
    @endphp

    <div
        x-data="{
            hidden: false,
            init() { try { this.hidden = sessionStorage.getItem(@js($dismissKey)) === '1' } catch (error) {} },
            dismiss() { this.hidden = true; try { sessionStorage.setItem(@js($dismissKey), '1') } catch (error) {} },
        }"
        x-show="! hidden"
        class="relative bg-teal-900 text-white"
    >
        <div class="container-page flex min-h-11 items-center justify-center gap-x-3 gap-y-1 py-2 pr-10 text-center text-sm flex-wrap">
            <span class="inline-flex items-center gap-1.5 font-semibold">
                <x-icon :name="$announcement->channel->icon()" class="size-4 text-coral-300" />
                {{ $announcement->translate('title') }}
            </span>

            @if ($announcement->ends_at)
                <x-site.countdown :ends-at="$announcement->ends_at" class="text-teal-100" />
            @endif

            @if ($link)
                <a href="{{ $link }}" @if (! str_starts_with($link, url('/'))) target="_blank" rel="noopener" @endif class="inline-flex items-center gap-1 rounded-full bg-coral-500 px-3 py-1 font-semibold text-teal-950 hover:bg-coral-400">
                    {{ $announcement->translate('button_label') ?: __('site.home.campaign.details') }}
                    <x-icon name="arrow-right" class="size-3.5" />
                </a>
            @endif
        </div>

        <button type="button" x-on:click="dismiss()" class="absolute top-1/2 right-2 grid size-9 -translate-y-1/2 place-items-center rounded-full text-teal-100 hover:bg-white/10 hover:text-white" aria-label="{{ __('site.home.campaign.dismiss') }}">
            <x-icon name="close" class="size-4" />
        </button>
    </div>
@endif
