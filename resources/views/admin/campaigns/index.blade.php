<x-layouts.admin title="ক্যাম্পেইন">
    <x-admin.page-header title="ক্যাম্পেইন ও ইভেন্ট" description="অফার, Facebook/YouTube লাইভ, প্রতিযোগিতা। শুরু-শেষের সময় দিলে নিজে থেকেই দেখাবে ও সরে যাবে।">
        <x-slot:actions>
            <x-button :href="route('admin.campaigns.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন ক্যাম্পেইন</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">ক্যাম্পেইন</th><th class="px-4 py-3">কোথায় দেখাবে</th><th class="px-4 py-3">সময়</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($campaigns as $campaign)
            @php($state = $campaign->scheduleState())
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if ($campaign->image_url)
                            <img src="{{ $campaign->image_url }}" alt="" class="size-12 rounded-lg object-cover ring-1 ring-line">
                        @else
                            <span class="grid size-12 place-items-center rounded-lg bg-surface-muted text-ink-subtle"><x-icon :name="$campaign->channel->icon()" class="size-5" /></span>
                        @endif
                        <div>
                            <p class="font-semibold text-ink">{{ $campaign->translate('title', 'bn') }}</p>
                            <p class="text-xs text-ink-subtle">{{ $campaign->channel->label() }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @if ($campaign->show_in_hero)<x-badge tone="info">হিরো ব্যানার</x-badge>@endif
                        @if ($campaign->show_in_bar)<x-badge tone="info">ঘোষণা বার</x-badge>@endif
                        <x-badge>ক্যাম্পেইন সেকশন</x-badge>
                    </div>
                    @if ($campaign->show_in_hero && ! $campaign->image_url)
                        <p class="mt-1 text-xs text-accent-ink">ছবি ছাড়া হিরোতে দেখাবে না।</p>
                    @endif
                </td>
                <td class="px-4 py-3 text-xs text-ink-muted">
                    {{ $campaign->starts_at?->format('d M Y, h:i A') ?? 'এখনই' }}<br>→ {{ $campaign->ends_at?->format('d M Y, h:i A') ?? 'বন্ধ না করা পর্যন্ত' }}
                </td>
                <td class="px-4 py-3"><x-badge :tone="$state['tone']">{{ $state['label'] }}</x-badge></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.campaigns.edit', $campaign)" variant="ghost" size="sm" aria-label="এডিট"><x-icon name="pencil" class="size-4" /></x-button>
                        <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" onsubmit="return confirm('মুছে ফেলবেন?')">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm" class="text-accent-ink" aria-label="মুছুন"><x-icon name="trash" class="size-4" /></x-button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো ক্যাম্পেইন নেই। নতুন অফার বা ইভেন্ট এলে এখানে যোগ করুন।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
