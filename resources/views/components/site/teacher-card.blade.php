@use('App\Support\Localization')

@props([
    'teacher',
])

<a
    href="{{ Localization::route('teachers.show', ['teacher' => $teacher]) }}"
    class="group flex flex-col items-center rounded-card bg-surface p-6 text-center shadow-card ring-1 ring-line transition duration-200 hover:-translate-y-1 hover:ring-line-strong"
>
    <span class="relative block size-28 overflow-hidden rounded-full bg-brand-soft ring-4 ring-surface-muted">
        @if ($teacher->photo_url)
            <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->translate('name') }}" loading="lazy" class="size-full object-cover">
        @else
            <span class="grid size-full place-items-center text-3xl font-bold text-brand">{{ mb_substr((string) $teacher->translate('name'), 0, 1) }}</span>
        @endif
    </span>

    <h3 class="mt-4 text-lg font-bold text-ink group-hover:text-accent-ink">{{ $teacher->translate('name') }}</h3>
    @if ($teacher->translate('designation'))
        <p class="text-sm font-medium text-brand">{{ $teacher->translate('designation') }}</p>
    @endif
    @if ($teacher->translate('summary'))
        <p class="mt-2 line-clamp-3 text-sm text-ink-muted">{{ $teacher->translate('summary') }}</p>
    @endif
</a>
