@props([
    'faqs',
])

{{-- Accessible accordion using native <details>; works without JavaScript. --}}
<div {{ $attributes->class(['grid gap-3']) }}>
    @foreach ($faqs as $faq)
        <details class="group rounded-2xl bg-surface shadow-card ring-1 ring-line open:ring-line-strong">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-semibold text-ink">
                {{ $faq->translate('question') }}
                <x-icon name="chevron-down" class="size-5 shrink-0 text-ink-subtle transition-transform group-open:rotate-180" />
            </summary>
            <div class="px-5 pb-5 leading-relaxed text-ink-muted">
                {!! nl2br(e($faq->translate('answer'))) !!}
            </div>
        </details>
    @endforeach
</div>
