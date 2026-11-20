<x-layouts.admin title="এরর লগ">
    <x-admin.page-header title="এরর লগ" description="storage/logs থেকে সাম্প্রতিক ১০০টি এন্ট্রি।">
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.developer.errors.clear') }}" onsubmit="return confirm('সব লগ ফাইল খালি করবেন?')">
                @csrf
                <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> লগ খালি করুন</x-button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.developer.errors') }}" @class(['rounded-full px-3 py-1 text-sm ring-1 ring-line ring-inset', 'bg-accent text-on-accent' => $level === null])>সব</a>
        @foreach ($levels as $item)
            <a href="{{ route('admin.developer.errors', ['level' => $item]) }}" @class(['rounded-full px-3 py-1 text-sm ring-1 ring-line ring-inset', 'bg-accent text-on-accent' => $level === $item])>{{ $item }}</a>
        @endforeach
    </div>

    <div class="grid gap-3">
        @forelse ($entries as $entry)
            <details class="rounded-card bg-surface shadow-card ring-1 ring-line">
                <summary class="flex cursor-pointer items-start gap-3 p-4">
                    <x-badge :tone="in_array($entry['level'], ['error', 'critical', 'alert', 'emergency'], true) ? 'danger' : ($entry['level'] === 'warning' ? 'warning' : 'neutral')">{{ $entry['level'] }}</x-badge>
                    <span class="min-w-0 flex-1">
                        <span class="block font-mono text-sm break-words text-ink">{{ \Illuminate\Support\Str::limit($entry['message'], 300) }}</span>
                        <span class="block text-xs text-ink-subtle">{{ $entry['time'] }} · {{ $entry['environment'] }} · {{ $entry['file'] }}</span>
                    </span>
                </summary>
                @if ($entry['context'] !== '' || strlen($entry['message']) > 300)
                    <pre class="max-h-96 overflow-auto border-t border-line p-4 text-xs whitespace-pre-wrap text-ink-muted">{{ $entry['message'] }}

{{ $entry['context'] }}</pre>
                @endif
            </details>
        @empty
            <x-card><p class="py-8 text-center text-ink-subtle">কোনো লগ এন্ট্রি নেই।</p></x-card>
        @endforelse
    </div>
</x-layouts.admin>
