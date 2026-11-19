@use('App\Support\PhoneNumber')

@php
    $whatsappUrl = PhoneNumber::whatsappUrl($siteContact['whatsapp'] ?? null, __('site.cta.whatsapp_message'));
    $telNumber = PhoneNumber::forTelLink($siteContact['hotline'] ?? null);
@endphp

<x-layouts.app :title="__('site.lead.thanks_title')" :noindex="true">
    <div class="container-page grid place-items-center py-16 text-center">
        <x-mascot mood="wave" class="h-36 w-auto text-ink" />
        <h1 class="mt-6 text-3xl font-bold text-ink sm:text-4xl">{{ __('site.lead.thanks_title') }}</h1>
        <p class="mt-3 max-w-xl text-lg text-ink-muted">{{ __('site.lead.thanks_body') }}</p>
        @if ($lead)
            <p class="mt-4 rounded-full bg-surface-muted px-4 py-1.5 text-sm font-semibold text-ink">{{ __('site.lead.reference', ['reference' => $lead->reference]) }}</p>
        @endif

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            @if ($whatsappUrl)
                <x-button :href="$whatsappUrl" target="_blank" rel="noopener" variant="brand"><x-icon name="message" class="size-4" /> {{ __('site.cta.chat_on_whatsapp') }}</x-button>
            @endif
            @if ($telNumber)
                <x-button :href="'tel:'.$telNumber" variant="secondary"><x-icon name="phone" class="size-4" /> {{ __('site.cta.talk_to_us') }}</x-button>
            @endif
            <x-button :href="\App\Support\Localization::route('courses.index')" variant="ghost">{{ __('site.cta.explore_courses') }}</x-button>
        </div>
    </div>
</x-layouts.app>
