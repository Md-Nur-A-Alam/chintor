@use('App\Support\Localization')

@php
    $selectedCourseIds = collect(old('course_ids', array_filter([$preselectedCourseId])))->map(fn ($id) => (int) $id)->values();
    $startStep = $errors->any() ? 3 : 1;
    $classLevels = range(1, 12);
@endphp

<x-layouts.app :title="__('site.lead.start_title')" :description="__('site.lead.start_subtitle')">
    <x-site.page-hero :title="__('site.lead.start_title')" :subtitle="__('site.lead.start_subtitle')" />

    <div class="container-page py-10">
        <form
            method="POST"
            action="{{ Localization::route('leads.store') }}"
            x-data="{ step: {{ $startStep }}, total: 3 }"
            class="mx-auto max-w-4xl"
        >
            @csrf
            <input type="hidden" name="form" value="wizard">
            <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

            {{-- Progress --}}
            <div class="mb-8">
                <div class="flex items-center justify-between text-sm font-semibold text-ink-muted">
                    <span x-text="@js(__('site.lead.step', ['current' => '__C__', 'total' => '__T__'])).replace('__C__', step).replace('__T__', total)"></span>
                    <x-mascot mood="wave" class="h-12 w-auto text-ink" />
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-surface-muted">
                    <div class="h-full rounded-full bg-accent transition-all duration-300" x-bind:style="`width: ${(step / total) * 100}%`" style="width: {{ round($startStep / 3 * 100) }}%"></div>
                </div>
            </div>

            {{-- Step 1: courses (multiple) --}}
            <section x-show="step === 1" @if ($startStep !== 1) x-cloak @endif>
                <h2 class="text-2xl font-bold text-ink">{{ __('site.lead.step_courses') }}</h2>
                <p class="mt-1 text-ink-muted">{{ __('site.lead.step_courses_hint') }}</p>

                <div class="mt-6 grid gap-8">
                    @foreach ($programs as $program)
                        <div>
                            <h3 class="mb-3 flex items-center gap-2 font-semibold text-ink-muted">
                                <x-icon :name="$program->icon ?: 'layers'" class="size-4 text-brand" /> {{ $program->translate('name') }}
                            </h3>
                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($program->courses as $course)
                                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl bg-surface p-4 shadow-card ring-1 ring-line ring-inset transition has-[:checked]:bg-accent-soft has-[:checked]:ring-2 has-[:checked]:ring-accent-ink/50">
                                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" @checked($selectedCourseIds->contains($course->id)) class="mt-1 size-5 shrink-0 accent-[var(--accent)]">
                                        <span>
                                            <span class="block font-semibold text-ink">{{ $course->translate('title') }}</span>
                                            @if ($course->classRangeLabel())
                                                <span class="block text-sm text-ink-subtle">{{ $course->classRangeLabel() }}</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <x-button type="button" x-on:click="step = 2" size="lg">{{ __('site.lead.next') }} <x-icon name="arrow-right" class="size-5" /></x-button>
                </div>
            </section>

            {{-- Step 2: class --}}
            <section x-show="step === 2" x-cloak>
                <h2 class="text-2xl font-bold text-ink">{{ __('site.lead.step_class') }}</h2>
                <div class="mt-6 grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                    @foreach ($classLevels as $class)
                        <label class="grid cursor-pointer place-items-center rounded-2xl bg-surface p-4 text-center shadow-card ring-1 ring-line ring-inset transition has-[:checked]:bg-accent has-[:checked]:text-on-accent">
                            <input type="radio" name="class_level" value="{{ $class }}" @checked((int) old('class_level') === $class) class="sr-only">
                            <span class="text-2xl font-bold">{{ Localization::digits($class) }}</span>
                            <span class="text-xs opacity-80">{{ __('site.class_n', ['class' => '']) }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-between">
                    <x-button type="button" x-on:click="step = 1" variant="ghost">{{ __('site.lead.previous') }}</x-button>
                    <x-button type="button" x-on:click="step = 3" size="lg">{{ __('site.lead.next') }} <x-icon name="arrow-right" class="size-5" /></x-button>
                </div>
            </section>

            {{-- Step 3: details --}}
            <section x-show="step === 3" @if ($startStep !== 3) x-cloak @endif>
                <h2 class="text-2xl font-bold text-ink">{{ __('site.lead.step_details') }}</h2>

                <div class="mt-6 grid gap-4 rounded-card bg-surface p-6 shadow-card ring-1 ring-line md:grid-cols-2">
                    <x-form.field name="student_name" :label="__('site.lead.student_name')" required />
                    <x-form.field name="guardian_name" :label="__('site.lead.guardian_name')" autocomplete="name" />
                    <x-form.field name="phone" type="tel" :label="__('site.lead.phone')" :help="__('site.lead.phone_hint')" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required />
                    <x-form.field name="age" type="number" :label="__('site.lead.age')" min="3" max="25" />
                    <div class="md:col-span-2">
                        <x-form.textarea name="message" :label="__('site.lead.message')" rows="3" />
                    </div>

                    <label class="flex items-center gap-2.5 text-sm font-medium text-ink md:col-span-2">
                        <input type="checkbox" name="wants_assessment" value="1" @checked(old('wants_assessment', $wantsAssessment)) class="size-4 accent-[var(--accent)]">
                        {{ __('site.lead.wants_assessment') }}
                    </label>

                    <label class="flex items-start gap-2.5 text-sm text-ink-muted md:col-span-2">
                        <input type="checkbox" name="consent" value="1" required class="mt-1 size-4 shrink-0 accent-[var(--accent)]">
                        <span>{{ __('site.lead.consent') }}</span>
                    </label>
                    @error('consent')
                        <p class="text-sm text-accent-ink md:col-span-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-between">
                    <x-button type="button" x-on:click="step = 2" variant="ghost">{{ __('site.lead.previous') }}</x-button>
                    <x-button type="submit" size="lg">{{ __('site.lead.submit') }} <x-icon name="arrow-right" class="size-5" /></x-button>
                </div>
            </section>
        </form>
    </div>
</x-layouts.app>
