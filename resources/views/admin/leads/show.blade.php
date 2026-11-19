@use('App\Enums\LeadStatus')
@use('App\Models\LeadActivity')
@use('App\Support\Money')
@use('App\Support\PhoneNumber')

@php
    $whatsappUrl = PhoneNumber::whatsappUrl($lead->whatsapp ?: $lead->phone);
@endphp

<x-layouts.admin :title="$lead->displayName()">
    <x-admin.page-header :title="$lead->displayName()" :description="$lead->reference.' · '.$lead->type->label().' · '.$lead->created_at->format('d M Y, h:i A')">
        <x-slot:actions>
            <x-button :href="'tel:'.$lead->phone" variant="brand" size="sm"><x-icon name="phone" class="size-4" /> কল করুন</x-button>
            @if ($whatsappUrl)
                <x-button :href="$whatsappUrl" target="_blank" variant="secondary" size="sm"><x-icon name="message" class="size-4" /> WhatsApp</x-button>
            @endif
            @can('enrollments.manage')
                <x-button :href="route('admin.enrollments.create', ['lead' => $lead->id])" size="sm"><x-icon name="check" class="size-4" /> ভর্তি করুন</x-button>
            @endcan
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="grid gap-6">
            {{-- Log a call or note --}}
            <x-card title="কল / নোট যোগ করুন">
                <form method="POST" action="{{ route('admin.leads.activities.store', $lead) }}" x-data="{ type: 'call' }" class="grid gap-3">
                    @csrf
                    <div class="inline-flex w-fit rounded-full bg-surface-muted p-1 ring-1 ring-line ring-inset">
                        @foreach (['call' => 'কল', 'note' => 'নোট'] as $type => $typeLabel)
                            <label class="cursor-pointer rounded-full px-4 py-1.5 text-sm font-medium has-[:checked]:bg-surface-raised has-[:checked]:shadow-sm">
                                <input type="radio" name="type" value="{{ $type }}" x-model="type" class="sr-only"> {{ $typeLabel }}
                            </label>
                        @endforeach
                    </div>
                    <div x-show="type === 'call'">
                        <x-form.select name="outcome" label="ফলাফল" :options="LeadActivity::CALL_OUTCOMES" placeholder="—" />
                    </div>
                    <x-form.textarea name="body" label="কী কথা হলো" rows="3" />
                    <div><x-button type="submit" size="sm">যোগ করুন</x-button></div>
                </form>
            </x-card>

            {{-- Timeline --}}
            <x-card title="টাইমলাইন">
                <ol class="grid gap-4">
                    @forelse ($lead->activities as $activity)
                        <li class="flex gap-3">
                            <span class="mt-1 grid size-8 shrink-0 place-items-center rounded-full bg-surface-muted text-ink-muted">
                                <x-icon :name="match ($activity->type) { 'call' => 'phone', 'assignment' => 'users', 'status_change' => 'refund', default => 'file' }" class="size-4" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm text-ink">
                                    @if ($activity->outcome)<strong>{{ LeadActivity::CALL_OUTCOMES[$activity->outcome] ?? $activity->outcome }}</strong> · @endif
                                    @if ($activity->type === LeadActivity::TypeFormResubmitted)<strong>আবার ফর্ম পূরণ করেছেন</strong> @endif
                                    {{ $activity->body }}
                                </p>
                                <p class="text-xs text-ink-subtle">{{ $activity->user?->name ?? 'ওয়েবসাইট' }} · {{ $activity->created_at->format('d M, h:i A') }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-ink-subtle">এখনো কিছু লেখা হয়নি।</li>
                    @endforelse
                </ol>
            </x-card>

            @if ($lead->enrollments->isNotEmpty())
                <x-card title="এনরোলমেন্ট">
                    <ul class="grid gap-2 text-sm">
                        @foreach ($lead->enrollments as $enrollment)
                            <li class="flex items-center justify-between gap-3">
                                <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="font-medium text-ink hover:text-accent-ink">{{ $enrollment->reference }} · {{ $enrollment->course->translate('title', 'bn') }}</a>
                                <span class="flex items-center gap-2">{{ Money::taka($enrollment->payable_amount, 'bn') }} <x-badge :tone="$enrollment->status->tone()">{{ $enrollment->status->label() }}</x-badge></span>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif
        </div>

        <aside class="grid content-start gap-6">
            <x-card title="অবস্থা">
                <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="grid gap-3" x-data="{ status: @js($lead->status->value) }">
                    @csrf @method('PUT')
                    <x-form.select name="status" label="অবস্থা" :value="$lead->status->value" :options="LeadStatus::options()" x-model="status" />
                    <x-form.field name="next_follow_up_at" type="datetime-local" label="পরের ফলো-আপ" :value="$lead->next_follow_up_at?->format('Y-m-d\TH:i')" />
                    <div x-show="status === 'lost'" x-cloak><x-form.field name="lost_reason" label="হারানোর কারণ" :value="$lead->lost_reason" /></div>
                    <x-button type="submit" size="sm">সেভ করুন</x-button>
                </form>
            </x-card>

            @if ($canAssign)
                <x-card title="দায়িত্ব">
                    <form method="POST" action="{{ route('admin.leads.assign') }}" class="grid gap-3">
                        @csrf
                        <input type="hidden" name="lead_ids[]" value="{{ $lead->id }}">
                        <x-form.select name="assigned_to" label="সেলস এক্সিকিউটিভ" :value="$lead->assigned_to" placeholder="— কিউতে রাখুন" :options="$salesTeam->pluck('name', 'id')->all()" />
                        <x-button type="submit" variant="secondary" size="sm">দায়িত্ব দিন</x-button>
                    </form>
                </x-card>
            @endif

            <x-card title="তথ্য">
                <dl class="grid gap-2 text-sm">
                    @foreach ([
                        'শিক্ষার্থী' => $lead->student_name,
                        'অভিভাবক' => $lead->guardian_name,
                        'ফোন' => $lead->phone,
                        'ইমেইল' => $lead->email,
                        'শ্রেণি' => $lead->class_level,
                        'বয়স' => $lead->age,
                        'আগ্রহ' => $lead->interests->map->label()->filter()->join(', '),
                        'বার্তা' => $lead->message,
                        'দায়িত্বে' => $lead->assignee?->name,
                        'উৎস' => $lead->source->label(),
                        'ক্যাম্পেইন' => collect([$lead->utm_source, $lead->utm_medium, $lead->utm_campaign])->filter()->join(' / '),
                        'প্রথম পেজ' => $lead->landing_page,
                    ] as $label => $value)
                        @if (filled($value))
                            <div><dt class="text-ink-subtle">{{ $label }}</dt><dd class="break-words font-medium text-ink">{{ $value }}</dd></div>
                        @endif
                    @endforeach
                </dl>
            </x-card>

            @if ($lead->assessment)
                <x-card title="অ্যাসেসমেন্ট">
                    <p class="flex items-center justify-between text-sm"><x-badge :tone="$lead->assessment->status->tone()">{{ $lead->assessment->status->label() }}</x-badge> <span class="text-ink-muted">{{ $lead->assessment->scheduled_at?->format('d M, h:i A') }}</span></p>
                    @can('assessments.manage')
                        <x-button :href="route('admin.assessments.edit', $lead->assessment)" variant="secondary" size="sm" class="mt-3 w-full">সময় ঠিক করুন / ফলাফল</x-button>
                    @endcan
                </x-card>
            @endif
        </aside>
    </div>
</x-layouts.admin>
