@props([
    'name',
    'label',
    'value' => [],
    'multiline' => false,
    'rows' => 4,
    'required' => false,
    'help' => null,
])

{{--
    Bangla + English inputs for one translatable field, posted as
    name[bn] and name[en]. Bangla is required when the field is required;
    English falls back to Bangla on the website when left empty.
--}}
<fieldset class="grid gap-3 rounded-2xl p-4 ring-1 ring-line ring-inset">
    <legend class="px-1 text-sm font-semibold text-ink">
        {{ $label }}
        @if ($required)
            <span class="text-accent-ink" aria-hidden="true">*</span>
        @endif
    </legend>

    <div class="grid gap-3 md:grid-cols-2">
        @foreach (['bn' => 'বাংলা', 'en' => 'English'] as $locale => $localeLabel)
            @if ($multiline)
                <x-form.textarea
                    :name="$name.'['.$locale.']'"
                    :label="$localeLabel"
                    :value="$value[$locale] ?? null"
                    :rows="$rows"
                    :required="$required && $locale === 'bn'"
                    :lang="$locale"
                />
            @else
                <x-form.field
                    :name="$name.'['.$locale.']'"
                    :label="$localeLabel"
                    :value="$value[$locale] ?? null"
                    :required="$required && $locale === 'bn'"
                    :lang="$locale"
                />
            @endif
        @endforeach
    </div>

    @if ($help)
        <p class="text-xs text-ink-subtle">{{ $help }}</p>
    @endif
</fieldset>
