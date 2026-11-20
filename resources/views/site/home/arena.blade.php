@use('App\Support\Localization')

{{--
    Exam Arena invitation: a mock exam card that shows what a sitting looks
    like (decorative), beside the pitch and the button.
--}}
@php
    $isBangla = app()->getLocale() === 'bn';
    $sample = $isBangla
        ? ['question' => 'একটি ত্রিভুজের দুটি কোণ ৫০° ও ৬০° হলে তৃতীয় কোণ কত?', 'options' => ['৬০°', '৭০°', '৮০°', '৯০°'], 'labels' => ['ক', 'খ', 'গ', 'ঘ']]
        : ['question' => 'Two angles of a triangle are 50° and 60°. What is the third?', 'options' => ['60°', '70°', '80°', '90°'], 'labels' => ['A', 'B', 'C', 'D']];
@endphp

<x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']">
    <div class="grid items-center gap-10 lg:grid-cols-2">
        <div data-reveal>
            <ul class="grid gap-4">
                @foreach (__('site.arena.rules') as $rule)
                    <li class="flex gap-3 text-lg text-ink-muted">
                        <span class="mt-1 grid size-7 shrink-0 place-items-center rounded-full bg-accent-soft text-accent-ink"><x-icon name="check" class="size-4" /></span>
                        {{ $rule }}
                    </li>
                @endforeach
            </ul>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <x-button :href="Localization::route('arena.index')" size="lg" class="group">
                    {{ __('site.arena.start') }}
                    <x-icon name="arrow-right" class="size-5 transition-transform group-hover:translate-x-1" />
                </x-button>
                <x-button :href="Localization::route('learn.index')" variant="secondary" size="lg">{{ __('site.nav.learn') }}</x-button>
            </div>
        </div>

        <div data-reveal style="--reveal-index: 1" aria-hidden="true" class="relative mx-auto w-full max-w-md">
            <div class="absolute -inset-4 rounded-[2.5rem] bg-[conic-gradient(from_160deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] blur-2xl"></div>
            <div class="relative rounded-[2rem] bg-surface p-6 shadow-card ring-1 ring-line">
                <div class="flex items-center justify-between text-sm font-semibold">
                    <span class="rounded-full bg-brand-soft px-3 py-1 text-brand">{{ __('site.arena.question_number', ['number' => Localization::digits(3)]) }}</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-accent-soft px-3 py-1 text-accent-ink tabular-nums"><x-icon name="clock" class="size-4" /> {{ Localization::digits('04:12') }}</span>
                </div>
                <p class="mt-5 text-lg font-bold text-ink">{{ $sample['question'] }}</p>
                <div class="mt-4 grid gap-2.5">
                    @foreach ($sample['options'] as $index => $option)
                        <span @class([
                            'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold ring-1',
                            'bg-brand-soft text-brand ring-brand/40' => $index === 1,
                            'bg-surface-muted text-ink-muted ring-line' => $index !== 1,
                        ])>
                            <span @class(['grid size-6 place-items-center rounded-full text-xs', 'bg-brand text-brand-ink' => $index === 1, 'bg-surface ring-1 ring-line' => $index !== 1])>{{ $sample['labels'][$index] }}</span>
                            {{ $option }}
                        </span>
                    @endforeach
                </div>
                <div class="mt-5 h-2 overflow-hidden rounded-full bg-surface-muted"><div class="h-full w-3/10 rounded-full bg-accent"></div></div>
            </div>
        </div>
    </div>
</x-site.section>
