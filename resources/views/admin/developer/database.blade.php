<x-layouts.admin title="ডেটাবেস কনসোল">
    <x-admin.page-header title="ডেটাবেস কনসোল" description="একাধিক কোয়েরি ; দিয়ে আলাদা করুন। SELECT/SHOW সবসময় চলে; ডেটা বদলানোর কোয়েরির জন্য Write মোড ও পাসওয়ার্ড লাগে। সব কোয়েরি অডিট লগে থাকে।" />

    <form method="POST" action="{{ route('admin.developer.database.run') }}" x-data="{ write: @js((bool) old('write_mode')) }" class="grid gap-4">
        @csrf
        <x-card>
            <div class="grid gap-3">
                <label for="sql" class="text-sm font-medium text-ink">SQL</label>
                <textarea id="sql" name="sql" rows="8" spellcheck="false" class="w-full rounded-xl bg-surface-muted p-3 font-mono text-sm text-ink ring-1 ring-line-strong outline-none ring-inset focus:ring-2 focus:ring-focus" placeholder="SELECT id, reference, status FROM leads ORDER BY id DESC LIMIT 20;">{{ $sql }}</textarea>
                @error('sql') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror

                <div class="flex flex-wrap items-end gap-4">
                    <label @class(['inline-flex items-center gap-2 text-sm font-medium', 'text-ink' => $writesAllowed, 'text-ink-subtle' => ! $writesAllowed])>
                        <input type="checkbox" name="write_mode" value="1" x-model="write" @disabled(! $writesAllowed) class="size-4 accent-[var(--accent)]">
                        Write মোড (INSERT/UPDATE/DELETE/ALTER) {{ $writesAllowed ? '' : '— এই সার্ভারে বন্ধ' }}
                    </label>
                    <div x-show="write" x-cloak class="w-64">
                        <x-form.field name="password" type="password" label="আপনার পাসওয়ার্ড" autocomplete="current-password" />
                    </div>
                    <x-button type="submit" variant="brand" size="sm"><x-icon name="play" class="size-4" /> চালান</x-button>
                </div>
                <p x-show="write" x-cloak class="rounded-xl bg-accent-soft px-3 py-2 text-sm text-accent-ink">সাবধান: write কোয়েরি সরাসরি লাইভ ডেটা বদলায় এবং ফেরানো যায় না। আগে ব্যাকআপ নিন।</p>
            </div>
        </x-card>
    </form>

    @foreach ($results as $result)
        <x-card class="mt-4">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <code class="block max-w-full truncate text-xs text-ink-muted">{{ $result['statement'] }}</code>
                <span class="flex items-center gap-2 text-xs text-ink-subtle">
                    {{ $result['duration_ms'] }} ms
                    @if ($result['error'])
                        <x-badge tone="danger">ত্রুটি</x-badge>
                    @elseif ($result['is_write'])
                        <x-badge tone="warning">{{ $result['affected'] }} সারি বদলেছে</x-badge>
                    @else
                        <x-badge tone="success">{{ $result['total'] }} সারি</x-badge>
                    @endif
                </span>
            </div>

            @if ($result['error'])
                <pre class="overflow-x-auto rounded-xl bg-accent-soft p-3 text-xs whitespace-pre-wrap text-accent-ink">{{ $result['error'] }}</pre>
            @elseif (! $result['is_write'])
                @if ($result['rows'] === [])
                    <p class="text-sm text-ink-subtle">কোনো সারি নেই।</p>
                @else
                    <div class="max-h-[28rem] overflow-auto rounded-xl ring-1 ring-line">
                        <table class="min-w-full text-left font-mono text-xs">
                            <thead class="sticky top-0 bg-surface-muted text-ink-subtle">
                                <tr>@foreach ($result['columns'] as $column)<th class="px-3 py-2 whitespace-nowrap">{{ $column }}</th>@endforeach</tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @foreach ($result['rows'] as $row)
                                    <tr>@foreach ($result['columns'] as $column)<td class="max-w-xs truncate px-3 py-1.5 text-ink" title="{{ $row[$column] }}">{{ is_null($row[$column]) ? 'NULL' : $row[$column] }}</td>@endforeach</tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($result['truncated'])
                        <p class="mt-2 text-xs text-ink-subtle">প্রথম {{ count($result['rows']) }}টি সারি দেখানো হলো। LIMIT ব্যবহার করুন।</p>
                    @endif
                @endif
            @endif
        </x-card>
    @endforeach

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_16rem]">
        <x-card title="সাম্প্রতিক কোয়েরি (অডিট লগ)">
            <ul class="grid gap-2 text-xs">
                @forelse ($history as $log)
                    <li class="flex items-start justify-between gap-3 border-b border-line pb-2">
                        <code class="min-w-0 flex-1 truncate text-ink" title="{{ $log->statement }}">{{ $log->statement }}</code>
                        <span class="shrink-0 text-ink-subtle">{{ $log->user?->name }} · {{ $log->created_at->diffForHumans() }} @if ($log->is_write)<x-badge tone="warning">W</x-badge>@endif @if ($log->error)<x-badge tone="danger">!</x-badge>@endif</span>
                    </li>
                @empty
                    <li class="text-ink-subtle">এখনো কোনো কোয়েরি চালানো হয়নি।</li>
                @endforelse
            </ul>
        </x-card>

        <x-card title="টেবিল">
            <ul class="grid max-h-96 gap-1 overflow-y-auto font-mono text-xs">
                @foreach ($tables as $table)
                    <li><a href="{{ route('admin.developer.schema', ['table' => $table, 'system' => 1]) }}" class="text-ink-muted hover:text-accent-ink">{{ $table }}</a></li>
                @endforeach
            </ul>
        </x-card>
    </div>
</x-layouts.admin>
