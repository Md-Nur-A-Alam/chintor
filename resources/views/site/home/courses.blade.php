@use('App\Support\Localization')

@if ($courses->isNotEmpty())
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']" :action-url="Localization::route('courses.index')">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($courses as $course)
                <x-reveal :index="$loop->index"><x-site.course-card :course="$course" /></x-reveal>
            @endforeach
        </div>
    </x-site.section>
@endif
