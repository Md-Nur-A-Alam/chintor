@use('App\Enums\LeadStatus')
@use('App\Enums\LeadType')

<x-layouts.admin title="লিড">
    <x-admin.page-header
        :title="$canSeeAll ? 'লিড — কেন্দ্রীয় কিউ' : 'আমার লিড'"
        :description="$canSeeAll ? 'ওয়েবসাইটের সব ফর্ম এখানে আসে। বাছাই করে সেলস এক্সিকিউটিভকে দিন।' : 'আপনাকে দেওয়া লিডগুলো। কল করে টাইমলাইনে লিখে রাখুন।'"
    />

    {{-- Filters --}}
    <form method="GET" class="mb-4 grid gap-3 rounded-card bg-surface p-4 shadow-card ring-1 ring-line sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
        <x-form.field name="search" label="খুঁজুন" :value="$filters['search'] ?? null" placeholder="ফোন, নাম বা রেফারেন্স" />
        <x-form.select name="status" label="অবস্থা" :value="$filters['status'] ?? null" placeholder="সব" :options="LeadStatus::options()" />
        <x-form.select name="type" label="ধরন" :value="$filters['type'] ?? null" placeholder="সব" :options="LeadType::options()" />
        @if ($canSeeAll)
            <x-form.select name="assignee" label="দায়িত্বে" :value="$filters['assignee'] ?? null" placeholder="সবাই" :options="['unassigned' => '— কেউ না (কিউ)'] + $salesTeam->pluck('name', 'id')->all()" />
        @endif
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 text-sm text-ink-muted"><input type="checkbox" name="due" value="1" @checked(($filters['due'] ?? null) === '1') class="size-4 accent-[var(--accent)]"> আজকের ফলো-আপ</label>
            <x-button type="submit" variant="brand" size="sm">ফিল্টার</x-button>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.leads.assign') }}" x-data="{ selected: [] }">
        @csrf

        @if ($canAssign)
            <div x-show="selected.length > 0" x-cloak class="mb-3 flex flex-wrap items-center gap-3 rounded-2xl bg-accent-soft px-4 py-3">
                <span class="text-sm font-semibold text-accent-ink"><span x-text="selected.length"></span>টি লিড বাছাই করা হয়েছে</span>
                <select name="assigned_to" class="h-9 rounded-lg bg-surface px-3 text-sm ring-1 ring-line-strong ring-inset">
                    <option value="">— কিউতে ফেরত দিন</option>
                    @foreach ($salesTeam as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
                <x-button type="submit" size="sm">দায়িত্ব দিন</x-button>
            </div>
        @endif

        <x-admin.table :paginator="$leads">
            <x-slot:head>
                <tr>
                    @if ($canAssign)<th class="w-10 px-4 py-3"></th>@endif
                    <th class="px-4 py-3">লিড</th>
                    <th class="px-4 py-3">আগ্রহ</th>
                    <th class="px-4 py-3">অবস্থা</th>
                    <th class="px-4 py-3">দায়িত্বে</th>
                    <th class="px-4 py-3">ফলো-আপ</th>
                </tr>
            </x-slot:head>

            @forelse ($leads as $lead)
                <tr class="hover:bg-surface-muted/50">
                    @if ($canAssign)
                        <td class="px-4 py-3"><input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" x-model="selected" class="size-4 accent-[var(--accent)]"></td>
                    @endif
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.leads.show', $lead) }}" class="font-semibold text-ink hover:text-accent-ink">{{ $lead->displayName() }}</a>
                        <p class="text-xs text-ink-subtle">{{ $lead->phone }} · {{ $lead->created_at->diffForHumans() }}</p>
                        <x-badge class="mt-1">{{ $lead->type->label() }}</x-badge>
                    </td>
                    <td class="px-4 py-3 text-ink-muted">
                        {{ $lead->interests->map->label()->filter()->join(', ') ?: '—' }}
                        @if ($lead->class_level)<p class="text-xs text-ink-subtle">{{ $lead->class_level }} শ্রেণি</p>@endif
                    </td>
                    <td class="px-4 py-3"><x-badge :tone="$lead->status->tone()">{{ $lead->status->label() }}</x-badge></td>
                    <td class="px-4 py-3 text-ink-muted">{{ $lead->assignee?->name ?? '—' }}</td>
                    <td @class(['px-4 py-3 text-sm', 'font-semibold text-accent-ink' => $lead->next_follow_up_at?->isPast(), 'text-ink-muted' => ! $lead->next_follow_up_at?->isPast()])>
                        {{ $lead->next_follow_up_at?->format('d M, h:i A') ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-ink-subtle">কোনো লিড পাওয়া যায়নি।</td></tr>
            @endforelse
        </x-admin.table>
    </form>
</x-layouts.admin>
