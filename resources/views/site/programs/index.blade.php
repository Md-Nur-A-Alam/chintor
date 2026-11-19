<x-layouts.app :title="__('site.program.index_title')" :description="__('site.program.index_subtitle')">
    <x-site.page-hero :title="__('site.program.index_title')" :subtitle="__('site.program.index_subtitle')" />

    <div class="container-page py-12">
        @if ($programs->isEmpty())
            <p class="py-16 text-center text-ink-subtle">{{ __('site.program.no_courses') }}</p>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($programs as $program)
                    <x-site.program-card :program="$program" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
