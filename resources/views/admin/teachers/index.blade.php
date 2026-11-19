<x-layouts.admin title="শিক্ষক">
    <x-admin.page-header title="শিক্ষক" description="প্রতিটি শিক্ষকের প্রোফাইল — সাধারণ লেখা অথবা সম্পূর্ণ কাস্টম HTML পেজ।">
        <x-slot:actions>
            <x-button :href="route('admin.teachers.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন শিক্ষক</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table>
        <x-slot:head>
            <tr>
                <th class="px-4 py-3">শিক্ষক</th>
                <th class="px-4 py-3">কোর্স</th>
                <th class="px-4 py-3">প্রোফাইল</th>
                <th class="px-4 py-3">অবস্থা</th>
                <th class="px-4 py-3 text-right">অ্যাকশন</th>
            </tr>
        </x-slot:head>

        @forelse ($teachers as $teacher)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="block size-10 overflow-hidden rounded-full bg-brand-soft">
                            @if ($teacher->photo_url)<img src="{{ $teacher->photo_url }}" alt="" class="size-full object-cover">@endif
                        </span>
                        <div>
                            <p class="font-semibold text-ink">{{ $teacher->translate('name', 'bn') }}</p>
                            <p class="text-xs text-ink-subtle">{{ $teacher->translate('designation', 'bn') }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-ink-muted">{{ $teacher->courses_count }}</td>
                <td class="px-4 py-3"><x-badge :tone="$teacher->details_mode === 'html' ? 'info' : 'neutral'">{{ $teacher->details_mode === 'html' ? 'কাস্টম HTML' : 'লেখা' }}</x-badge></td>
                <td class="px-4 py-3"><x-badge :tone="$teacher->is_active ? 'success' : 'neutral'">{{ $teacher->is_active ? 'সক্রিয়' : 'লুকানো' }}</x-badge></td>
                <td class="px-4 py-3 text-right">
                    <x-button :href="route('admin.teachers.edit', $teacher)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /> এডিট</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো শিক্ষক যোগ করা হয়নি।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
