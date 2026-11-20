@use('App\Support\Localization')

{{--
    How it works: removes the "what happens after I fill the form?" worry.
    On wide screens a coral line draws itself across the steps when they
    scroll into view (.reveal-line in app.css).
--}}
@php($stepIcons = ['file', 'phone', 'calendar', 'play'])

<x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']">
    <ol class="relative grid gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
        <span aria-hidden="true" class="absolute top-8 right-[12%] left-8 hidden h-0.5 rounded-full bg-line-strong lg:block"></span>
        <span aria-hidden="true" data-reveal class="reveal-line absolute top-8 right-[12%] left-8 hidden h-0.5 rounded-full bg-accent lg:block"></span>

        @foreach (__('site.home.steps') as $index => $step)
            <li data-reveal style="--reveal-index: {{ $index + 1 }}" class="group relative">
                <span class="relative grid size-16 place-items-center rounded-2xl bg-surface-raised text-accent-ink shadow-card ring-1 ring-line transition duration-300 group-hover:-translate-y-1 group-hover:bg-accent group-hover:text-on-accent">
                    <x-icon :name="$stepIcons[$index] ?? 'check'" class="size-7" />
                    <span class="absolute -top-2 -right-2 grid size-7 place-items-center rounded-full bg-ink text-xs font-bold text-canvas">{{ Localization::digits($index + 1) }}</span>
                </span>
                <h3 class="mt-5 text-xl font-bold text-ink">{{ $step['title'] }}</h3>
                <p class="mt-2 text-ink-muted">{{ $step['body'] }}</p>
            </li>
        @endforeach
    </ol>
</x-site.section>
