@use('App\Support\Localization')

@php
    $refundParams = [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ];

    $pillars = [
        'live' => 'users',
        'assessment' => 'target',
        'refund' => 'refund',
    ];
@endphp

<x-layouts.app>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="bg-aurora pointer-events-none absolute inset-0"></div>
        <div aria-hidden="true" class="bg-dots pointer-events-none absolute inset-0"></div>

        <div class="container-page relative grid gap-12 py-14 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:py-24">
            <div>
                <p class="inline-flex items-center gap-2 rounded-full bg-brand-soft px-3 py-1 text-sm font-medium text-brand ring-1 ring-brand/20 ring-inset">
                    <span class="size-2 rounded-full bg-sun-400 shadow-[0_0_10px_var(--color-sun-400)]"></span>
                    {{ __('site.home.eyebrow') }}
                </p>

                <h1 class="mt-5 text-4xl leading-tight font-bold tracking-tight text-ink sm:text-5xl lg:text-6xl">
                    {{ __('site.home.headline') }}
                </h1>

                <p class="mt-5 max-w-xl text-lg text-ink-muted sm:text-xl">{{ __('site.home.subheadline') }}</p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-button :href="Localization::route('start')" size="lg">
                        {{ __('site.cta.start_learning') }}
                        <x-icon name="arrow-right" class="size-5" />
                    </x-button>
                    <x-button :href="Localization::route('start', ['assessment' => 1])" variant="secondary" size="lg">
                        {{ __('site.cta.free_assessment') }}
                    </x-button>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-md">
                <div aria-hidden="true" class="absolute -inset-4 rounded-[3rem] bg-[conic-gradient(from_200deg,var(--aurora-teal),var(--aurora-coral),var(--aurora-teal))] blur-2xl"></div>
                <div class="relative aspect-square rounded-[2.5rem] bg-surface-raised p-10 shadow-card ring-1 ring-line">
                    <x-logo variant="stacked" class="h-full w-full" />
                </div>
                <x-mascot mood="wave" class="absolute -bottom-6 -left-6 h-28 w-auto text-ink drop-shadow-xl sm:h-32" />

                {{-- Truthful trust chips (policies, not invented numbers). --}}
                <ul class="absolute -right-3 top-8 grid gap-2 text-sm font-semibold sm:-right-8">
                    @foreach (['live' => 'users', 'assessment' => 'target', 'refund' => 'shield'] as $chip => $icon)
                        <li class="flex items-center gap-2 rounded-full bg-surface-raised px-3 py-2 text-ink shadow-card ring-1 ring-line">
                            <x-icon :name="$icon" class="size-4 text-accent-ink" />
                            {{ __("site.home.chips.{$chip}", $refundParams) }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Programs (fully admin-managed) --}}
    @if ($programs->isNotEmpty())
        <x-site.section
            :title="__('site.home.programs_title')"
            :subtitle="__('site.home.programs_subtitle')"
            :action-url="Localization::route('programs.index')"
        >
            {{-- Full class names so Tailwind can see them. --}}
            <div @class([
                'grid gap-5 sm:grid-cols-2',
                'lg:grid-cols-3' => $programs->count() === 3,
                'lg:grid-cols-4' => $programs->count() >= 4,
            ])>
                @foreach ($programs as $program)
                    <x-site.program-card :program="$program" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- Courses --}}
    @if ($courses->isNotEmpty())
        <x-site.section
            :title="__('site.home.courses_title')"
            :subtitle="__('site.home.courses_subtitle')"
            :action-url="Localization::route('courses.index')"
        >
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $course)
                    <x-site.course-card :course="$course" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- How it works --}}
    <section class="border-y border-line bg-surface">
        <div class="container-page py-12 lg:py-16">
            <h2 class="text-3xl font-bold tracking-tight text-ink sm:text-4xl">{{ __('site.home.steps_title') }}</h2>
            <ol class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (__('site.home.steps') as $index => $step)
                    <li class="relative">
                        <span class="grid size-12 place-items-center rounded-2xl bg-accent text-lg font-bold text-on-accent shadow-glow">
                            {{ Localization::digits($index + 1) }}
                        </span>
                        <h3 class="mt-4 text-lg font-bold text-ink">{{ $step['title'] }}</h3>
                        <p class="mt-1 text-ink-muted">{{ $step['body'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Why Chintor --}}
    <x-site.section :title="__('site.home.why_title')">
        <ul class="grid gap-4 md:grid-cols-3">
            @foreach ($pillars as $key => $icon)
                <li class="rounded-card bg-surface p-6 shadow-card ring-1 ring-line transition duration-200 hover:-translate-y-0.5 hover:ring-line-strong">
                    <span class="grid size-12 place-items-center rounded-2xl bg-accent-soft text-accent-ink ring-1 ring-accent-ink/15 ring-inset">
                        <x-icon :name="$icon" class="size-6" />
                    </span>
                    <h3 class="mt-4 text-xl font-semibold text-ink">{{ __("site.home.pillars.{$key}.title", $refundParams) }}</h3>
                    <p class="mt-2 text-ink-muted">{{ __("site.home.pillars.{$key}.body", $refundParams) }}</p>
                </li>
            @endforeach
        </ul>
    </x-site.section>

    {{-- Teachers --}}
    @if ($teachers->isNotEmpty())
        <x-site.section :title="__('site.home.teachers_title')" :action-url="Localization::route('teachers.index')">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($teachers as $teacher)
                    <x-site.teacher-card :teacher="$teacher" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- Books --}}
    @if ($books->isNotEmpty())
        <x-site.section
            :title="__('site.home.books_title')"
            :subtitle="__('site.home.books_subtitle')"
            :action-url="Localization::route('books.index')"
        >
            <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                @foreach ($books as $book)
                    <x-site.book-card :book="$book" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- Testimonials: only real, consented reviews --}}
    @if ($testimonials->isNotEmpty())
        <x-site.section :title="__('site.home.testimonials_title')">
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <figure class="flex flex-col rounded-card bg-surface p-6 shadow-card ring-1 ring-line">
                        @if ($testimonial->rating)
                            <div class="flex gap-0.5 text-sun-500" aria-label="{{ $testimonial->rating }}/5">
                                @for ($star = 1; $star <= 5; $star++)
                                    <x-icon name="star" @class(['size-4', 'fill-current' => $star <= $testimonial->rating]) />
                                @endfor
                            </div>
                        @endif
                        <blockquote class="mt-3 flex-1 leading-relaxed text-ink">“{{ $testimonial->translate('body') }}”</blockquote>
                        <figcaption class="mt-4 text-sm">
                            <span class="font-semibold text-ink">{{ $testimonial->author_name }}</span>
                            @if ($testimonial->translate('author_role'))
                                <span class="text-ink-subtle"> · {{ $testimonial->translate('author_role') }}</span>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- FAQ --}}
    @if ($faqs->isNotEmpty())
        <x-site.section :title="__('site.home.faq_title')">
            <x-site.faq-list :faqs="$faqs" class="max-w-3xl" />
        </x-site.section>
    @endif

    {{-- Call-back band --}}
    <section class="container-page pb-4">
        <div class="relative overflow-hidden rounded-[2rem] bg-teal-900 px-6 py-10 text-white shadow-card sm:px-10 lg:px-14 lg:py-14">
            <div aria-hidden="true" class="pointer-events-none absolute -top-20 -right-20 size-72 rounded-full bg-coral-500/25 blur-3xl"></div>
            <div class="relative grid gap-8 lg:grid-cols-[1fr_1.1fr] lg:items-center">
                <div class="flex items-start gap-4">
                    <x-mascot mood="think" class="hidden h-24 w-auto text-black sm:block" />
                    <div>
                        <h2 class="text-3xl font-bold">{{ __('site.home.cta_title') }}</h2>
                        <p class="mt-3 text-teal-100">{{ __('site.home.cta_body') }}</p>
                    </div>
                </div>
                <div class="rounded-2xl bg-surface p-5 text-ink shadow-card">
                    <x-site.callback-form />
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
