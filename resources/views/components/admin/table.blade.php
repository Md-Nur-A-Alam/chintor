@props([
    'paginator' => null,
])

{{-- Responsive data table wrapper: pass <thead> as the "head" slot and rows as the default slot. --}}
<div {{ $attributes->class(['overflow-hidden rounded-card bg-surface shadow-card ring-1 ring-line']) }}>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[40rem] text-left text-sm">
            <thead class="border-b border-line bg-surface-muted text-xs font-semibold tracking-wide text-ink-subtle">
                {{ $head }}
            </thead>
            <tbody class="divide-y divide-line">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if ($paginator && $paginator->hasPages())
        <div class="border-t border-line px-4 py-3">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
