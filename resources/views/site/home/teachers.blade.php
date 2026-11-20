@use('App\Support\Localization')

@if ($teachers->isNotEmpty())
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']" :action-url="Localization::route('teachers.index')">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($teachers as $teacher)
                <x-site.teacher-card :teacher="$teacher" />
            @endforeach
        </div>
    </x-site.section>
@endif
