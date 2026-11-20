{{--
    Impact numbers entered by staff (admin → Website → Homepage). Shown only
    when real figures are entered; the trade licence (from .env) is a
    strong trust signal for Bangladeshi parents, so it sits beside them.
--}}
@if ($stats !== [] || $siteLegal['trade_license'])
    <section class="container-page pt-4 pb-8" aria-label="{{ $section['title'] }}">
        <div class="rounded-[2rem] bg-surface p-6 shadow-card ring-1 ring-line sm:p-8">
            @if ($stats !== [])
                <h2 class="sr-only">{{ $section['title'] }}</h2>
                <dl @class([
                    'grid gap-6 text-center',
                    'grid-cols-2' => count($stats) >= 2,
                    'lg:grid-cols-3' => count($stats) === 3,
                    'lg:grid-cols-4' => count($stats) === 4,
                ])>
                    @foreach ($stats as $stat)
                        <div class="grid gap-1">
                            <dt class="order-2 text-sm text-ink-muted sm:text-base">{{ $stat['label'] }}</dt>
                            <dd class="order-1 text-3xl font-bold tracking-tight text-ink tabular-nums sm:text-4xl">{{ $stat['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif

            @if ($siteLegal['trade_license'])
                <p @class(['flex items-center justify-center gap-2 text-sm text-ink-muted', 'mt-6 border-t border-line pt-5' => $stats !== []])>
                    <x-icon name="shield" class="size-4 text-accent-ink" />
                    {{ __('site.home.legal_badge', ['number' => $siteLegal['trade_license']]) }}
                </p>
            @endif
        </div>
    </section>
@endif
