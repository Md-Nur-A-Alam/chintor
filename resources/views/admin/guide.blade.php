<x-layouts.admin title="নির্দেশিকা">
    <x-admin.page-header title="নির্দেশিকা" description="আপনার রোল অনুযায়ী অ্যাডমিন প্যানেল ব্যবহারের নিয়ম।" />

    <div x-data="{ tab: @js($guides->has($active) ? $active : $guides->keys()->first()) }" class="grid gap-6 lg:grid-cols-[14rem_1fr]">
        <nav class="flex gap-2 overflow-x-auto lg:flex-col" aria-label="নির্দেশিকা">
            @foreach ($guides as $name => $guide)
                <button
                    type="button"
                    x-on:click="tab = @js($name)"
                    x-bind:class="tab === @js($name) ? 'bg-accent-soft text-accent-ink' : 'text-ink-muted hover:bg-surface-muted'"
                    class="cursor-pointer rounded-xl px-4 py-2 text-left text-sm font-semibold whitespace-nowrap"
                >{{ $guide['label'] }}</button>
            @endforeach
        </nav>

        <div>
            @foreach ($guides as $name => $guide)
                <x-card x-show="tab === @js($name)" x-cloak>
                    <article class="prose-chintor">{!! $guide['html'] !!}</article>
                </x-card>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
