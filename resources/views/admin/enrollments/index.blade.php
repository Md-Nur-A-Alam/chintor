@use('App\Enums\EnrollmentStatus')
@use('App\Support\Money')

<x-layouts.admin title="এনরোলমেন্ট">
    <x-admin.page-header title="এনরোলমেন্ট" description="ভর্তির অনুরোধ ও সক্রিয় শিক্ষার্থী। পেমেন্ট যাচাই হলে নিজে থেকে সক্রিয় হয়।" />

    <form method="GET" class="mb-4 grid max-w-2xl gap-3 sm:grid-cols-[1fr_14rem_auto] sm:items-end">
        <x-form.field name="search" label="খুঁজুন" :value="$search" placeholder="রেফারেন্স, ফোন বা শিক্ষার্থী" />
        <x-form.select name="status" label="অবস্থা" :value="$status" placeholder="সব" :options="EnrollmentStatus::options()" />
        <x-button type="submit" variant="brand" size="sm">ফিল্টার</x-button>
    </form>

    <x-admin.table :paginator="$enrollments">
        <x-slot:head>
            <tr><th class="px-4 py-3">রেফারেন্স</th><th class="px-4 py-3">শিক্ষার্থী</th><th class="px-4 py-3">কোর্স / ব্যাচ</th><th class="px-4 py-3">টাকা</th><th class="px-4 py-3">অবস্থা</th></tr>
        </x-slot:head>
        @forelse ($enrollments as $enrollment)
            <tr class="hover:bg-surface-muted/50">
                <td class="px-4 py-3"><a href="{{ route('admin.enrollments.show', $enrollment) }}" class="font-mono font-semibold text-ink hover:text-accent-ink">{{ $enrollment->reference }}</a><p class="text-xs text-ink-subtle">{{ $enrollment->created_at->format('d M Y') }}</p></td>
                <td class="px-4 py-3"><p class="font-medium text-ink">{{ $enrollment->student->name }}</p><p class="text-xs text-ink-subtle">{{ $enrollment->guardian->phone }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ $enrollment->course->translate('title', 'bn') }}<p class="text-xs text-ink-subtle">{{ $enrollment->batch?->code }}</p></td>
                <td class="px-4 py-3 font-medium">{{ Money::taka($enrollment->payable_amount, 'bn') }}</td>
                <td class="px-4 py-3"><x-badge :tone="$enrollment->status->tone()">{{ $enrollment->status->label() }}</x-badge></td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">কোনো এনরোলমেন্ট নেই।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
