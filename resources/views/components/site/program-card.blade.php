@use('App\Support\Localization')

@props([
    'program',
])

@php
    $accentClasses = match ($program->accent) {
        'coral' => 'bg-accent-soft text-accent-ink',
        'sun' => 'bg-sun-300/30 text-ink',
        default => 'bg-brand-soft text-brand',
    };
@endphp

<a
    href="{{ Localization::route('programs.show', ['program' => $program]) }}"
    class="group relative flex flex-col overflow-hidden rounded-card bg-surface p-6 shadow-card ring-1 ring-line transition duration-200 hover:-translate-y-1 hover:ring-line-strong"
>
    <span class="grid size-14 place-items-center rounded-2xl {{ $accentClasses }}">
        <x-icon :name="$program->icon ?: 'layers'" class="size-7" />
    </span>

    <h3 class="mt-5 text-xl font-bold text-ink">{{ $program->translate('name') }}</h3>

    @if ($program->translate('tagline'))
        <p class="mt-2 text-ink-muted">{{ $program->translate('tagline') }}</p>
    @endif

    <div class="mt-auto flex items-center justify-between pt-6 text-sm">
        @isset($program->courses_count)
            <span class="text-ink-subtle">{{ __('site.program.course_count', ['count' => Localization::digits($program->courses_count)]) }}</span>
        @endisset
        <span class="inline-flex items-center gap-1 font-semibold text-accent-ink">
            {{ __('site.details') }}
            <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
        </span>
    </div>
</a>
