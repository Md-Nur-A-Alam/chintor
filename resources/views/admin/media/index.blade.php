@php
    $providerLabels = ['r2' => 'R2', 'imgbb' => 'ImgBB', 'local' => 'সার্ভার'];
@endphp

<x-layouts.admin title="মিডিয়া">
    <x-admin.page-header title="মিডিয়া লাইব্রেরি" description="ছবি আপলোড করুন, প্রিভিউ দেখুন ও URL কপি করুন।" />

    <div class="mb-6 grid gap-6 lg:grid-cols-2">
        <x-card title="ছবি আপলোড" :description="'এখন জমা হবে: '.$providerLabels[$activeProvider].' · JPG, PNG, WebP, GIF · সর্বোচ্চ ৫ MB'">
            <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="grid gap-3">
                @csrf
                <input
                    type="file"
                    name="image"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    required
                    class="block w-full cursor-pointer rounded-xl bg-surface-muted text-sm text-ink-muted ring-1 ring-line ring-inset file:mr-3 file:cursor-pointer file:rounded-xl file:border-0 file:bg-brand file:px-4 file:py-2.5 file:font-semibold file:text-brand-ink"
                >
                @error('image')
                    <p class="text-sm text-accent-ink">{{ $message }}</p>
                @enderror
                <div><x-button type="submit" size="sm"><x-icon name="upload" class="size-4" /> আপলোড</x-button></div>
            </form>
        </x-card>

        <x-card title="YouTube প্রিভিউ" description="Unlisted ভিডিওর লিংক দিয়ে দেখে নিন সাইটে কেমন দেখাবে।">
            <form method="GET" class="flex gap-2">
                <input
                    type="url"
                    name="youtube"
                    value="{{ $youtubeUrl }}"
                    placeholder="https://youtu.be/..."
                    class="h-10 flex-1 rounded-xl bg-surface px-3.5 text-sm ring-1 ring-line-strong outline-none ring-inset focus:ring-2 focus:ring-focus"
                >
                <x-button type="submit" variant="secondary" size="sm">দেখুন</x-button>
            </form>
            @if ($youtubeUrl !== '')
                <x-youtube :url="$youtubeUrl" class="mt-4" />
            @endif
        </x-card>
    </div>

    @if ($mediaItems->isEmpty())
        <x-card><p class="py-8 text-center text-ink-subtle">এখনো কোনো ছবি আপলোড হয়নি।</p></x-card>
    @else
        <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($mediaItems as $item)
                <li x-data="{ copied: false }" class="overflow-hidden rounded-card bg-surface shadow-card ring-1 ring-line">
                    <a href="{{ $item->url }}" target="_blank" rel="noopener" class="block aspect-square bg-surface-muted">
                        <img src="{{ $item->url }}" alt="{{ $item->original_name }}" loading="lazy" class="size-full object-cover">
                    </a>
                    <div class="grid gap-2 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-xs text-ink-muted" title="{{ $item->original_name }}">{{ $item->original_name }}</p>
                            <x-badge>{{ $providerLabels[$item->provider] ?? $item->provider }}</x-badge>
                        </div>
                        <div class="flex gap-1.5">
                            <button
                                type="button"
                                x-on:click="navigator.clipboard.writeText(@js($item->url)); copied = true; setTimeout(() => copied = false, 1500)"
                                class="inline-flex flex-1 cursor-pointer items-center justify-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold ring-1 ring-line ring-inset hover:bg-surface-muted"
                            >
                                <x-icon name="copy" class="size-3.5" />
                                <span x-text="copied ? 'কপি হয়েছে' : 'URL কপি'">URL কপি</span>
                            </button>
                            <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('ছবিটি মুছে ফেলবেন?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="grid size-8 cursor-pointer place-items-center rounded-lg text-accent-ink ring-1 ring-line ring-inset hover:bg-accent-soft" title="মুছুন">
                                    <x-icon name="trash" class="size-3.5" />
                                    <span class="sr-only">মুছুন</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $mediaItems->links() }}</div>
    @endif
</x-layouts.admin>
