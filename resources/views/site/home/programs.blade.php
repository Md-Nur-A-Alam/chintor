@use('App\Support\Localization')

{{-- Programs (fully admin-managed). --}}
@if ($programs->isNotEmpty())
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']" :action-url="Localization::route('programs.index')">
        {{-- Full class names so Tailwind can see them. --}}
        <div @class([
            'grid gap-5 sm:grid-cols-2',
            'lg:grid-cols-3' => $programs->count() === 3,
            'lg:grid-cols-4' => $programs->count() >= 4,
        ])>
            @foreach ($programs as $program)
                <x-reveal :index="$loop->index"><x-site.program-card :program="$program" /></x-reveal>
            @endforeach
        </div>
    </x-site.section>
@endif
