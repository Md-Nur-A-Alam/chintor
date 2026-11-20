<x-layouts.admin title="পরীক্ষা">
    <x-admin.page-header title="এক্সাম এরিনার পরীক্ষা" description="নির্দিষ্ট পরীক্ষা: সময়, প্রশ্নসংখ্যা ও প্রশ্ন আপনি ঠিক করবেন। বেশি প্রশ্ন যোগ করলে প্রতিবার তার থেকে এলোমেলোভাবে বাছাই হবে।">
        <x-slot:actions>
            <x-button :href="route('admin.exams.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন পরীক্ষা</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">পরীক্ষা</th><th class="px-4 py-3">সময় · প্রশ্ন</th><th class="px-4 py-3">ফলাফল প্রকাশ</th><th class="px-4 py-3">অংশগ্রহণ</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($exams as $exam)
            <tr>
                <td class="px-4 py-3"><p class="font-semibold text-ink">{{ $exam->translate('title', 'bn') }}</p><p class="text-xs text-ink-subtle">/arena/exams/{{ $exam->slug }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ $exam->duration_minutes }} মিনিট · {{ $exam->question_count }}/{{ $exam->questions_count }} প্রশ্ন</td>
                <td class="px-4 py-3 text-ink-muted">{{ $exam->results_at?->format('d M Y, h:i A') ?? 'সাথে সাথে' }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ $exam->attempts_count }}</td>
                <td class="px-4 py-3"><x-badge :tone="$exam->is_published ? 'success' : 'neutral'">{{ $exam->is_published ? 'প্রকাশিত' : 'খসড়া' }}</x-badge></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.exams.edit', $exam)" variant="ghost" size="sm" aria-label="এডিট"><x-icon name="pencil" class="size-4" /></x-button>
                        <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('পরীক্ষাটি মুছে ফেলবেন? প্রশ্নগুলো প্রশ্ন ব্যাংকে থেকে যাবে।')">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm" class="text-accent-ink" aria-label="মুছুন"><x-icon name="trash" class="size-4" /></x-button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-ink-subtle">এখনো কোনো পরীক্ষা নেই। আগে প্রশ্ন ব্যাংকে প্রশ্ন যোগ করুন।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
