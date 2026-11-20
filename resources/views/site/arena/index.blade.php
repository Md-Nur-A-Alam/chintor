@use('App\Support\Localization')

{{--
    Exam Arena home: ready-made exams, and a custom practice builder. The
    topic list follows the chosen subject (Alpine); without JavaScript every
    topic shows and the server only accepts topics of the chosen subject.
--}}
@php
    $topicsBySubject = $subjects->mapWithKeys(fn ($subject) => [
        $subject->id => $subject->children->map(fn ($topic) => ['id' => $topic->id, 'name' => $topic->translate('name')])->values(),
    ]);
@endphp

<x-layouts.app :title="__('site.arena.title')" :description="__('site.arena.subtitle')">
    <x-site.page-hero :title="__('site.arena.title')" :subtitle="__('site.arena.subtitle')" />

    <div class="container-page grid gap-12 py-12 lg:grid-cols-[1.1fr_0.9fr] lg:py-16">
        {{-- Ready-made exams --}}
        <section aria-labelledby="ready-exams">
            <h2 id="ready-exams" class="text-2xl font-bold text-ink sm:text-3xl">{{ __('site.arena.ready_exams') }}</h2>

            @if ($exams->isEmpty())
                <p class="mt-6 rounded-card bg-surface p-8 text-ink-muted ring-1 ring-line">{{ __('site.arena.no_exams') }}</p>
            @else
                <div class="mt-6 grid gap-4">
                    @foreach ($exams as $exam)
                        <x-reveal :index="$loop->index">
                            <a href="{{ Localization::route('arena.exams.show', ['exam' => $exam]) }}" class="group flex items-center gap-5 rounded-[1.5rem] bg-surface p-5 shadow-card ring-1 ring-line transition duration-200 hover:-translate-y-0.5 hover:ring-line-strong">
                                <span class="grid size-14 shrink-0 place-items-center rounded-2xl bg-accent-soft text-accent-ink transition group-hover:scale-105"><x-icon name="target" class="size-7" /></span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-lg font-bold text-ink">{{ $exam->translate('title') }}</span>
                                    <span class="mt-1 block text-sm text-ink-muted">
                                        {{ __('site.arena.exam_meta', ['count' => Localization::digits($exam->question_count), 'minutes' => Localization::digits($exam->duration_minutes)]) }}
                                        @if ($exam->results_at && $exam->results_at->isFuture())
                                            · {{ __('site.arena.results_at', ['date' => Localization::digits($exam->results_at->translatedFormat('j F, g:i A'))]) }}
                                        @endif
                                    </span>
                                </span>
                                <x-icon name="arrow-right" class="size-5 shrink-0 text-accent-ink transition-transform group-hover:translate-x-1" />
                            </a>
                        </x-reveal>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Custom practice --}}
        <section aria-labelledby="practice" class="lg:sticky lg:top-28 lg:self-start">
            <div class="rounded-[2rem] bg-surface p-6 shadow-card ring-1 ring-line sm:p-8">
                <h2 id="practice" class="text-2xl font-bold text-ink">{{ __('site.arena.practice_title') }}</h2>
                <p class="mt-2 text-ink-muted">{{ __('site.arena.practice_body', ['minutes' => Localization::digits($maxMinutesPerQuestion)]) }}</p>

                @if ($subjects->isEmpty())
                    <p class="mt-6 rounded-2xl bg-surface-muted p-5 text-ink-muted">{{ __('site.arena.no_subjects') }}</p>
                @else
                    <form
                        method="POST"
                        action="{{ Localization::route('arena.practice') }}"
                        class="mt-6 grid gap-5"
                        x-data="{
                            subject: @js((string) old('subject_id', '')),
                            topics: @js($topicsBySubject),
                            count: @js((int) old('question_count', 10)),
                            minutes: @js((int) old('duration_minutes', 10)),
                            get maxMinutes() { return Math.max(1, (parseInt(this.count) || 1) * {{ (int) $maxMinutesPerQuestion }}) },
                        }"
                        x-effect="if (minutes > maxMinutes) minutes = maxMinutes"
                    >
                        @csrf
                        <div class="grid content-start gap-1.5">
                            <label for="practice-subject" class="text-sm font-medium text-ink">{{ __('site.arena.subject') }} <span class="text-accent-ink" aria-hidden="true">*</span></label>
                            <select id="practice-subject" name="subject_id" x-model="subject" required class="h-11 rounded-xl bg-surface px-3 text-ink ring-1 ring-line-strong ring-inset focus:ring-2 focus:ring-focus">
                                <option value="">{{ __('site.arena.choose_subject') }}</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>{{ $subject->translate('name') }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
                        </div>

                        <fieldset x-show="(topics[subject] || []).length > 0" x-cloak class="grid gap-2">
                            <legend class="text-sm font-medium text-ink">{{ __('site.arena.topics') }}</legend>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="topic in (topics[subject] || [])" :key="topic.id">
                                    <label class="inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-full bg-surface-muted px-4 text-sm font-medium text-ink ring-1 ring-line has-[:checked]:bg-accent-soft has-[:checked]:text-accent-ink has-[:checked]:ring-accent">
                                        <input type="checkbox" name="topic_ids[]" x-bind:value="topic.id" class="size-4 accent-[var(--accent)]">
                                        <span x-text="topic.name"></span>
                                    </label>
                                </template>
                            </div>
                        </fieldset>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="grid content-start gap-1.5">
                                <label for="practice-class" class="text-sm font-medium text-ink">{{ __('site.arena.class_level') }}</label>
                                <select id="practice-class" name="class_level" class="h-11 rounded-xl bg-surface px-3 text-ink ring-1 ring-line-strong ring-inset">
                                    <option value="">{{ __('site.arena.any_class') }}</option>
                                    @foreach (range(1, 12) as $class)
                                        <option value="{{ $class }}" @selected((string) old('class_level') === (string) $class)>{{ __('site.arena.class_option', ['class' => Localization::digits($class)]) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid content-start gap-1.5">
                                <label for="practice-count" class="text-sm font-medium text-ink">{{ __('site.arena.question_count') }}</label>
                                <input id="practice-count" type="number" name="question_count" min="1" max="{{ $maxQuestions }}" x-model.number="count" required class="h-11 rounded-xl bg-surface px-3 text-ink ring-1 ring-line-strong ring-inset">
                                @error('question_count') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid content-start gap-1.5">
                                <label for="practice-minutes" class="text-sm font-medium text-ink">{{ __('site.arena.minutes') }}</label>
                                <input id="practice-minutes" type="number" name="duration_minutes" min="1" x-bind:max="maxMinutes" x-model.number="minutes" required class="h-11 rounded-xl bg-surface px-3 text-ink ring-1 ring-line-strong ring-inset">
                                <p class="text-xs text-ink-subtle" x-text="@js(__('site.arena.max_minutes_hint', ['max' => '__MAX__'])).replace('__MAX__', maxMinutes)"></p>
                                @error('duration_minutes') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @include('site.arena.partials.student-fields')

                        <x-button type="submit" size="lg" class="w-full">{{ __('site.arena.start') }} <x-icon name="arrow-right" class="size-5" /></x-button>
                    </form>
                @endif
            </div>
        </section>
    </div>
</x-layouts.app>
