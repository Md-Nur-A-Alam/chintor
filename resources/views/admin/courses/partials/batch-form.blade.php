@use('App\Enums\BatchStatus')
@use('App\Models\CourseBatch')

{{-- One live batch. Used for both "add" and "edit" on the course page. --}}
@php
    $selectedDays = collect(old('class_days', $batch->class_days ?? []));
@endphp

<form method="POST" action="{{ $action }}" class="grid gap-4">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-form.field name="code" label="ব্যাচ কোড" :value="$batch->code" placeholder="MO-P-2601" required />
        <x-form.select name="batch_status" label="অবস্থা" :value="$batch->status?->value" :options="BatchStatus::options()" />
        <x-form.field name="seat_limit" type="number" label="আসন সংখ্যা" :value="$batch->seat_limit" help="খালি = সীমা নেই" />
        <x-form.field name="meeting_url" type="url" label="Zoom/Meet লিংক" :value="$batch->meeting_url" />
    </div>

    <x-form.translatable name="name" label="ব্যাচের নাম (ঐচ্ছিক, যেমন: সন্ধ্যার ব্যাচ)" :value="$batch->name" />

    <fieldset>
        <legend class="mb-2 text-sm font-medium text-ink">ক্লাসের দিন</legend>
        <div class="flex flex-wrap gap-2">
            @foreach (CourseBatch::DAYS as $day => $dayLabel)
                <label class="cursor-pointer rounded-full px-3 py-1.5 text-sm ring-1 ring-line ring-inset has-[:checked]:bg-accent has-[:checked]:text-on-accent">
                    <input type="checkbox" name="class_days[]" value="{{ $day }}" @checked($selectedDays->contains($day)) class="sr-only">
                    {{ $dayLabel }}
                </label>
            @endforeach
        </div>
    </fieldset>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-form.field name="class_start_time" type="time" label="ক্লাস শুরু" :value="$batch->class_start_time ? substr($batch->class_start_time, 0, 5) : null" />
        <x-form.field name="class_end_time" type="time" label="ক্লাস শেষ" :value="$batch->class_end_time ? substr($batch->class_end_time, 0, 5) : null" />
        <x-form.field name="orientation_at" type="datetime-local" label="ওরিয়েন্টেশন" :value="$batch->orientation_at?->format('Y-m-d\TH:i')" />
        <x-form.field name="starts_on" type="date" label="শুরুর তারিখ" :value="$batch->starts_on?->toDateString()" />
        <x-form.field name="ends_on" type="date" label="শেষের তারিখ" :value="$batch->ends_on?->toDateString()" />
        <x-form.field name="enrollment_deadline" type="date" label="ভর্তির শেষ দিন" :value="$batch->enrollment_deadline?->toDateString()" />
    </div>
    <div><x-button type="submit" size="sm">সেভ করুন</x-button></div>
</form>
