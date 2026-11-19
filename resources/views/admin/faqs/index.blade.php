<x-layouts.admin title="FAQ">
    <x-admin.page-header title="প্রশ্নোত্তর (FAQ)" description="কোর্স বাছাই না করলে প্রশ্নটি হোম পেজে দেখাবে; কোর্স বাছাই করলে শুধু সেই কোর্সের পেজে।">
        <x-slot:actions>
            <x-button :href="route('admin.faqs.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন প্রশ্ন</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">প্রশ্ন</th><th class="px-4 py-3">কোথায়</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($faqs as $faq)
            <tr>
                <td class="px-4 py-3 font-medium text-ink">{{ $faq->translate('question', 'bn') }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ $faq->faqable?->translate('title', 'bn') ?? 'হোম পেজ (সাধারণ)' }}</td>
                <td class="px-4 py-3"><x-badge :tone="$faq->is_active ? 'success' : 'neutral'">{{ $faq->is_active ? 'সক্রিয়' : 'লুকানো' }}</x-badge></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.faqs.edit', $faq)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /></x-button>
                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('মুছে ফেলবেন?')">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /></x-button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-10 text-center text-ink-subtle">কোনো প্রশ্নোত্তর নেই।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
