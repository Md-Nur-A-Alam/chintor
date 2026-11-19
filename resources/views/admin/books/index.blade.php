<x-layouts.admin title="বই">
    <x-admin.page-header title="বই" description="মুদ্রিত বই — দাম, স্টক ও ভেতরের পাতার ছবি।">
        <x-slot:actions>
            <x-button :href="route('admin.books.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন বই</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table :paginator="$books">
        <x-slot:head>
            <tr>
                <th class="px-4 py-3">বই</th>
                <th class="px-4 py-3">দাম</th>
                <th class="px-4 py-3">স্টক</th>
                <th class="px-4 py-3">অবস্থা</th>
                <th class="px-4 py-3 text-right">অ্যাকশন</th>
            </tr>
        </x-slot:head>

        @forelse ($books as $book)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="block h-14 w-10 overflow-hidden rounded bg-surface-muted ring-1 ring-line">
                            @if ($book->cover_url)<img src="{{ $book->cover_url }}" alt="" class="size-full object-cover">@endif
                        </span>
                        <div>
                            <p class="font-semibold text-ink">{{ $book->translate('title', 'bn') }}</p>
                            <p class="text-xs text-ink-subtle">{{ $book->authors->map->translate('name', 'bn')->join(', ') }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3"><x-price :regular="$book->regular_price" :discounted="$book->discounted_price" size="sm" :show-saving="false" /></td>
                <td class="px-4 py-3"><x-badge :tone="$book->stock_quantity > 0 ? 'success' : 'danger'">{{ $book->stock_quantity }}</x-badge></td>
                <td class="px-4 py-3"><x-badge :tone="$book->status->tone()">{{ $book->status->label() }}</x-badge></td>
                <td class="px-4 py-3 text-right">
                    <x-button :href="route('admin.books.edit', $book)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /> এডিট</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো বই যোগ করা হয়নি।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
