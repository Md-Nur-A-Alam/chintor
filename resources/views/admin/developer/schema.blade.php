<x-layouts.admin title="ER ডায়াগ্রাম">
    <x-admin.page-header title="ER ডায়াগ্রাম" :description="$focus ? $focus.' টেবিল ও সরাসরি সম্পর্কিত টেবিল' : 'পুরো ডেটাবেসের সম্পর্ক (লাইভ স্কিমা থেকে তৈরি)'">
        <x-slot:actions>
            @if ($focus)
                <x-button :href="route('admin.developer.schema', ['system' => $includeSystem ? 1 : null])" variant="secondary" size="sm">পুরো ডায়াগ্রাম</x-button>
            @endif
            <x-button :href="route('admin.developer.schema', ['table' => $focus, 'system' => $includeSystem ? null : 1])" variant="ghost" size="sm">
                {{ $includeSystem ? 'সিস্টেম টেবিল লুকান' : 'সিস্টেম টেবিলসহ' }}
            </x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-card>
        <div x-data="{ zoom: 1 }">
            <div class="mb-3 flex items-center gap-2 text-sm">
                <button type="button" class="cursor-pointer rounded-lg px-2 py-1 ring-1 ring-line" x-on:click="zoom = Math.max(0.3, zoom - 0.1)">−</button>
                <span x-text="Math.round(zoom * 100) + '%'"></span>
                <button type="button" class="cursor-pointer rounded-lg px-2 py-1 ring-1 ring-line" x-on:click="zoom = Math.min(2, zoom + 0.1)">+</button>
            </div>
            <div class="overflow-auto rounded-xl bg-white p-4 ring-1 ring-line">
                <div x-bind:style="`transform: scale(${zoom}); transform-origin: top left`">
                    <pre class="mermaid">{{ $diagram }}</pre>
                </div>
            </div>
        </div>
    </x-card>

    <x-card title="টেবিলের বিবরণ" class="mt-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($tables as $table)
                <details class="rounded-2xl ring-1 ring-line ring-inset" @if ($focus === $table['name']) open @endif>
                    <summary class="flex cursor-pointer items-center justify-between gap-2 p-3">
                        <span class="font-mono text-sm font-semibold text-ink">{{ $table['name'] }}</span>
                        <a href="{{ route('admin.developer.schema', ['table' => $table['name'], 'system' => $includeSystem ? 1 : null]) }}" class="text-xs text-accent-ink hover:underline">ফোকাস</a>
                    </summary>
                    <ul class="border-t border-line p-3 font-mono text-xs">
                        @foreach ($table['columns'] as $column)
                            @php($foreignKey = collect($table['foreign_keys'])->firstWhere('column', $column['name']))
                            <li class="flex justify-between gap-2 py-0.5">
                                <span @class(['font-semibold text-ink' => $column['primary'], 'text-ink-muted' => ! $column['primary']])>
                                    {{ $column['name'] }}{{ $column['nullable'] ? '?' : '' }}
                                    @if ($foreignKey)<span class="text-accent-ink">→ {{ $foreignKey['references'] }}</span>@endif
                                </span>
                                <span class="text-ink-subtle">{{ $column['type'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endforeach
        </div>
    </x-card>

    <x-slot:scripts>
        {{-- Mermaid is loaded only on this developer page. --}}
        <script type="module">
            import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11.4.1/dist/mermaid.esm.min.mjs';
            mermaid.initialize({ startOnLoad: true, theme: 'default', er: { useMaxWidth: false }, securityLevel: 'strict' });
        </script>
    </x-slot:scripts>
</x-layouts.admin>
