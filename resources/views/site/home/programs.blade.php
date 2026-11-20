@use('App\Support\Localization')

{{--
    Programs as large brand-coloured tiles (colour from the program's
    "accent" in the admin panel). Hover lifts the tile, spins the arrow and
    grows the background icon. Full class names so Tailwind can see them.
--}}
@if ($programs->isNotEmpty())
    <x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']" :action-url="Localization::route('programs.index')">
        <div @class([
            'grid gap-5 sm:grid-cols-2',
            'lg:grid-cols-3' => $programs->count() >= 3,
        ])>
            @foreach ($programs as $program)
                @php
                    $tone = match ($program->accent) {
                        'coral' => ['tile' => 'from-coral-600 to-coral-800 text-white', 'icon' => 'bg-white/15', 'muted' => 'text-white/80', 'chip' => 'bg-white/15'],
                        'sun' => ['tile' => 'from-sun-300 to-sun-400 text-teal-950', 'icon' => 'bg-teal-950/10', 'muted' => 'text-teal-950/75', 'chip' => 'bg-teal-950/10'],
                        default => ['tile' => 'from-teal-700 to-teal-900 text-white', 'icon' => 'bg-white/15', 'muted' => 'text-white/80', 'chip' => 'bg-white/15'],
                    };
                @endphp
                <x-reveal :index="$loop->index">
                    <a
                        href="{{ Localization::route('programs.show', ['program' => $program]) }}"
                        class="group relative isolate flex min-h-72 flex-col overflow-hidden rounded-[2rem] bg-gradient-to-br p-7 shadow-card transition duration-300 hover:-translate-y-1.5 hover:shadow-glow {{ $tone['tile'] }}"
                    >
                        <x-icon :name="$program->icon ?: 'layers'" class="absolute -right-6 -bottom-6 -z-10 size-44 opacity-10 transition duration-500 group-hover:scale-110 group-hover:-rotate-6" />

                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-14 place-items-center rounded-2xl {{ $tone['icon'] }}">
                                <x-icon :name="$program->icon ?: 'layers'" class="size-7" />
                            </span>
                            <span class="grid size-11 place-items-center rounded-full {{ $tone['chip'] }} transition duration-300 group-hover:-rotate-45">
                                <x-icon name="arrow-right" class="size-5" />
                            </span>
                        </div>

                        <h3 class="mt-auto pt-10 text-2xl font-bold">{{ $program->translate('name') }}</h3>
                        @if ($program->translate('tagline'))
                            <p class="mt-2 {{ $tone['muted'] }}">{{ $program->translate('tagline') }}</p>
                        @endif
                        <span class="mt-5 inline-flex w-fit items-center rounded-full px-3 py-1 text-sm font-semibold {{ $tone['chip'] }}">
                            {{ __('site.program.course_count', ['count' => Localization::digits($program->courses_count)]) }}
                        </span>
                    </a>
                </x-reveal>
            @endforeach
        </div>
    </x-site.section>
@endif
