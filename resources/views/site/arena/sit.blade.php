@use('App\Support\Localization')

{{--
    The exam paper. Alpine keeps the countdown (from the server's remaining
    seconds, so a wrong device clock does not matter), remembers answers in
    this browser in case the page reloads, warns before leaving, and submits
    by itself when time is up. The server still grades only what arrives
    before the deadline plus a short grace period.
--}}
@php
    $total = $questions->count();
    $labels = app()->getLocale() === 'bn' ? ['ক', 'খ', 'গ', 'ঘ', 'ঙ'] : ['A', 'B', 'C', 'D', 'E'];
    $title = $attempt->exam?->translate('title') ?? __('site.arena.practice_label');
@endphp

<x-layouts.app :title="$title" :noindex="true">
    <form
        method="POST"
        action="{{ Localization::route('arena.attempts.submit', ['attempt' => $attempt]) }}"
        x-data="{
            key: @js('chintor-arena-'.$attempt->ulid),
            answers: {},
            seconds: {{ $secondsLeft }},
            total: {{ $total }},
            timer: null,
            timeUp: false,
            leaving: (event) => { event.preventDefault(); event.returnValue = '' },
            init() {
                try { this.answers = JSON.parse(localStorage.getItem(this.key)) || {} } catch (error) {}
                window.addEventListener('beforeunload', this.leaving);
                if (this.seconds <= 0) { this.finish(); return }
                this.timer = setInterval(() => { if (--this.seconds <= 0) this.finish() }, 1000);
            },
            pick(question, option) {
                this.answers[question] = option;
                try { localStorage.setItem(this.key, JSON.stringify(this.answers)) } catch (error) {}
            },
            get done() { return Object.keys(this.answers).length },
            digits(text) {
                return @js(app()->getLocale() === 'bn') ? String(text).replace(/[0-9]/g, (d) => '০১২৩৪৫৬৭৮৯'[d]) : String(text);
            },
            get clock() {
                const s = Math.max(0, this.seconds);
                return this.digits(String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0'));
            },
            get progressLabel() {
                return @js(__('site.arena.answered', ['done' => '__DONE__', 'total' => Localization::digits($total)])).replace('__DONE__', this.digits(this.done));
            },
            release() {
                clearInterval(this.timer);
                window.removeEventListener('beforeunload', this.leaving);
                try { localStorage.removeItem(this.key) } catch (error) {}
            },
            finish() { this.timeUp = true; this.release(); this.$el.submit() },
            confirmSubmit(event) {
                if (this.done < this.total && ! confirm(@js(__('site.arena.confirm_submit')))) { event.preventDefault(); return }
                this.release();
            },
        }"
        x-on:submit="confirmSubmit($event)"
    >
        @csrf

        {{-- Sticky exam bar --}}
        <div class="sticky top-16 z-30 border-b border-line bg-canvas/95 backdrop-blur lg:top-20">
            <div class="container-page flex flex-wrap items-center gap-3 py-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate font-bold text-ink">{{ $title }}</p>
                    <p class="text-sm text-ink-muted" x-text="progressLabel">{{ __('site.arena.answered', ['done' => Localization::digits(0), 'total' => Localization::digits($total)]) }}</p>
                </div>
                <p class="inline-flex items-center gap-2 rounded-full px-4 py-2 font-bold tabular-nums" x-bind:class="seconds <= 60 ? 'bg-accent text-on-accent' : 'bg-accent-soft text-accent-ink'" role="timer" aria-label="{{ __('site.arena.time_left') }}">
                    <x-icon name="clock" class="size-5" /> <span x-text="clock">{{ Localization::digits(sprintf('%02d:%02d', intdiv($secondsLeft, 60), $secondsLeft % 60)) }}</span>
                </p>
                <x-button type="submit" size="sm">{{ __('site.arena.submit') }}</x-button>
            </div>
            <div class="h-1 bg-surface-muted"><div class="h-full bg-accent transition-[width] duration-300" x-bind:style="`width: ${total ? (done / total) * 100 : 0}%`"></div></div>
        </div>

        <div class="container-page grid gap-8 py-8 lg:grid-cols-[1fr_16rem]">
            <ol class="grid gap-6">
                @foreach ($questions as $index => $question)
                    <li id="q-{{ $index + 1 }}" class="scroll-mt-40 rounded-[1.5rem] bg-surface p-5 shadow-card ring-1 ring-line sm:p-7">
                        <p class="text-sm font-semibold text-accent-ink">{{ __('site.arena.question_number', ['number' => Localization::digits($index + 1)]) }}</p>
                        <div class="mt-2 text-lg leading-relaxed font-semibold text-ink">{!! nl2br(e($question->body)) !!}</div>
                        @if ($question->image_url)
                            <img src="{{ $question->image_url }}" alt="" loading="lazy" class="mt-4 max-h-72 rounded-xl ring-1 ring-line">
                        @endif

                        <fieldset class="mt-5 grid gap-2.5">
                            <legend class="sr-only">{{ __('site.arena.question_number', ['number' => Localization::digits($index + 1)]) }}</legend>
                            @foreach ($question->options as $optionIndex => $option)
                                <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl bg-surface-muted px-4 py-3 ring-1 ring-line transition hover:ring-line-strong has-[:checked]:bg-brand-soft has-[:checked]:ring-2 has-[:checked]:ring-brand">
                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $optionIndex }}"
                                        x-on:change="pick({{ $question->id }}, {{ $optionIndex }})"
                                        x-bind:checked="answers[{{ $question->id }}] === {{ $optionIndex }}"
                                        class="peer sr-only"
                                    >
                                    <span class="grid size-8 shrink-0 place-items-center rounded-full bg-surface text-sm font-bold text-ink ring-1 ring-line peer-checked:bg-brand peer-checked:text-brand-ink peer-checked:ring-brand peer-focus-visible:ring-2 peer-focus-visible:ring-focus">{{ $labels[$optionIndex] ?? $optionIndex + 1 }}</span>
                                    <span class="text-ink">{{ $option }}</span>
                                </label>
                            @endforeach
                        </fieldset>
                    </li>
                @endforeach
            </ol>

            {{-- Question navigator --}}
            <aside class="hidden lg:block">
                <div class="sticky top-48 rounded-[1.5rem] bg-surface p-5 shadow-card ring-1 ring-line">
                    <div class="grid grid-cols-5 gap-2">
                        @foreach ($questions as $index => $question)
                            <a href="#q-{{ $index + 1 }}" class="grid size-10 place-items-center rounded-xl text-sm font-bold ring-1 transition" x-bind:class="answers[{{ $question->id }}] !== undefined ? 'bg-brand text-brand-ink ring-brand' : 'bg-surface-muted text-ink-muted ring-line hover:ring-line-strong'">{{ Localization::digits($index + 1) }}</a>
                        @endforeach
                    </div>
                    <x-button type="submit" class="mt-5 w-full">{{ __('site.arena.submit') }}</x-button>
                </div>
            </aside>
        </div>

        <div x-show="timeUp" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-black/60 p-6" role="alert">
            <p class="rounded-2xl bg-surface px-6 py-5 text-lg font-bold text-ink shadow-card">{{ __('site.arena.time_up') }}</p>
        </div>
    </form>
</x-layouts.app>
