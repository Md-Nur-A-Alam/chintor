@use('App\Models\PaymentAccount')

<x-layouts.admin title="পেমেন্ট অ্যাকাউন্ট">
    <x-admin.page-header title="পেমেন্ট অ্যাকাউন্ট" description="চেকআউটে গ্রাহকেরা এই নম্বরগুলোতে টাকা পাঠাবেন।">
        <x-slot:actions>
            <x-button :href="route('admin.payment-accounts.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন অ্যাকাউন্ট</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">মাধ্যম</th><th class="px-4 py-3">নম্বর</th><th class="px-4 py-3">ধরন</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($accounts as $account)
            <tr>
                <td class="px-4 py-3 font-semibold text-ink">{{ $account->method->label() }}</td>
                <td class="px-4 py-3 font-mono text-ink">{{ $account->account_number }}<p class="font-sans text-xs text-ink-subtle">{{ $account->account_name }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ PaymentAccount::ACCOUNT_TYPES[$account->account_type] ?? $account->account_type }}</td>
                <td class="px-4 py-3"><x-badge :tone="$account->is_active ? 'success' : 'neutral'">{{ $account->is_active ? 'সক্রিয়' : 'বন্ধ' }}</x-badge></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.payment-accounts.edit', $account)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /></x-button>
                        <form method="POST" action="{{ route('admin.payment-accounts.destroy', $account) }}" onsubmit="return confirm('মুছে ফেলবেন?')">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /></x-button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো পেমেন্ট নম্বর নেই — চেকআউটে “নম্বর শিগগিরই যোগ হবে” দেখাচ্ছে।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
