@use('App\Support\Localization')
@use('App\Support\PhoneNumber')

@php
    $contact = $siteContact;
    $telNumber = PhoneNumber::forTelLink($contact['hotline']);
    $whatsappUrl = PhoneNumber::whatsappUrl($contact['whatsapp'], __('site.cta.whatsapp_message'));
    $startUrl = Localization::route('start');
@endphp

{{-- Mobile: always-visible action bar (call · WhatsApp · primary CTA). --}}
<div class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-surface/95 pb-[env(safe-area-inset-bottom)] backdrop-blur md:hidden">
    <div class="flex items-center gap-2 px-3 py-2.5">
        @if ($telNumber)
            <a href="tel:{{ $telNumber }}" class="grid size-12 shrink-0 place-items-center rounded-full bg-brand-soft text-brand">
                <x-icon name="phone" class="size-5" />
                <span class="sr-only">{{ __('site.cta.call') }}</span>
            </a>
        @endif
        @if ($whatsappUrl)
            <a href="{{ $whatsappUrl }}" rel="noopener" target="_blank" class="grid size-12 shrink-0 place-items-center rounded-full bg-[#25D366] text-white">
                <x-icon name="message" class="size-5" />
                <span class="sr-only">{{ __('site.cta.chat_on_whatsapp') }}</span>
            </a>
        @endif
        <x-button :href="$startUrl" size="lg" class="flex-1">
            {{ __('site.cta.start_learning') }}
        </x-button>
    </div>
</div>

{{-- Desktop: floating WhatsApp button. --}}
@if ($whatsappUrl)
    <a
        href="{{ $whatsappUrl }}"
        rel="noopener"
        target="_blank"
        class="fixed right-6 bottom-6 z-40 hidden size-14 place-items-center rounded-full bg-[#25D366] text-white shadow-lg shadow-black/15 transition-transform hover:scale-105 md:grid"
    >
        <x-icon name="message" class="size-6" />
        <span class="sr-only">{{ __('site.cta.chat_on_whatsapp') }}</span>
    </a>
@endif
