@use('App\Support\Localization')

<x-layouts.app :title="$program->translate('name')" :description="$program->translate('tagline')">
    <x-site.page-hero
        :eyebrow="__('site.nav.programs')"
        :title="$program->translate('name')"
        :subtitle="$program->translate('tagline')"
    >
        @if ($program->translate('description'))
            <p class="mt-4 max-w-3xl leading-relaxed text-ink-muted">{{ $program->translate('description') }}</p>
        @endif
        <div class="mt-6 flex flex-wrap gap-3">
            <x-button :href="Localization::route('start')">{{ __('site.cta.start_learning') }} <x-icon name="arrow-right" class="size-4" /></x-button>
            <x-button :href="Localization::route('start', ['assessment' => 1])" variant="secondary">{{ __('site.cta.free_assessment') }}</x-button>
        </div>
    </x-site.page-hero>

    <x-site.section :title="__('site.program.courses_in', ['program' => $program->translate('name')])">
        @if ($courses->isEmpty())
            <div class="flex flex-col items-center gap-4 rounded-card bg-surface p-10 text-center ring-1 ring-line">
                <x-mascot mood="think" class="h-24 w-auto text-ink" />
                <p class="text-ink-muted">{{ __('site.program.no_courses') }}</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $course)
                    <x-site.course-card :course="$course" />
                @endforeach
            </div>
        @endif
    </x-site.section>

    @if ($books->isNotEmpty())
        <x-site.section :title="__('site.home.books_title')">
            <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                @foreach ($books as $book)
                    <x-site.book-card :book="$book" />
                @endforeach
            </div>
        </x-site.section>
    @endif
</x-layouts.app>
