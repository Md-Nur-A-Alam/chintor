@use('App\Support\Districts')

<x-layouts.admin title="বিক্রয় সেটিংস">
    <x-admin.page-header title="বিক্রয় সেটিংস" description="রিফান্ড নীতি ও বইয়ের ডেলিভারি চার্জ।" />

    <form method="POST" action="{{ route('admin.settings.sales.update') }}">
        @csrf
        @method('PUT')
        <x-card title="রিফান্ড নীতি" description="নতুন ভর্তিতে প্রযোজ্য। প্রতিটি ভর্তির সময়ের নীতি ভর্তির সাথে সংরক্ষিত থাকে।">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="grid gap-1.5">
                    <span class="text-sm font-medium text-ink">গণনার ধরন</span>
                    <select name="mode" class="h-11 rounded-xl bg-surface px-3 ring-1 ring-line-strong ring-inset">
                        <option value="days" @selected($refundPolicy['mode'] === 'days')>দিন (প্রথম ক্লাস থেকে)</option>
                        <option value="classes" disabled>প্রথম N-টি ক্লাস (LMS চালু হলে)</option>
                    </select>
                </div>
                <x-form.field name="window" type="number" label="সময়সীমা (দিন)" :value="$refundPolicy['window']" required />
                <x-form.field name="percent" type="number" label="রিফান্ড (%)" :value="$refundPolicy['percent']" required />
            </div>
            <div class="mt-4"><x-button type="submit" size="sm">সেভ করুন</x-button></div>
        </x-card>
    </form>

    <x-card id="zones" class="mt-6 scroll-mt-24" title="ডেলিভারি জোন" description="জোনে যে জেলাগুলো বাছাই করবেন সেখানে সেই চার্জ; বাকি সব জেলায় “ডিফল্ট” জোনের চার্জ।">
        <div class="grid gap-3">
            @foreach ($zones as $zone)
                <details class="rounded-2xl ring-1 ring-line ring-inset">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4">
                        <span class="font-semibold text-ink">{{ $zone->translate('name', 'bn') }} @if ($zone->is_default)<x-badge tone="info">ডিফল্ট</x-badge>@endif</span>
                        <span class="text-sm text-ink-muted">৳{{ $zone->charge }} · {{ count($zone->districts ?? []) }} জেলা</span>
                    </summary>
                    <div class="border-t border-line p-4">
                        @include('admin.settings.partials.zone-form', ['zone' => $zone, 'action' => route('admin.delivery-zones.update', $zone), 'method' => 'PUT'])
                        <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone) }}" onsubmit="return confirm('জোনটি মুছে ফেলবেন?')" class="mt-3">
                            @csrf @method('DELETE')
                            <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> মুছুন</x-button>
                        </form>
                    </div>
                </details>
            @endforeach

            <details class="rounded-2xl bg-surface-muted ring-1 ring-line ring-inset" @if ($zones->isEmpty()) open @endif>
                <summary class="flex cursor-pointer list-none items-center gap-2 p-4 font-semibold text-accent-ink"><x-icon name="plus" class="size-4" /> নতুন জোন</summary>
                <div class="border-t border-line p-4">
                    @include('admin.settings.partials.zone-form', ['zone' => new App\Models\DeliveryZone(['is_active' => true]), 'action' => route('admin.delivery-zones.store'), 'method' => 'POST'])
                </div>
            </details>
        </div>
    </x-card>
</x-layouts.admin>
