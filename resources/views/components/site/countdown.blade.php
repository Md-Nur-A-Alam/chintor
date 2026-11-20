@use('App\Support\Localization')

@props([
    'endsAt',
])

{{--
    "Ends in 3 days 4 hrs 12 min" for a campaign deadline. The server
    renders the deadline date; Alpine replaces it with a live countdown
    (updated every 30 s) and hides it once the deadline passes.
--}}
@php
    $units = __('site.home.campaign.units');
    $banglaDigits = app()->getLocale() === 'bn';
@endphp

<span
    x-data="{
        endsAt: {{ $endsAt->getTimestamp() * 1000 }},
        text: '',
        timer: null,
        digits(value) {
            const text = String(value);
            return @js($banglaDigits) ? text.replace(/[0-9]/g, (digit) => '০১২৩৪৫৬৭৮৯'[digit]) : text;
        },
        tick() {
            const minutesLeft = Math.floor((this.endsAt - Date.now()) / 60000);
            if (minutesLeft < 0) { this.text = ''; clearInterval(this.timer); return; }
            const days = Math.floor(minutesLeft / 1440);
            const hours = Math.floor((minutesLeft % 1440) / 60);
            const minutes = minutesLeft % 60;
            const parts = [];
            if (days > 0) parts.push(this.digits(days) + ' ' + @js($units['days']));
            if (days > 0 || hours > 0) parts.push(this.digits(hours) + ' ' + @js($units['hours']));
            parts.push(this.digits(minutes) + ' ' + @js($units['minutes']));
            this.text = @js(__('site.home.campaign.ends_in')) + ' ' + parts.join(' ');
        },
        init() { this.tick(); this.timer = setInterval(() => this.tick(), 30000); },
        destroy() { clearInterval(this.timer); },
    }"
    x-show="text !== ''"
    {{ $attributes->class(['inline-flex items-center gap-1.5 tabular-nums']) }}
>
    <x-icon name="clock" class="size-4" />
    <span x-text="text">{{ __('site.home.campaign.ends_on', ['date' => Localization::digits($endsAt->translatedFormat('j F, g:i A'))]) }}</span>
</span>
