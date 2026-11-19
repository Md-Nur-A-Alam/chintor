@use('App\Support\Localization')

@props([
    'form' => 'callback',
    'courseId' => null,
    'compact' => false,
])

{{-- Short lead form (name + phone). Reused on the home page, course pages and contact page. --}}
<form method="POST" action="{{ Localization::route('leads.store') }}" {{ $attributes->class(['grid gap-3']) }}>
    @csrf
    <input type="hidden" name="form" value="{{ $form }}">
    @if ($courseId)
        <input type="hidden" name="course_ids[]" value="{{ $courseId }}">
    @endif
    {{-- Honeypot for bots; hidden from people and screen readers. --}}
    <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

    <div @class(['grid gap-3', 'sm:grid-cols-2' => ! $compact])>
        <x-form.field name="guardian_name" :label="__('site.lead.guardian_name')" autocomplete="name" />
        <x-form.field name="phone" type="tel" :label="__('site.lead.phone')" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" required />
    </div>

    <label class="flex items-start gap-2.5 text-sm text-ink-muted">
        <input type="checkbox" name="consent" value="1" required class="mt-1 size-4 shrink-0 accent-[var(--accent)]">
        <span>{{ __('site.lead.consent') }}</span>
    </label>
    @error('consent')
        <p class="text-sm text-accent-ink">{{ $message }}</p>
    @enderror

    <x-button type="submit" size="lg" class="w-full sm:w-auto">{{ __('site.cta.request_callback') }}</x-button>
</form>
