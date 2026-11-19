@use('App\Support\Localization')
@use('App\Support\Money')

@php
    $learningOutcomes = $course->translate('learning_outcomes') ?? [];
    $highlights = $course->translate('highlights') ?? [];
    $syllabus = $course->translate('syllabus') ?? [];
    $refundParams = [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ];

    $sections = array_filter([
        'about' => true,
        'syllabus' => $syllabus !== [],
        'teachers' => $course->teachers->isNotEmpty(),
        'batches' => true,
        'faq' => $course->faqs->isNotEmpty(),
    ]);

    $stats = array_filter([
        ['icon' => 'users', 'label' => __('site.course.stat_live'), 'value' => $course->live_class_count ? Localization::digits($course->live_class_count) : null],
        ['icon' => 'calendar', 'label' => __('site.course.stat_duration'), 'value' => $course->duration_months ? __('site.months', ['count' => Localization::digits($course->duration_months)]) : null],
        ['icon' => 'clock', 'label' => __('site.course.stat_class_length'), 'value' => $course->class_duration_minutes ? __('site.minutes', ['count' => Localization::digits($course->class_duration_minutes)]) : null],
        ['icon' => 'target', 'label' => __('site.course.stat_level'), 'value' => $course->classRangeLabel()],
    ], fn (array $stat) => filled($stat['value']));

    // Plan options for the enrol box (Alpine reads these).
    $plans = $course->variations->map(fn ($variation) => [
        'id' => $variation->id,
        'label' => $variation->translate('label'),
        'badge' => $variation->translate('badge'),
        'regular' => Money::taka($variation->regular_price),
        'payable' => Money::taka($variation->payablePrice()),
        'hasDiscount' => $variation->hasDiscount(),
    ])->values();
@endphp

<x-layouts.app
    :title="$course->translate('seo_title') ?: $course->translate('title')"
    :description="$course->translate('seo_description') ?: $course->translate('summary')"
    :image="$course->thumbnail_url"
