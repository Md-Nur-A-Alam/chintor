@use('App\Support\Localization')
@use('App\Support\PhoneNumber')

@php
    $contact = $siteContact;
    $legal = $siteLegal;

    $page = fn (string $slug): string => Localization::route('pages.show', ['page' => $slug]);

    // Policy and about pages are created and edited in the admin panel (Content → Pages).
    $columns = [
        __('site.footer.explore') => [
            __('site.nav.programs') => Localization::route('programs.index'),
            __('site.nav.courses') => Localization::route('courses.index'),
            __('site.nav.books') => Localization::route('books.index'),
            __('site.nav.learn') => Localization::route('learn.index'),
            __('site.nav.arena') => Localization::route('arena.index'),
            __('site.cta.free_assessment') => Localization::route('start', ['assessment' => 1]),
            __('site.nav.track_order') => Localization::route('orders.track'),
        ],
        __('site.footer.company') => [
            __('site.nav.about') => $page('about'),
            __('site.nav.teachers') => Localization::route('teachers.index'),
            __('site.nav.contact') => $page('contact'),
        ],
        __('site.footer.policies') => [
            __('site.footer.refund_policy') => $page('refund-policy'),
            __('site.footer.privacy_policy') => $page('privacy-policy'),
            __('site.footer.terms') => $page('terms'),
            __('site.footer.delivery_policy') => $page('delivery-policy'),
        ],
    ];

    $whatsappUrl = PhoneNumber::whatsappUrl($contact['whatsapp'], __('site.cta.whatsapp_message'));
@endphp

<footer class="mt-16 border-t border-line bg-surface">
    <div class="container-page grid gap-10 py-12 md:grid-cols-2 lg:grid-cols-[1.4fr_repeat(3,1fr)_1.2fr]">
        <div class="max-w-sm">
            <x-logo class="h-11 w-auto" />
            <p class="mt-4 text-sm leading-relaxed text-ink-muted">{{ __('site.footer.about') }}</p>

            @php($socialLinks = array_filter(['facebook' => $contact['facebook_url'], 'youtube' => $contact['youtube_url'], 'instagram' => $contact['instagram_url']]))
            @if ($socialLinks !== [])
                <div class="mt-5 flex gap-2">
                    @foreach ($socialLinks as $network => $url)
                        <a href="{{ $url }}" rel="noopener" target="_blank" class="grid size-11 place-items-center rounded-full bg-surface-muted text-ink-muted hover:text-ink">
                            <x-icon :name="$network" class="size-5" />
                            <span class="sr-only">{{ ucfirst($network) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @foreach ($columns as $heading => $links)
            <nav aria-label="{{ $heading }}">
                <h2 class="text-sm font-semibold text-ink">{{ $heading }}</h2>
                <ul class="mt-4 grid gap-2.5 text-sm">
                    @foreach ($links as $label => $url)
                        <li><a href="{{ $url }}" class="text-ink-muted hover:text-ink">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @endforeach

        <div>
            <h2 class="text-sm font-semibold text-ink">{{ __('site.footer.contact') }}</h2>
            <ul class="mt-4 grid gap-3 text-sm text-ink-muted">
                @if ($contact['hotline'])
                    <li>
                        <a href="tel:{{ PhoneNumber::forTelLink($contact['hotline']) }}" class="flex items-center gap-2 hover:text-ink">
                            <x-icon name="phone" class="size-4" /> {{ Localization::digits($contact['hotline']) }}
                        </a>
                    </li>
                @endif
                @if ($whatsappUrl)
                    <li>
                        <a href="{{ $whatsappUrl }}" rel="noopener" target="_blank" class="flex items-center gap-2 hover:text-ink">
                            <x-icon name="message" class="size-4" /> {{ __('site.cta.chat_on_whatsapp') }}
                        </a>
                    </li>
                @endif
                @if ($contact['email'])
                    <li>
                        <a href="mailto:{{ $contact['email'] }}" class="flex items-center gap-2 hover:text-ink">
                            <x-icon name="mail" class="size-4" /> {{ $contact['email'] }}
                        </a>
                    </li>
                @endif
                @if ($contact['office_hours'])
                    <li class="flex items-center gap-2">
                        <x-icon name="clock" class="size-4" />
                        {{ __('site.footer.office_hours', ['hours' => Localization::digits($contact['office_hours'])]) }}
                    </li>
                @endif
            </ul>

            @if ($contact['address'])
                <address class="mt-3 text-sm leading-relaxed text-ink-muted not-italic">{{ $contact['address'] }}</address>
            @endif

            <h2 class="mt-6 text-sm font-semibold text-ink">{{ __('site.footer.we_accept') }}</h2>
            <p class="mt-2 text-sm text-ink-muted">{{ __('site.footer.payment_methods') }}</p>
        </div>
    </div>

    <div class="border-t border-line">
        <div class="container-page flex flex-col gap-2 py-5 text-xs text-ink-subtle sm:flex-row sm:items-center sm:justify-between">
            <p>{{ __('site.footer.rights', ['year' => Localization::digits(now()->year)]) }}</p>
            @if ($legal['trade_license'] || $legal['tin'])
                <p class="flex flex-wrap gap-x-4">
                    @if ($legal['trade_license'])
                        <span>{{ __('site.footer.trade_license', ['number' => $legal['trade_license']]) }}</span>
                    @endif
                    @if ($legal['tin'])
                        <span>{{ __('site.footer.tin', ['number' => $legal['tin']]) }}</span>
                    @endif
                </p>
            @endif
        </div>
    </div>
</footer>
