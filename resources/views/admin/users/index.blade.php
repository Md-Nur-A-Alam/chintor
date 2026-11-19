<x-layouts.admin title="স্টাফ">
    <x-admin.page-header title="স্টাফ অ্যাকাউন্ট" description="কে অ্যাডমিন প্যানেলে ঢুকতে পারবে এবং কোন রোলে কাজ করবে।">
        <x-slot:actions>
            <x-button :href="route('admin.users.create')" size="sm">
                <x-icon name="plus" class="size-4" /> নতুন স্টাফ
            </x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="GET" class="mb-4 flex max-w-md gap-2">
        <input
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="নাম, ফোন বা অফিস আইডি"
            class="h-10 flex-1 rounded-xl bg-surface px-3.5 text-sm ring-1 ring-line-strong outline-none ring-inset focus:ring-2 focus:ring-focus"
        >
        <x-button type="submit" variant="secondary" size="sm"><x-icon name="search" class="size-4" /> খুঁজুন</x-button>
    </form>

    <x-admin.table :paginator="$users">
        <x-slot:head>
            <tr>
                <th class="px-4 py-3">নাম</th>
                <th class="px-4 py-3">ফোন</th>
                <th class="px-4 py-3">রোল</th>
                <th class="px-4 py-3">অবস্থা</th>
                <th class="px-4 py-3 text-right">অ্যাকশন</th>
            </tr>
        </x-slot:head>

        @forelse ($users as $user)
            <tr class="hover:bg-surface-muted/50">
                <td class="px-4 py-3">
                    <p class="font-semibold text-ink">{{ $user->name }}</p>
                    <p class="text-xs text-ink-subtle">{{ collect([$user->office_id, $user->designation])->filter()->join(' · ') }}</p>
                </td>
                <td class="px-4 py-3 text-ink-muted">{{ $user->phone }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @foreach ($user->roles as $role)
                            <x-badge>{{ $role->label }}</x-badge>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-3">
                    <x-badge :tone="$user->is_active ? 'success' : 'danger'">{{ $user->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</x-badge>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.users.edit', $user)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /> এডিট</x-button>
                        @unless ($user->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ $user->name }}-এর অ্যাকাউন্ট মুছে ফেলবেন?')">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> মুছুন</x-button>
                            </form>
                        @endunless
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">কোনো স্টাফ পাওয়া যায়নি।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
