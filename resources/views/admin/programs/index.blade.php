<x-layouts.admin title="প্রোগ্রাম">
    <x-admin.page-header title="প্রোগ্রাম" description="কোর্সগুলোর বড় ভাগ (যেমন অলিম্পিয়াড, একাডেমিক)। ওয়েবসাইটের মেনু ও হোম পেজ এখান থেকেই তৈরি হয়।">
        <x-slot:actions>
            <x-button :href="route('admin.programs.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন প্রোগ্রাম</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table>
        <x-slot:head>
            <tr>
                <th class="px-4 py-3">প্রোগ্রাম</th>
                <th class="px-4 py-3">কোর্স</th>
                <th class="px-4 py-3">অবস্থা</th>
                <th class="px-4 py-3">ক্রম</th>
                <th class="px-4 py-3 text-right">অ্যাকশন</th>
            </tr>
        </x-slot:head>

        @forelse ($programs as $program)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="grid size-9 place-items-center rounded-xl bg-brand-soft text-brand"><x-icon :name="$program->icon ?: 'layers'" class="size-4" /></span>
                        <div>
                            <p class="font-semibold text-ink">{{ $program->translate('name', 'bn') }}</p>
                            <p class="text-xs text-ink-subtle">/programs/{{ $program->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-ink-muted">{{ $program->courses_count }}</td>
                <td class="px-4 py-3"><x-badge :tone="$program->status->tone()">{{ $program->status->label() }}</x-badge></td>
                <td class="px-4 py-3 text-ink-muted">{{ $program->sort_order }}</td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.programs.edit', $program)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /> এডিট</x-button>
                        <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" onsubmit="return confirm('প্রোগ্রামটি মুছে ফেলবেন?')">
                            @csrf @method('DELETE')
                            <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /></x-button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো প্রোগ্রাম নেই। প্রথমটি তৈরি করুন।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
