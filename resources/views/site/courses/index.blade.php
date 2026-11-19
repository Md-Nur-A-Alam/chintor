@use('App\Support\Localization')

@php
    $classOptions = collect(range(1, 12))->mapWithKeys(fn (int $class) => [$class => __('site.class_n', ['class' => Localization::digits($class)])]);
@endphp

<x-layouts.app :title="__('site.course.index_title')" :description="__('site.course.index_subtitle')">
    <x-site.page-hero :title="__('site.course.index_title')" :subtitle="__('site.course.index_subtitle')">
        <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="sm:w-64">
                <x-form.select
                    name="program"
                    :label="__('site.nav.programs')"
                    :value="$selectedProgram"
                    :placeholder="__('site.course.all_programs')"
                    :options="$programs->mapWithKeys(fn ($program) => [$program->slug => $program->translate('name')])->all()"
                />
            </div>
            <div class="sm:w-48">
                <x-form.select name="class" :label="__('site.course.stat_level')" :value="$selectedClass" :placeholder="__('site.course.any_class')" :options="$classOptions->all()" />
            </div>
            <x-button type="submit" variant="brand">{{ __('site.course.filter') }}</x-button>
        </form>
    </x-site.page-hero>

    <div class="container-page py-12">
        @if ($courses->isEmpty())
            <div class="flex flex-col items-center gap-4 py-12 text-center">
                <x-mascot mood="think" class="h-24 w-auto text-ink" />
                <p class="text-ink-muted">{{ __('site.course.none_found') }}</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $course)
                    <x-site.course-card :course="$course" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