>
    <x-slot:head>
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course->translate('title'),
            'description' => $course->translate('summary'),
            'provider' => ['@type' => 'Organization', 'name' => __('site.brand'), 'sameAs' => Localization::route('home')],
            'inLanguage' => 'bn',
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    </x-slot:head>

    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-line">
        <div aria-hidden="true" class="bg-aurora pointer-events-none absolute inset-0"></div>
        <div class="container-page relative py-10 lg:py-14">
            <nav class="text-sm text-ink-subtle" aria-label="Breadcrumb">
                <a href="{{ Localization::route('programs.show', ['program' => $course->program]) }}" class="font-medium text-accent-ink hover:underline">{{ $course->program->translate('name') }}</a>
            </nav>
            <h1 class="mt-3 max-w-3xl text-4xl font-bold tracking-tight text-ink sm:text-5xl">{{ $course->translate('title') }}</h1>
            @if ($course->translate('subtitle') || $course->translate('summary'))
                <p class="mt-4 max-w-2xl text-lg text-ink-muted">{{ $course->translate('subtitle') ?: $course->translate('summary') }}</p>
            @endif
            <div class="mt-5 flex flex-wrap gap-2">
                @if ($course->classRangeLabel())
                    <x-badge tone="info">{{ $course->classRangeLabel() }}</x-badge>
                @endif
                @if ($course->live_class_count)
                    <x-badge tone="success">{{ __('site.course.live_classes', ['count' => Localization::digits($course->live_class_count)]) }}</x-badge>
                @endif
            </div>
        </div>
    </section>

    {{-- In-page navigation --}}
    <nav class="sticky top-16 z-30 border-b border-line bg-canvas/90 backdrop-blur lg:top-20" aria-label="{{ __('site.course.overview') }}">
        <ul class="container-page flex gap-1 overflow-x-auto py-2 text-sm font-medium">
            @foreach (array_keys($sections) as $section)
                <li><a href="#{{ $section }}" class="block rounded-full px-3 py-1.5 whitespace-nowrap text-ink-muted hover:bg-surface-muted hover:text-ink">{{ __("site.course.nav.{$section}") }}</a></li>
            @endforeach
        </ul>
    </nav>

    <div class="container-page grid gap-10 py-10 lg:grid-cols-[1fr_22rem] lg:items-start">
        <div class="grid min-w-0 gap-12">
            {{-- Explainer video (required before a course can be published) --}}
            <x-youtube :url="$course->explainer_video_url" :title="$course->translate('title')" />

            <section id="about" class="scroll-mt-36 grid gap-8">
                @if ($learningOutcomes !== [])
                    <div>
                        <h2 class="text-2xl font-bold text-ink">{{ __('site.course.what_you_learn') }}</h2>
                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($learningOutcomes as $outcome)
                                <li class="flex gap-3 rounded-2xl bg-surface p-4 ring-1 ring-line">
                                    <span class="grid size-6 shrink-0 place-items-center rounded-full bg-brand-soft text-brand"><x-icon name="check" class="size-4" /></span>
                                    <span class="text-ink">{{ $outcome }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($stats !== [])
                    <div>
                        <h2 class="text-2xl font-bold text-ink">{{ __('site.course.overview') }}</h2>
                        <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($stats as $stat)
                                <div class="rounded-2xl bg-surface p-4 shadow-card ring-1 ring-line">
                                    <x-icon :name="$stat['icon']" class="size-5 text-accent-ink" />
                                    <dd class="mt-2 text-xl font-bold text-ink">{{ $stat['value'] }}</dd>
                                    <dt class="text-sm text-ink-subtle">{{ $stat['label'] }}</dt>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                @if ($course->translate('description'))
                    <div>
                        <h2 class="text-2xl font-bold text-ink">{{ __('site.course.about') }}</h2>
                        <div class="mt-3 max-w-none leading-relaxed text-ink-muted">{!! nl2br(e($course->translate('description'))) !!}</div>
                    </div>
                @endif

                @if ($highlights !== [])
                    <div>
                        <h2 class="text-2xl font-bold text-ink">{{ __('site.course.highlights') }}</h2>
                        <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($highlights as $highlight)
                                <li class="flex items-start gap-2 text-ink"><x-icon name="star" class="mt-1 size-4 text-sun-500" />{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>

            @if ($syllabus !== [])
                <section id="syllabus" class="scroll-mt-36">
                    <h2 class="text-2xl font-bold text-ink">{{ __('site.course.syllabus') }}</h2>
                    <ol class="mt-4 grid gap-3">
                        @foreach ($syllabus as $index => $line)
                            @php([$moduleTitle, $moduleTopics] = array_pad(array_map('trim', explode(':', $line, 2)), 2, null))
                            <li class="flex gap-4 rounded-2xl bg-surface p-4 shadow-card ring-1 ring-line">
                                <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-accent-soft font-bold text-accent-ink">{{ Localization::digits($index + 1) }}</span>
                                <div>
                                    <p class="font-semibold text-ink">{{ $moduleTitle }}</p>
                                    @if ($moduleTopics)
                                        <p class="mt-1 text-sm text-ink-muted">{{ $moduleTopics }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            @if ($course->teachers->isNotEmpty())
                <section id="teachers" class="scroll-mt-36">
                    <h2 class="text-2xl font-bold text-ink">{{ __('site.course.teachers') }}</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @foreach ($course->teachers as $teacher)
                            <x-site.teacher-card :teacher="$teacher" />
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="batches" class="scroll-mt-36">
                <h2 class="text-2xl font-bold text-ink">{{ __('site.course.batches') }}</h2>
                @if ($course->openBatches->isEmpty())
                    <div class="mt-4 rounded-2xl bg-surface p-5 ring-1 ring-line">
                        <p class="text-ink-muted">{{ __('site.course.no_batches') }}</p>
                        <x-site.callback-form form="waitlist" :course-id="$course->id" class="mt-4" />
                    </div>
                @else
                    <ul class="mt-4 grid gap-3">
                        @foreach ($course->openBatches as $batch)
                            <li class="flex flex-col gap-3 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-bold text-ink">{{ $batch->translate('name') ?: $batch->code }}</p>
                                    <p class="mt-1 text-sm text-ink-muted">
                                        {{ $batch->daysLabel() }}
                                        @if ($batch->class_start_time)
                                            · {{ Localization::digits(\Illuminate\Support\Carbon::parse($batch->class_start_time)->format('g:i A')) }}
                                        @endif
                                    </p>
                                    <p class="mt-1 flex flex-wrap gap-x-4 text-sm text-ink-subtle">
                                        @if ($batch->starts_on)
                                            <span>{{ __('site.course.batch_starts', ['date' => Localization::digits($batch->starts_on->translatedFormat('j F'))]) }}</span>
                                        @endif
                                        @if ($batch->enrollment_deadline)
                                            <span>{{ __('site.course.deadline', ['date' => Localization::digits($batch->enrollment_deadline->translatedFormat('j F'))]) }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if ($batch->seatsLeft() !== null)
                                        <x-badge :tone="$batch->seatsLeft() > 0 ? 'warning' : 'danger'">
                                            {{ $batch->seatsLeft() > 0 ? __('site.course.seats_left', ['count' => Localization::digits($batch->seatsLeft())]) : __('site.course.seats_full') }}
                                        </x-badge>
                                    @endif
                                    @if ($batch->isOpenForEnrollment() && $course->variations->isNotEmpty())
                                        <x-button :href="Localization::route('enrollments.create', ['course' => $course, 'batch' => $batch->id])" size="sm">{{ __('site.cta.enroll_now') }}</x-button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            @if ($course->faqs->isNotEmpty())
                <section id="faq" class="scroll-mt-36">
                    <h2 class="text-2xl font-bold text-ink">{{ __('site.course.faq') }}</h2>
                    <x-site.faq-list :faqs="$course->faqs" class="mt-4" />
                </section>
            @endif

            @if ($testimonials->isNotEmpty())
                <section>
                    <h2 class="text-2xl font-bold text-ink">{{ __('site.home.testimonials_title') }}</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @foreach ($testimonials as $testimonial)
                            <figure class="rounded-card bg-surface p-5 shadow-card ring-1 ring-line">
                                <blockquote class="text-ink">“{{ $testimonial->translate('body') }}”</blockquote>
                                <figcaption class="mt-3 text-sm font-semibold text-ink">{{ $testimonial->author_name }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Sticky enrol box --}}
        <aside class="lg:sticky lg:top-40">
            <div
                x-data="{ plans: @js($plans), selected: @js($plans->first()['id'] ?? null), get plan() { return this.plans.find(p => p.id === this.selected) } }"
                class="rounded-card bg-surface-raised p-5 shadow-card ring-1 ring-line"
            >
                @if ($plans->isNotEmpty())
                    <p class="text-sm font-semibold text-ink-subtle">{{ __('site.course.choose_plan') }}</p>
                    <div class="mt-3 grid gap-2" role="radiogroup">
                        @foreach ($course->variations as $variation)
                            <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl p-3 ring-1 ring-line ring-inset has-[:checked]:bg-accent-soft has-[:checked]:ring-accent-ink/40">
                                <span class="flex items-center gap-2.5">
                                    <input type="radio" name="plan" value="{{ $variation->id }}" x-model.number="selected" class="size-4 accent-[var(--accent)]">
                                    <span class="font-medium text-ink">{{ $variation->translate('label') }}</span>
                                    @if ($variation->translate('badge'))
                                        <x-badge tone="warning">{{ $variation->translate('badge') }}</x-badge>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-accent-ink" x-text="plan.payable">{{ $plans->first()['payable'] }}</span>
                        <del class="text-ink-subtle opacity-70" x-show="plan.hasDiscount" x-text="plan.regular">{{ $plans->first()['hasDiscount'] ? $plans->first()['regular'] : '' }}</del>
                    </div>

                    <a
                        x-bind:href="@js(Localization::route('enrollments.create', ['course' => $course])) + '?plan=' + selected"
                        href="{{ Localization::route('enrollments.create', ['course' => $course]) }}"
                        class="mt-4 inline-flex h-13 w-full cursor-pointer items-center justify-center gap-2 rounded-full bg-accent text-lg font-semibold text-on-accent shadow-glow transition hover:bg-accent-hover"
                    >{{ __('site.cta.enroll_now') }} <x-icon name="arrow-right" class="size-5" /></a>
                @else
                    <x-button :href="Localization::route('start', ['course' => $course->id])" size="lg" class="w-full">{{ __('site.cta.start_learning') }}</x-button>
                @endif

                <x-button :href="Localization::route('start', ['course' => $course->id, 'assessment' => 1])" variant="secondary" class="mt-2 w-full">{{ __('site.cta.free_assessment') }}</x-button>

                <ul class="mt-5 grid gap-2 border-t border-line pt-4 text-sm text-ink-muted">
                    <li class="flex items-center gap-2"><x-icon name="shield" class="size-4 text-brand" />{{ __('site.course.refund_line', $refundParams) }}</li>
                    <li class="flex items-center gap-2"><x-icon name="target" class="size-4 text-brand" />{{ __('site.course.assessment_line') }}</li>
                </ul>
            </div>

            <div class="mt-4 rounded-card bg-surface p-5 ring-1 ring-line">
                <p class="font-semibold text-ink">{{ __('site.course.not_ready') }}</p>
                <x-site.callback-form :course-id="$course->id" :compact="true" class="mt-3" />
            </div>
        </aside>
    </div>
</x-layouts.app>
