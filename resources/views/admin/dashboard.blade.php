<x-layouts.admin title="ড্যাশবোর্ড">
    <x-admin.page-header :title="'স্বাগতম, '.$staff->name" :description="$staff->roles->pluck('label')->join(', ').($staff->designation ? ' · '.$staff->designation : '')" />

    @if ($cards !== [])
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($cards as $card)
                @php($tag = $card['url'] ? 'a' : 'div')
                <{{ $tag }} @if ($card['url']) href="{{ $card['url'] }}" @endif class="group rounded-card bg-surface p-5 shadow-card ring-1 ring-line transition hover:ring-line-strong">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-ink-muted">{{ $card['label'] }}</span>
                        <span @class([
                            'grid size-9 place-items-center rounded-xl',
                            'bg-accent-soft text-accent-ink' => in_array($card['tone'], ['danger', 'warning'], true),
                            'bg-brand-soft text-brand' => ! in_array($card['tone'], ['danger', 'warning'], true),
                        ])><x-icon :name="$card['icon']" class="size-4" /></span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-ink">{{ $card['value'] }}</p>
                </{{ $tag }}>
            @endforeach
        </div>
    @endif

    @if ($recentLeads->isNotEmpty())
        <x-card title="সাম্প্রতিক খোলা লিড" class="mt-6">
            <ul class="divide-y divide-line">
                @foreach ($recentLeads as $lead)
                    <li class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <div>
                            <a href="{{ route('admin.leads.show', $lead) }}" class="font-semibold text-ink hover:text-accent-ink">{{ $lead->displayName() }}</a>
                            <p class="text-xs text-ink-subtle">{{ $lead->phone }} · {{ $lead->type->label() }} · {{ $lead->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-ink-muted">{{ $lead->assignee?->name ?? 'কিউতে' }}</span>
                            <x-badge :tone="$lead->status->tone()">{{ $lead->status->label() }}</x-badge>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-card>
    @endif

    <x-card title="আপনার প্রোফাইল" class="mt-6">
        <dl class="grid gap-x-8 gap-y-4 text-sm sm:grid-cols-3">
            @foreach ([
                'অফিস আইডি' => $staff->office_id,
                'ফোন' => $staff->phone,
                'ইমেইল' => $staff->email,
                'যোগদান' => $staff->joined_on?->format('d/m/Y'),
            ] as $label => $value)
                <div><dt class="text-ink-subtle">{{ $label }}</dt><dd class="mt-0.5 font-medium text-ink">{{ $value ?: '—' }}</dd></div>
            @endforeach
        </dl>
    </x-card>
</x-layouts.admin>
