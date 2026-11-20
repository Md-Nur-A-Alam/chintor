@use('App\Support\Localization')

{{--
    Result: score ring, counts and an encouraging line, then every question
    with the student's answer, the correct one and the explanation (text,
    image and/or a Google Drive PDF). If the exam publishes results later,
    only the "answers received" note shows until then.
--}}
@php
    $total = $attempt->totalQuestions();
    $percent = $attempt->scorePercent();
    $title = $attempt->exam?->translate('title') ?? __('site.arena.practice_label');
    $labels = app()->getLocale() === 'bn' ? ['ক', 'খ', 'গ', 'ঘ', 'ঙ'] : ['A', 'B', 'C', 'D', 'E'];
    $message = match (true) {
        $percent >= 80 => __('site.arena.messages.excellent'),
        $percent >= 50 => __('site.arena.messages.good'),
        default => __('site.arena.messages.keep_going'),
    };
@endphp

<x-layouts.app :title="__('site.arena.result_title', ['name' => $attempt->student_name])" :noindex="true">
    <section class="relative overflow-hidden border-b border-line">
        <div aria-hidden="true" class="bg-aurora pointer-events-none absolute inset-0"></div>
        <div class="container-page relative py-12 lg:py-16">
            <p class="text-sm font-semibold text-accent-ink">{{ $title }}</p>
            <h1 class="mt-2 text-3xl font-bold text-ink sm:text-4xl">{{ __('site.arena.result_title', ['name' => $attempt->student_name]) }}</h1>

            @if (! $attempt->resultsArePublished())
                <p class="mt-6 max-w-2xl rounded-2xl bg-surface p-6 text-lg text-ink shadow-card ring-1 ring-line">
                    {{ __('site.arena.results_pending', ['date' => Localization::digits($attempt->exam->results_at->translatedFormat('j F, g:i A'))]) }}
                </p>
            @else
                <div class="mt-8 flex flex-col gap-8 sm:flex-row sm:items-center">
                    <div class="animate-pop relative grid size-40 shrink-0 place-items-center rounded-full" style="background: conic-gradient(var(--accent) {{ $percent * 3.6 }}deg, var(--surface-muted) 0deg)">
                        <div class="grid size-32 place-items-center rounded-full bg-canvas text-center">
                            <span>
                                <span class="block text-4xl font-bold text-ink tabular-nums">{{ Localization::digits($percent) }}%</span>
                                <span class="block text-sm text-ink-muted">{{ __('site.arena.score', ['correct' => Localization::digits($attempt->correct_count), 'total' => Localization::digits($total)]) }}</span>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-ink">{{ $message }}</p>
                        <dl class="mt-4 flex flex-wrap gap-3 text-sm font-semibold">
                            <div class="rounded-full bg-brand-soft px-4 py-2 text-brand"><dt class="inline">{{ __('site.arena.correct') }}:</dt> <dd class="inline">{{ Localization::digits($attempt->correct_count) }}</dd></div>
                            <div class="rounded-full bg-accent-soft px-4 py-2 text-accent-ink"><dt class="inline">{{ __('site.arena.wrong') }}:</dt> <dd class="inline">{{ Localization::digits($attempt->wrong_count) }}</dd></div>
                            <div class="rounded-full bg-surface-muted px-4 py-2 text-ink-muted"><dt class="inline">{{ __('site.arena.skipped') }}:</dt> <dd class="inline">{{ Localization::digits($attempt->skipped_count) }}</dd></div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if ($questions->isNotEmpty())
        <ol class="container-page grid gap-6 py-12">
            @foreach ($questions as $index => $question)
                @php
                    $chosen = $attempt->answers[$question->id] ?? null;
                    $state = $chosen === null ? 'skipped' : ($chosen === $question->correct_option ? 'correct' : 'wrong');
                @endphp
                <li class="rounded-[1.5rem] bg-surface p-5 shadow-card ring-1 ring-line sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-ink-muted">{{ __('site.arena.question_number', ['number' => Localization::digits($index + 1)]) }}</p>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-bold',
                            'bg-brand-soft text-brand' => $state === 'correct',
                            'bg-accent-soft text-accent-ink' => $state === 'wrong',
                            'bg-surface-muted text-ink-muted' => $state === 'skipped',
                        ])>{{ __("site.arena.{$state}") }}</span>
                    </div>
                    <div class="mt-2 text-lg leading-relaxed font-semibold text-ink">{!! nl2br(e($question->body)) !!}</div>
                    @if ($question->image_url)
                        <img src="{{ $question->image_url }}" alt="" loading="lazy" class="mt-4 max-h-72 rounded-xl ring-1 ring-line">
                    @endif

                    <ul class="mt-5 grid gap-2.5">
                        @foreach ($question->options as $optionIndex => $option)
                            @php
                                $isCorrect = $optionIndex === $question->correct_option;
                                $isChosen = $optionIndex === $chosen;
                            @endphp
                            <li @class([
                                'flex items-center gap-3 rounded-xl px-4 py-3 ring-1',
                                'bg-brand-soft ring-2 ring-brand' => $isCorrect,
                                'bg-accent-soft ring-2 ring-accent' => $isChosen && ! $isCorrect,
                                'bg-surface-muted ring-line' => ! $isCorrect && ! $isChosen,
                            ])>
                                <span @class(['grid size-8 shrink-0 place-items-center rounded-full text-sm font-bold', 'bg-brand text-brand-ink' => $isCorrect, 'bg-accent text-on-accent' => $isChosen && ! $isCorrect, 'bg-surface text-ink ring-1 ring-line' => ! $isCorrect && ! $isChosen])>{{ $labels[$optionIndex] ?? $optionIndex + 1 }}</span>
                                <span class="flex-1 text-ink">{{ $option }}</span>
                                @if ($isCorrect)
                                    <span class="text-xs font-bold text-brand">{{ __('site.arena.correct_answer') }}</span>
                                @elseif ($isChosen)
                                    <span class="text-xs font-bold text-accent-ink">{{ __('site.arena.your_answer') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @if ($question->hasExplanation())
                        <div class="mt-5 rounded-2xl bg-surface-muted p-5">
                            <p class="font-bold text-ink">{{ __('site.arena.explanation') }}</p>
                            @if ($question->explanation)
                                <div class="mt-2 leading-relaxed text-ink-muted">{!! nl2br(e($question->explanation)) !!}</div>
                            @endif
                            @if ($question->explanation_image_url)
                                <img src="{{ $question->explanation_image_url }}" alt="{{ __('site.arena.explanation') }}" loading="lazy" class="mt-4 max-h-96 rounded-xl ring-1 ring-line">
                            @endif
                            @if ($question->explanation_pdf_url)
                                <a href="{{ $question->explanation_pdf_url }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-11 items-center gap-2 rounded-full bg-surface px-4 font-semibold text-accent-ink ring-1 ring-line hover:ring-line-strong">
                                    <x-icon name="file" class="size-4" /> {{ __('site.arena.explanation_pdf') }} <x-icon name="external" class="size-4" />
                                </a>
                            @endif
                        </div>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif

    <section class="container-page pb-6">
        <div class="flex flex-col items-start justify-between gap-6 rounded-[2rem] bg-teal-900 p-8 text-white shadow-card sm:flex-row sm:items-center sm:p-10">
            <div>
                <h2 class="text-2xl font-bold sm:text-3xl">{{ __('site.arena.cta_title') }}</h2>
                <p class="mt-2 max-w-xl text-teal-100">{{ __('site.arena.cta_body') }}</p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                <x-button :href="Localization::route('start', ['assessment' => 1])" size="lg">{{ __('site.cta.free_assessment') }}</x-button>
                <x-button :href="Localization::route('arena.index')" variant="secondary" size="lg">{{ __('site.arena.try_again') }}</x-button>
            </div>
        </div>
    </section>
</x-layouts.app>
