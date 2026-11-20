@use('App\Support\Localization')

{{--
    Course explorer: program tabs filter the grid in place (no reload).
    Without JavaScript every course simply shows. Tabs only appear when the
    courses come from more than one program.
--}}
@if ($courses->isNotEmpty())
    @php($coursePrograms = $courses->pluck('program')->filter()->unique('id')->values())

    <x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :action-url="Localization::route('courses.index')" :band="$section['band']">
        <div x-data="{ active: 'all' }">
            @if ($coursePrograms->count() > 1)
                <div data-reveal role="tablist" aria-label="{{ __('site.home.explorer.label') }}" class="-mx-4 mb-8 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:px-0">
                    @foreach (['all' => __('site.home.explorer.all')] + $coursePrograms->mapWithKeys(fn ($program) => ['p'.$program->id => $program->translate('name')])->all() as $key => $label)
                        <button
                            type="button"
                            role="tab"
                            x-on:click="active = @js($key)"
                            x-bind:aria-selected="active === @js($key)"
                            x-bind:class="active === @js($key) ? 'bg-ink text-canvas shadow-card' : 'bg-surface text-ink-muted ring-1 ring-line hover:text-ink hover:ring-line-strong'"
                            class="inline-flex min-h-11 shrink-0 cursor-pointer items-center rounded-full px-5 text-sm font-semibold whitespace-nowrap transition duration-200"
                        >{{ $label }}</button>
                    @endforeach
                </div>
            @endif

            {{-- Four or eight courses fill rows of four; anything else uses rows of three, so no card sits alone. --}}
            <div role="tabpanel" @class([
                'grid gap-6 sm:grid-cols-2',
                'xl:grid-cols-4' => $courses->count() % 4 === 0,
                'lg:grid-cols-3' => $courses->count() % 4 !== 0,
            ])>
                @foreach ($courses as $course)
                    <div
                        x-show="active === 'all' || active === @js('p'.$course->program_id)"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="grid"
                    >
                        <x-reveal :index="$loop->index"><x-site.course-card :course="$course" /></x-reveal>
                    </div>
                @endforeach
            </div>
        </div>
    </x-site.section>
@endif
