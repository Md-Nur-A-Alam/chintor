@php
    $unread = auth()->user()->unreadNotifications()->count();
@endphp

{{-- Header bell: unread badge + latest notifications, refreshed every minute. --}}
<div
    x-data="{
        open: false,
        unread: {{ $unread }},
        latest: [],
        async refresh() {
            try {
                const response = await fetch(@js(route('admin.notifications.summary')), { headers: { Accept: 'application/json' } });
                if (! response.ok) return;
                const data = await response.json();
                this.unread = data.unread;
                this.latest = data.latest;
            } catch (error) {}
        },
        init() { setInterval(() => this.refresh(), 60000) },
    }"
    x-on:click.outside="open = false"
    class="relative"
>
    <button
        type="button"
        x-on:click="open = ! open; if (open) refresh()"
        class="relative grid size-10 cursor-pointer place-items-center rounded-full text-ink ring-1 ring-line ring-inset hover:bg-surface-muted"
        aria-label="নোটিফিকেশন"
    >
        <x-icon name="bell" class="size-5" />
        <span
            x-show="unread > 0"
            x-text="unread > 99 ? '99+' : unread"
            @if ($unread === 0) x-cloak @endif
            class="absolute -top-1 -right-1 grid min-w-5 place-items-center rounded-full bg-accent px-1 text-[0.7rem] leading-5 font-bold text-on-accent"
        >{{ $unread }}</span>
    </button>

    <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-2xl bg-surface-raised shadow-card ring-1 ring-line">
        <div class="flex items-center justify-between border-b border-line px-4 py-3">
            <span class="font-semibold text-ink">নোটিফিকেশন</span>
            <a href="{{ route('admin.notifications.index') }}" class="text-sm font-medium text-accent-ink hover:underline">সব দেখুন</a>
        </div>
        <ul class="max-h-96 overflow-y-auto">
            <template x-for="item in latest" :key="item.id">
                <li>
                    <a :href="item.open_url" class="flex gap-3 px-4 py-3 hover:bg-surface-muted" :class="item.read ? '' : 'bg-accent-soft/40'">
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-ink" x-text="item.title"></span>
                            <span class="block truncate text-xs text-ink-muted" x-text="item.body" x-show="item.body"></span>
                            <span class="block text-xs text-ink-subtle" x-text="item.time"></span>
                        </span>
                    </a>
                </li>
            </template>
            <li x-show="latest.length === 0" class="px-4 py-8 text-center text-sm text-ink-subtle">কোনো নোটিফিকেশন নেই।</li>
        </ul>
    </div>
</div>
