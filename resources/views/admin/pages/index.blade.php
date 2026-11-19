@use('App\Models\Page')

@php
    // Pages the footer links to; shown so staff know what to create.
    $expectedSlugs = ['about' => 'আমাদের সম্পর্কে', 'contact' => 'যোগাযোগ', 'refund-policy' => 'রিফান্ড নীতি', 'privacy-policy' => 'প্রাইভেসি নীতি', 'terms' => 'শর্তাবলি', 'delivery-policy' => 'ডেলিভারি নীতি'];
    $missingSlugs = array_diff_key($expectedSlugs, $pages->keyBy('slug')->all());
@endphp

<x-layouts.admin title="পেজ">
    <x-admin.page-header title="পেজ" description="আমাদের সম্পর্কে, যোগাযোগ ও নীতিমালা। লেখা Markdown-এ (## শিরোনাম, **বোল্ড**, - তালিকা)।">
        <x-slot:actions>
            <x-button :href="route('admin.pages.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন পেজ</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($missingSlugs !== [])
        <div class="mb-6 rounded-2xl bg-sun-300/20 px-4 py-3 text-sm text-ink ring-1 ring-sun-500/30 ring-inset">
            ফুটারের এই লিংকগুলোর পেজ এখনো নেই (এই URL নাম দিয়ে তৈরি করুন):
            <strong>{{ collect($missingSlugs)->map(fn ($label, $slug) => "{$label} ({$slug})")->join(', ') }}</strong>
        </div>
    @endif

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">পেজ</th><th class="px-4 py-3">ধরন</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($pages as $page)
            <tr>
                <td class="px-4 py-3"><p class="font-semibold text-ink">{{ $page->translate('title', 'bn') }}</p><p class="text-xs text-ink-subtle">/info/{{ $page->slug }}</p></td>
                <td class="px-4 py-3 text-ink-muted">{{ Page::TYPES[$page->type] ?? $page->type }}</td>
                <td class="px-4 py-3"><x-badge :tone="$page->status->tone()">{{ $page->status->label() }}</x-badge></td>
                <td class="px-4 py-3 text-right"><x-button :href="route('admin.pages.edit', $page)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /> এডিট</x-button></td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-10 text-center text-ink-subtle">কোনো পেজ নেই।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
