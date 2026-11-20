@use('App\Support\Localization')
@use('App\Support\PhoneNumber')

{{--
    FAQ beside a "still have questions?" card with the hotline and WhatsApp
    (from admin → Settings → Contact), so the next step is always one tap.
--}}
@if ($faqs->isNotEmpty())
    @php($whatsappUrl = PhoneNumber::whatsappUrl($siteContact['whatsapp'], __('site.cta.whatsapp_message')))

    <x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']">
        <div class="grid items-start gap-8 lg:grid-cols-[1.6fr_1fr]">
            <div data-reveal>
                <x-site.faq-list :faqs="$faqs" />
            </div>

            <aside data-reveal style="--reveal-index: 1" class="relative isolate overflow-hidden rounded-[2rem] bg-surface p-7 shadow-card ring-1 ring-line lg:sticky lg:top-28">
                <div aria-hidden="true" class="absolute -top-16 -right-16 -z-10 size-48 rounded-full bg-accent-soft blur-2xl"></div>
                <x-mascot mood="think" class="animate-float h-20 w-auto text-ink" />
                <h3 class="mt-4 text-2xl font-bold text-ink">{{ __('site.home.faq_help.title') }}</h3>
                <p class="mt-2 text-ink-muted">{{ __('site.home.faq_help.body') }}</p>

                <div class="mt-6 grid gap-3">
                    @if ($siteContact['hotline'])
                        <x-button :href="'tel:'.PhoneNumber::forTelLink($siteContact['hotline'])">
                            <x-icon name="phone" class="size-4" /> {{ Localization::digits($siteContact['hotline']) }}
                        </x-button>
                    @endif
                    @if ($whatsappUrl)
                        <x-button :href="$whatsappUrl" variant="secondary" target="_blank" rel="noopener">
                            <x-icon name="message" class="size-4" /> {{ __('site.cta.chat_on_whatsapp') }}
                        </x-button>
                    @endif
                    @if (! $siteContact['hotline'] && ! $whatsappUrl)
                        <x-button :href="Localization::route('start')">{{ __('site.cta.request_callback') }}</x-button>
                    @endif
                </div>
            </aside>
        </div>
    </x-site.section>
@endif
