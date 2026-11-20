<x-layouts.admin title="এরিনার ফলাফল">
    <x-admin.page-header title="এরিনার ফলাফল" description="কে কোন পরীক্ষা দিয়েছে ও কেমন করেছে। মোবাইল নম্বর দিলে সেটি লিড হিসেবেও কিউতে যায় — কম নম্বর পাওয়া শিক্ষার্থীর অভিভাবককে পরামর্শ দেওয়ার ভালো সুযোগ।" />

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">শিক্ষার্থী</th><th class="px-4 py-3">পরীক্ষা</th><th class="px-4 py-3">ফলাফল</th><th class="px-4 py-3">সময়</th><th class="px-4 py-3">লিড</th></tr>
        </x-slot:head>
        @forelse ($attempts as $attempt)
            <tr>
                <td class="px-4 py-3"><p class="font-semibold text-ink">{{ $attempt->student_name }}</p><p class="text-xs text-ink-subtle">{{ $attempt->phone ?? '—' }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ $attempt->exam?->translate('title', 'bn') ?? 'নিজের মতো প্র্যাকটিস' }}</td>
                <td class="px-4 py-3">
                    <x-badge :tone="$attempt->scorePercent() >= 80 ? 'success' : ($attempt->scorePercent() >= 50 ? 'info' : 'warning')">{{ $attempt->correct_count }}/{{ $attempt->totalQuestions() }} ({{ $attempt->scorePercent() }}%)</x-badge>
                </td>
                <td class="px-4 py-3 text-ink-muted">{{ $attempt->submitted_at->diffForHumans() }}</td>
                <td class="px-4 py-3">
                    @if ($attempt->lead)
                        <a href="{{ route('admin.leads.show', $attempt->lead) }}" class="font-semibold text-accent-ink hover:underline">{{ $attempt->lead->reference }}</a>
                    @else
                        <span class="text-ink-subtle">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">এখনো কেউ পরীক্ষা দেয়নি।</td></tr>
        @endforelse
    </x-admin.table>

    <div class="mt-6">{{ $attempts->links() }}</div>
</x-layouts.admin>
