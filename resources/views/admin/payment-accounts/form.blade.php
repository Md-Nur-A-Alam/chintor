@use('App\Enums\PaymentMethod')
@use('App\Models\PaymentAccount')

@php($isEditing = $account->exists)

<x-layouts.admin :title="$isEditing ? 'অ্যাকাউন্ট এডিট' : 'নতুন অ্যাকাউন্ট'">
    <x-admin.page-header :title="$isEditing ? 'পেমেন্ট অ্যাকাউন্ট এডিট' : 'নতুন পেমেন্ট অ্যাকাউন্ট'" />

    <form method="POST" action="{{ $isEditing ? route('admin.payment-accounts.update', $account) : route('admin.payment-accounts.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4 md:grid-cols-2">
                <x-form.select name="method" label="মাধ্যম" :value="$account->method?->value" :options="PaymentMethod::options()" />
                <x-form.select name="account_type" label="অ্যাকাউন্টের ধরন" :value="$account->account_type" :options="PaymentAccount::ACCOUNT_TYPES" />
                <x-form.field name="account_number" label="নম্বর / অ্যাকাউন্ট নম্বর" :value="$account->account_number" required />
                <x-form.field name="account_name" label="অ্যাকাউন্টের নাম" :value="$account->account_name" required />
                <x-form.field name="bank_name" label="ব্যাংকের নাম (ব্যাংক হলে)" :value="$account->bank_name" />
                <x-form.field name="branch" label="শাখা" :value="$account->branch" />
            </div>
            <div class="mt-4 grid gap-4">
                <x-form.translatable name="instructions" label="গ্রাহকের জন্য নির্দেশনা (ঐচ্ছিক)" :value="$account->instructions" />
                <div class="grid gap-4 md:grid-cols-2">
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$account->sort_order ?? 0" />
                    <div class="self-end pb-2"><x-form.checkbox name="is_active" label="চেকআউটে দেখাবে" :checked="$account->is_active" /></div>
                </div>
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">সেভ করুন</x-button>
            <x-button :href="route('admin.payment-accounts.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>
</x-layouts.admin>
