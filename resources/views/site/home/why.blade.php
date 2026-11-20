@use('App\Support\Localization')

{{-- Why Chintor: small batches, free assessment and the refund guarantee (risk reversal). --}}
@php
    $refundParams = [
        'percent' => Localization::digits($refundPolicy['percent']),
        'window' => Localization::digits($refundPolicy['window']),
    ];
@endphp

<x-site.section :title="$section['title']" :subtitle="$section['subtitle']">
    <div role="list" class="grid gap-4 md:grid-cols-3">
        @foreach (['live' => 'users', 'assessment' => 'target', 'refund' => 'refund'] as $key => $icon)
            <x-reveal :index="$loop->index" role="listitem">
                <div class="rounded-card bg-surface p-6 shadow-card ring-1 ring-line transition duration-200 hover:-translate-y-0.5 hover:ring-line-strong">
                    <span class="grid size-12 place-items-center rounded-2xl bg-accent-soft text-accent-ink ring-1 ring-accent-ink/15 ring-inset">
                        <x-icon :name="$icon" class="size-6" />
                    </span>
                    <h3 class="mt-4 text-xl font-semibold text-ink">{{ __("site.home.pillars.{$key}.title", $refundParams) }}</h3>
                    <p class="mt-2 text-ink-muted">{{ __("site.home.pillars.{$key}.body", $refundParams) }}</p>
                </div>
            </x-reveal>
        @endforeach
    </div>
</x-site.section>
