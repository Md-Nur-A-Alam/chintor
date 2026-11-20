@use('App\Support\Localization')

{{-- A ready-made exam: what it covers, the rules, and the start form. --}}
<x-layouts.app :title="$exam->translate('title')" :description="__('site.arena.subtitle')">
    <x-site.page-hero :eyebrow="__('site.arena.title')" :title="$exam->translate('title')" :subtitle="$exam->translate('description')">
        <p class="mt-6 flex flex-wrap gap-2 text-sm font-semibold">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-raised px-3 py-1.5 text-ink ring-1 ring-line"><x-icon name="file" class="size-4 text-accent-ink" /> {{ __('site.arena.exam_meta', ['count' => Localization::digits($exam->question_count), 'minutes' => Localization::digits($exam->duration_minutes)]) }}</span>
            @if ($exam->results_at && $exam->results_at->isFuture())
                <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-raised px-3 py-1.5 text-ink ring-1 ring-line"><x-icon name="calendar" class="size-4 text-accent-ink" /> {{ __('site.arena.results_at', ['date' => Localization::digits($exam->results_at->translatedFormat('j F, g:i A'))]) }}</span>
            @endif
        </p>
    </x-site.page-hero>

    <div class="container-page grid gap-10 py-12 lg:grid-cols-2 lg:py-16">
        <section>
            <h2 class="text-2xl font-bold text-ink">{{ __('site.arena.rules_title') }}</h2>
            <ul class="mt-5 grid gap-4">
                @foreach (__('site.arena.rules') as $rule)
                    <li class="flex gap-3 text-ink-muted">
                        <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full bg-accent-soft text-accent-ink"><x-icon name="check" class="size-4" /></span>
                        {{ $rule }}
                    </li>
                @endforeach
            </ul>
        </section>

        <form method="POST" action="{{ Localization::route('arena.exams.start', ['exam' => $exam]) }}" class="grid gap-5 rounded-[2rem] bg-surface p-6 shadow-card ring-1 ring-line sm:p-8">
            @csrf
            @error('exam') <p class="rounded-xl bg-accent-soft p-3 text-sm text-accent-ink">{{ $message }}</p> @enderror
            @include('site.arena.partials.student-fields')
            <x-button type="submit" size="lg" class="w-full">{{ __('site.arena.start') }} <x-icon name="arrow-right" class="size-5" /></x-button>
        </form>
    </div>
</x-layouts.app>
