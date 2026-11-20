@use('App\Support\Localization')

{{-- How it works: removes the "what happens after I fill the form?" worry. --}}
<section class="border-y border-line bg-surface">
    <div class="container-page py-12 lg:py-16">
        <h2 class="text-3xl font-bold tracking-tight text-ink sm:text-4xl">{{ $section['title'] }}</h2>
        @if ($section['subtitle'])
            <p class="mt-2 max-w-2xl text-lg text-ink-muted">{{ $section['subtitle'] }}</p>
        @endif
        <ol class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (__('site.home.steps') as $index => $step)
                <li class="relative">
                    <span class="grid size-12 place-items-center rounded-2xl bg-accent text-lg font-bold text-on-accent shadow-glow">
                        {{ Localization::digits($index + 1) }}
                    </span>
                    <h3 class="mt-4 text-lg font-bold text-ink">{{ $step['title'] }}</h3>
                    <p class="mt-1 text-ink-muted">{{ $step['body'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
