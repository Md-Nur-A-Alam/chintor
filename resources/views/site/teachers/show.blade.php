<x-layouts.app :title="$teacher->translate('name')" :description="$teacher->translate('summary')" :image="$teacher->photo_url">
    <div class="container-page py-10 lg:py-14">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
            <span class="block size-32 shrink-0 overflow-hidden rounded-full bg-brand-soft ring-4 ring-surface-muted">
                @if ($teacher->photo_url)
                    <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->translate('name') }}" class="size-full object-cover">
                @else
                    <span class="grid size-full place-items-center text-4xl font-bold text-brand">{{ mb_substr((string) $teacher->translate('name'), 0, 1) }}</span>
                @endif
            </span>
            <div>
                <h1 class="text-4xl font-bold tracking-tight text-ink">{{ $teacher->translate('name') }}</h1>
                @if ($teacher->translate('designation'))
                    <p class="mt-1 text-lg font-medium text-brand">{{ $teacher->translate('designation') }}</p>
                @endif
                @if ($teacher->translate('summary'))
                    <p class="mt-3 max-w-2xl text-ink-muted">{{ $teacher->translate('summary') }}</p>
                @endif
            </div>
        </div>

        <section class="mt-10">
            <h2 class="sr-only">{{ __('site.teacher.profile') }}</h2>
            @if ($teacher->usesCustomHtml())
                {{--
                    Custom HTML/CSS/JS profile (requirements §3.4), isolated in a
                    sandboxed iframe: scripts run, but they cannot reach this site,
                    its cookies or the admin session. Height follows the content.
                --}}
                <iframe
                    title="{{ $teacher->translate('name') }}"
                    sandbox="allow-scripts allow-popups allow-popups-to-escape-sandbox"
                    srcdoc="{{ $teacher->details_html }}"
                    class="h-[70vh] w-full rounded-card bg-white shadow-card ring-1 ring-line"
                    loading="lazy"
                ></iframe>
            @elseif ($teacher->translate('details_rich'))
                <div class="max-w-3xl rounded-card bg-surface p-6 leading-relaxed text-ink-muted shadow-card ring-1 ring-line">
                    {!! nl2br(e($teacher->translate('details_rich'))) !!}
                </div>
            @endif
        </section>

        @if ($teacher->courses->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-2xl font-bold text-ink">{{ __('site.teacher.courses') }}</h2>
                <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($teacher->courses as $course)
                        <x-site.course-card :course="$course" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
