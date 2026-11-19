@props([
    'name',
    'label',
    'value' => null,
    'help' => null,
])

{{--
    Image input: upload a file (stored in the active media storage) or paste
    a URL from the media library. Posts {name}_file, {name}_url, {name}_remove;
    handled by the HandlesImageInput controller trait.
--}}
<fieldset class="grid gap-3 rounded-2xl p-4 ring-1 ring-line ring-inset">
    <legend class="px-1 text-sm font-semibold text-ink">{{ $label }}</legend>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
        <div class="grid size-28 shrink-0 place-items-center overflow-hidden rounded-xl bg-surface-muted ring-1 ring-line">
            @if ($value)
                <img src="{{ $value }}" alt="" class="size-full object-cover">
            @else
                <x-icon name="image" class="size-8 text-ink-subtle" />
            @endif
        </div>

        <div class="grid flex-1 gap-3">
            <input
                type="file"
                name="{{ $name }}_file"
                accept="image/jpeg,image/png,image/webp"
                class="block w-full cursor-pointer rounded-xl bg-surface-muted text-sm text-ink-muted ring-1 ring-line ring-inset file:mr-3 file:cursor-pointer file:rounded-xl file:border-0 file:bg-brand file:px-4 file:py-2 file:font-semibold file:text-brand-ink"
            >
            <input
                type="url"
                name="{{ $name }}_url"
                value="{{ old($name.'_url') }}"
                placeholder="অথবা মিডিয়া লাইব্রেরি থেকে ছবির URL পেস্ট করুন"
                class="h-10 w-full rounded-xl bg-surface px-3.5 text-sm ring-1 ring-line-strong outline-none ring-inset focus:ring-2 focus:ring-focus"
            >
            @if ($value)
                <label class="inline-flex items-center gap-2 text-sm text-ink-muted">
                    <input type="checkbox" name="{{ $name }}_remove" value="1" class="size-4 accent-[var(--accent)]"> ছবিটি সরিয়ে দিন
                </label>
            @endif
        </div>
    </div>

    @if ($help)
        <p class="text-xs text-ink-subtle">{{ $help }}</p>
    @endif
    @error($name.'_file') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
    @error($name.'_url') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
</fieldset>
