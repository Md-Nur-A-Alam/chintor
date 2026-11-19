@use('App\Support\TranslatableInput')

@props([
    'name',
    'label',
    'value' => null,
    'help' => 'প্রতি লাইনে একটি করে লিখুন।',
    'rows' => 5,
])

{{-- A translatable list edited as "one item per line" (bn + en). --}}
<x-form.translatable
    :name="$name"
    :label="$label"
    :value="TranslatableInput::linesForForm($value)"
    :multiline="true"
    :rows="$rows"
    :help="$help"
/>
