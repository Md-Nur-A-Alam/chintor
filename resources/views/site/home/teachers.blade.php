@use('App\Support\Localization')

{{--
    Teachers in a swipeable row (scroll-snap, works without JavaScript)
    with previous/next buttons for mouse and keyboard users. On wide
    screens four fit, so the buttons only show when there are more.
--}}
@if ($teachers->isNotEmpty())
    <x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']" :action-url="Localization::route('teachers.index')">
        <div x-data="{ scroll(direction) { this.$refs.track.scrollBy({ left: direction * this.$refs.track.clientWidth * 0.8, behavior: 'smooth' }) } }">
            {{-- Centred only when all fit: centring an overflowing row would cut off its start. --}}
            <div x-ref="track" @class([
                '-mx-4 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-4 pb-4 [scrollbar-width:none] sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0',
                'lg:justify-center' => $teachers->count() <= 4,
            ])>
                @foreach ($teachers as $teacher)
                    <div class="w-[78%] shrink-0 snap-start sm:w-[45%] lg:w-[calc((100%-3.75rem)/4)]">
                        <x-reveal :index="$loop->index" class="h-full"><x-site.teacher-card :teacher="$teacher" /></x-reveal>
                    </div>
                @endforeach
            </div>

            @if ($teachers->count() > 1)
                <div @class(['mt-4 flex justify-end gap-2', 'lg:hidden' => $teachers->count() <= 4])>
                    <button type="button" x-on:click="scroll(-1)" class="grid size-11 cursor-pointer place-items-center rounded-full bg-surface-raised text-ink shadow-card ring-1 ring-line transition hover:ring-line-strong" aria-label="{{ __('site.home.teachers_previous') }}">
                        <x-icon name="chevron-left" class="size-5" />
                    </button>
                    <button type="button" x-on:click="scroll(1)" class="grid size-11 cursor-pointer place-items-center rounded-full bg-surface-raised text-ink shadow-card ring-1 ring-line transition hover:ring-line-strong" aria-label="{{ __('site.home.teachers_next') }}">
                        <x-icon name="chevron-right" class="size-5" />
                    </button>
                </div>
            @endif
        </div>
    </x-site.section>
@endif
