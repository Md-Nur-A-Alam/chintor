@use('App\Enums\AssessmentStatus')

<x-layouts.admin title="অ্যাসেসমেন্ট">
    <x-admin.page-header title="ফ্রি অ্যাসেসমেন্ট" description="অভিভাবককে কল করে সময় ঠিক করুন, শিক্ষক দিন; শেষে ফলাফল লিখুন।" />

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.assessments.index') }}" @class(['rounded-full px-4 py-1.5 text-sm font-medium ring-1 ring-line ring-inset', 'bg-accent text-on-accent' => $status === '', 'text-ink-muted' => $status !== ''])>সব</a>
        @foreach (AssessmentStatus::cases() as $case)
            <a href="{{ route('admin.assessments.index', ['status' => $case->value]) }}" @class(['rounded-full px-4 py-1.5 text-sm font-medium ring-1 ring-line ring-inset', 'bg-accent text-on-accent' => $status === $case->value, 'text-ink-muted' => $status !== $case->value])>{{ $case->label() }}</a>
        @endforeach
    </div>

    <x-admin.table :paginator="$assessments">
        <x-slot:head>
            <tr><th class="px-4 py-3">শিক্ষার্থী</th><th class="px-4 py-3">কোর্স</th><th class="px-4 py-3">শিক্ষক</th><th class="px-4 py-3">সময়</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3"></th></tr>
        </x-slot:head>
        @forelse ($assessments as $assessment)
            <tr>
                <td class="px-4 py-3"><p class="font-medium text-ink">{{ $assessment->lead->student_name ?? $assessment->lead->displayName() }}</p><p class="text-xs text-ink-subtle">{{ $assessment->lead->phone }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ $assessment->course?->translate('title', 'bn') ?? '—' }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ $assessment->teacher?->translate('name', 'bn') ?? '—' }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ $assessment->scheduled_at?->format('d M, h:i A') ?? '—' }}</td>
                <td class="px-4 py-3"><x-badge :tone="$assessment->status->tone()">{{ $assessment->status->label() }}</x-badge></td>
                <td class="px-4 py-3 text-right"><x-button :href="route('admin.assessments.edit', $assessment)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /></x-button></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-ink-subtle">কোনো অ্যাসেসমেন্ট নেই।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
