<x-layouts.admin title="রিভিউ">
    <x-admin.page-header title="রিভিউ" description="শুধু আসল রিভিউ — সম্মতি নিশ্চিত হলেই প্রকাশ করা যায়।">
        <x-slot:actions>
            <x-button :href="route('admin.testimonials.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন রিভিউ</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">রিভিউ</th><th class="px-4 py-3">কোর্স</th><th class="px-4 py-3">সম্মতি</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($testimonials as $testimonial)
            <tr>
                <td class="px-4 py-3"><p class="font-semibold text-ink">{{ $testimonial->author_name }}</p><p class="line-clamp-1 text-xs text-ink-subtle">{{ $testimonial->translate('body', 'bn') }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ $testimonial->course?->translate('title', 'bn') ?? '—' }}</td>
                <td class="px-4 py-3"><x-badge :tone="$testimonial->consent_confirmed ? 'success' : 'warning'">{{ $testimonial->consent_confirmed ? 'আছে' : 'নেই' }}</x-badge></td>
                <td class="px-4 py-3"><x-badge :tone="$testimonial->is_published ? 'success' : 'neutral'">{{ $testimonial->is_published ? 'প্রকাশিত' : 'খসড়া' }}</x-badge></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.testimonials.edit', $testimonial)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /></x-button>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" onsubmit="return confirm('মুছে ফেলবেন?')">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /></x-button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো রিভিউ নেই। আসল রিভিউ এলে এখানে যোগ করুন।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
