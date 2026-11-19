@php
    $providerLabels = ['r2' => 'Cloudflare R2', 'imgbb' => 'ImgBB', 'local' => 'এই সার্ভার'];
@endphp

<x-layouts.admin title="ছবি স্টোরেজ">
    <x-admin.page-header title="ছবি স্টোরেজ" description="ছবি কোথায় জমা হবে তা ঠিক করুন। ক্রেডেনশিয়াল এনক্রিপ্ট করে ডেটাবেসে রাখা হয়।" />

    <x-card class="mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm text-ink-muted">এখন নতুন ছবি জমা হচ্ছে:</span>
            <x-badge tone="success">{{ $providerLabels[$activeProvider] }}</x-badge>
            @foreach ($configured as $provider => $isConfigured)
                <x-badge :tone="$isConfigured ? 'info' : 'neutral'">
                    {{ $providerLabels[$provider] }}: {{ $isConfigured ? 'প্রস্তুত' : 'সেট করা নেই' }}
                </x-badge>
            @endforeach
        </div>
    </x-card>

    <form method="POST" action="{{ route('admin.settings.media.update') }}" class="grid gap-6">
        @csrf
        @method('PUT')

        <x-card title="কোন স্টোরেজ ব্যবহার হবে">
            <x-form.select
                name="driver"
                label="স্টোরেজ"
                :value="$media['driver']"
                :options="['auto' => 'স্বয়ংক্রিয় (R2 → ImgBB → এই সার্ভার)', 'r2' => 'Cloudflare R2', 'imgbb' => 'ImgBB', 'local' => 'এই সার্ভার (public/uploads)']"
                help="স্বয়ংক্রিয় রাখলে যেটার ক্রেডেনশিয়াল আছে সেটাই ব্যবহার হবে। আগের ছবিগুলো যেখানে আছে সেখানেই থাকবে।"
            />
        </x-card>

        <x-card title="ImgBB (প্ল্যান বি)" description="api.imgbb.com থেকে ফ্রি API key নিন।">
            <x-form.field
                name="imgbb_api_key"
                label="API key"
                type="password"
                autocomplete="off"
                :placeholder="$savedSecrets['imgbb_api_key'] ?? 'সেট করা নেই'"
                help="খালি রাখলে আগের key-ই থাকবে।"
            />
            @if ($savedSecrets['imgbb_api_key'])
                <label class="mt-3 inline-flex cursor-pointer items-center gap-2 text-sm text-ink-muted">
                    <input type="checkbox" name="clear[]" value="imgbb_api_key" class="size-4 accent-[var(--accent)]"> সেভ করা key মুছে ফেলুন
                </label>
            @endif
        </x-card>

        <x-card title="Cloudflare R2" description="R2 → Manage API tokens থেকে Access Key ও Secret নিন; বাকেটে Public access (r2.dev বা নিজস্ব ডোমেইন) চালু রাখুন।">
            <div class="grid gap-4 md:grid-cols-2">
                <x-form.field name="r2_endpoint" label="S3 API endpoint" type="url" :value="$media['r2_endpoint']" placeholder="https://<account-id>.r2.cloudflarestorage.com" />
                <x-form.field name="r2_bucket" label="বাকেটের নাম" :value="$media['r2_bucket']" placeholder="chintor-media" />
                <x-form.field name="r2_public_url" label="পাবলিক URL" type="url" :value="$media['r2_public_url']" placeholder="https://pub-xxxx.r2.dev বা https://media.chintor.com" />
                <div></div>
                <x-form.field name="r2_access_key_id" label="Access Key ID" type="password" autocomplete="off" :placeholder="$savedSecrets['r2_access_key_id'] ?? 'সেট করা নেই'" />
                <x-form.field name="r2_secret_access_key" label="Secret Access Key" type="password" autocomplete="off" :placeholder="$savedSecrets['r2_secret_access_key'] ?? 'সেট করা নেই'" />
            </div>
            @if ($savedSecrets['r2_access_key_id'] || $savedSecrets['r2_secret_access_key'])
                <div class="mt-4 flex flex-wrap gap-6 text-sm text-ink-muted">
                    <label class="inline-flex cursor-pointer items-center gap-2"><input type="checkbox" name="clear[]" value="r2_access_key_id" class="size-4 accent-[var(--accent)]"> Access Key মুছুন</label>
                    <label class="inline-flex cursor-pointer items-center gap-2"><input type="checkbox" name="clear[]" value="r2_secret_access_key" class="size-4 accent-[var(--accent)]"> Secret মুছুন</label>
                </div>
            @endif
        </x-card>

        <div><x-button type="submit">সেভ করুন</x-button></div>
    </form>

    <x-card title="সংযোগ পরীক্ষা" description="সেভ করার পর এখানে চেক করুন। ImgBB-তে একটি ছোট টেস্ট ছবি যায়, যা ৬০ সেকেন্ড পর নিজে মুছে যায়।" class="mt-6">
        <div class="flex flex-wrap gap-3">
            @foreach ($providerLabels as $provider => $label)
                <form method="POST" action="{{ route('admin.settings.media.test') }}">
                    @csrf
                    <input type="hidden" name="provider" value="{{ $provider }}">
                    <x-button type="submit" variant="secondary" size="sm" :disabled="! $configured[$provider]">{{ $label }} পরীক্ষা</x-button>
                </form>
            @endforeach
        </div>
    </x-card>
</x-layouts.admin>
