<x-layouts.app :title="__('site.teacher.index_title')" :description="__('site.teacher.index_subtitle')">
    <x-site.page-hero :title="__('site.teacher.index_title')" :subtitle="__('site.teacher.index_subtitle')" />

    <div class="container-page py-12">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($teachers as $teacher)
                <x-site.teacher-card :teacher="$teacher" />
            @endforeach
        </div>
    </div>
</x-layouts.app>
