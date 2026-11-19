@use('App\Support\Districts')

@php($selectedDistricts = collect(old('districts', $zone->districts ?? [])))

<form method="POST" action="{{ $action }}" class="grid gap-4">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <x-form.translatable name="zone_name" label="জোনের নাম (যেমন: ঢাকার ভেতরে)" :value="$zone->name" required />
    <div class="grid gap-4 sm:grid-cols-3">
        <x-form.field name="charge" type="number" label="চার্জ (৳)" :value="$zone->charge" required />
        <x-form.field name="estimated_days" label="সময় (দিন)" :value="$zone->estimated_days" placeholder="1-2" />
        <x-form.field name="zone_sort_order" type="number" label="ক্রম" :value="$zone->sort_order ?? 0" />
    </div>

    <details class="rounded-xl ring-1 ring-line ring-inset">
        <summary class="cursor-pointer p-3 text-sm font-medium text-ink">জেলা বাছাই ({{ $selectedDistricts->count() }})</summary>
        <div class="grid max-h-64 grid-cols-2 gap-1 overflow-y-auto border-t border-line p-3 sm:grid-cols-4">
            @foreach (Districts::options() as $district => $districtLabel)
                <label class="flex items-center gap-2 text-sm text-ink-muted">
                    <input type="checkbox" name="districts[]" value="{{ $district }}" @checked($selectedDistricts->contains($district)) class="size-4 accent-[var(--accent)]">
                    {{ $districtLabel }}
                </label>
            @endforeach
        </div>
    </details>

    <div class="flex flex-wrap gap-6">
        <x-form.checkbox name="is_default" label="ডিফল্ট জোন (তালিকায় না থাকা সব জেলা)" :checked="$zone->is_default" />
        <x-form.checkbox name="is_active" label="সক্রিয়" :checked="$zone->is_active" />
    </div>
    <div><x-button type="submit" size="sm">সেভ করুন</x-button></div>
</form>
