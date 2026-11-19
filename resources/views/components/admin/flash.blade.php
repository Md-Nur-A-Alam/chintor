{{-- Success message after a redirect, and any non-field error. --}}
@if (session('status'))
    <div role="status" class="mb-6 flex items-start gap-3 rounded-2xl bg-brand-soft px-4 py-3 text-sm font-medium text-brand ring-1 ring-brand/20 ring-inset">
        <x-icon name="check" class="mt-0.5 size-4" />
        <span>{{ session('status') }}</span>
    </div>
@endif

@foreach (['connection', 'user', 'general'] as $errorKey)
    @error($errorKey)
        <div role="alert" class="mb-6 rounded-2xl bg-accent-soft px-4 py-3 text-sm font-medium text-accent-ink ring-1 ring-accent-ink/20 ring-inset">
            {{ $message }}
        </div>
    @enderror
@endforeach
