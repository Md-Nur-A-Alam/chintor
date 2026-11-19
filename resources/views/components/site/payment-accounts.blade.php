@use('App\Models\PaymentAccount')

@props([
    'accounts',
])

{{-- Where to send money (semi-automated payments). Numbers come from the admin panel. --}}
@if ($accounts->isEmpty())
    <p class="rounded-2xl bg-surface-muted p-4 text-sm text-ink-muted">{{ __('site.checkout.no_accounts') }}</p>
@else
    <ul class="grid gap-3 sm:grid-cols-2">
        @foreach ($accounts as $account)
            <li x-data="{ copied: false }" class="rounded-2xl bg-surface-muted p-4 ring-1 ring-line ring-inset">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-ink">{{ $account->method->label() }}</span>
                    <span class="text-xs text-ink-subtle">{{ PaymentAccount::ACCOUNT_TYPES[$account->account_type] ?? $account->account_type }}</span>
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="font-mono text-lg font-semibold tracking-wide text-ink">{{ $account->account_number }}</span>
                    <button
                        type="button"
                        x-on:click="navigator.clipboard.writeText(@js($account->account_number)); copied = true; setTimeout(() => copied = false, 1500)"
                        class="grid size-8 cursor-pointer place-items-center rounded-lg text-ink-subtle ring-1 ring-line hover:text-ink"
                        aria-label="Copy"
                    >
                        <x-icon name="copy" class="size-4" x-show="! copied" />
                        <x-icon name="check" class="size-4 text-brand" x-show="copied" x-cloak />
                    </button>
                </div>
                <p class="text-sm text-ink-muted">{{ $account->account_name }}@if ($account->bank_name) · {{ $account->bank_name }}@endif @if ($account->branch) · {{ $account->branch }}@endif</p>
                @if ($account->translate('instructions'))
                    <p class="mt-2 text-xs text-ink-subtle">{{ $account->translate('instructions') }}</p>
                @endif
            </li>
        @endforeach
    </ul>
@endif
