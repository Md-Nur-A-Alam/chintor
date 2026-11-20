@use('App\Enums\CampaignChannel')

@php($isEditing = $campaign->exists)

<x-layouts.admin :title="$isEditing ? 'ক্যাম্পেইন এডিট' : 'নতুন ক্যাম্পেইন'">
    <x-admin.page-header :title="$isEditing ? $campaign->translate('title', 'bn') : 'নতুন ক্যাম্পেইন'" />

    <form method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.campaigns.update', $campaign) : route('admin.campaigns.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4">
                <x-form.translatable name="title" label="শিরোনাম" :value="$campaign->title" required help="ব্যানারের ছবির বিকল্প লেখা (alt) হিসেবেও ব্যবহার হয়।" />
                <x-form.translatable name="summary" label="সংক্ষেপ (ঐচ্ছিক)" :value="$campaign->summary" :multiline="true" :rows="2" />

                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.select name="channel" label="কোথায় চলছে" :value="$campaign->channel?->value ?? 'website'" :options="CampaignChannel::options()" required />
                    <x-form.field name="link_url" label="লিংক" :value="$campaign->link_url" placeholder="/start বা https://facebook.com/..." help="সাইটের পেজ “/” দিয়ে, Facebook/YouTube লিংক https:// দিয়ে।" />
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$campaign->sort_order ?? 0" />
                </div>
                <x-form.translatable name="button_label" label="বাটনের লেখা (ঐচ্ছিক)" :value="$campaign->button_label" :help="'ফাঁকা থাকলে: '.__('site.home.campaign.details', [], 'bn')" />

                <x-form.image name="image" label="ব্যানার ছবি" :value="$campaign->image_url" help="বর্গাকার ১০৮০×১০৮০ px (Facebook/Instagram পোস্টের মাপ) — একই ডিজাইন সব জায়গায় ব্যবহার করা যায়। হিরো ব্যানারে দেখাতে ছবি লাগবে।" />

                <div class="grid gap-4 md:grid-cols-2">
                    <x-form.field name="starts_at" type="datetime-local" label="শুরু (ঐচ্ছিক)" :value="$campaign->starts_at?->format('Y-m-d\TH:i')" help="ফাঁকা থাকলে এখনই শুরু।" />
                    <x-form.field name="ends_at" type="datetime-local" label="শেষ (ঐচ্ছিক)" :value="$campaign->ends_at?->format('Y-m-d\TH:i')" help="দিলে ওয়েবসাইটে কাউন্টডাউন দেখাবে, আর সময় শেষে নিজে থেকেই সরে যাবে।" />
                </div>

                <div class="grid gap-3 rounded-2xl bg-surface-muted p-4">
                    <x-form.checkbox name="is_active" label="চালু" :checked="$campaign->is_active" />
                    <x-form.checkbox name="show_in_hero" label="হোমপেজের হিরোতে ব্যানার হিসেবে দেখান" :checked="$campaign->show_in_hero" />
                    <x-form.checkbox name="show_in_bar" label="সব পেজের ওপরে ঘোষণা বারে দেখান (একসাথে একটিই দেখায়)" :checked="$campaign->show_in_bar" />
                    <p class="text-xs text-ink-subtle">চালু থাকলে হোমপেজের “চলমান ক্যাম্পেইন ও ইভেন্ট” সেকশনে সবসময় দেখায় (সেকশনটি চালু থাকলে)।</p>
                </div>
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">সেভ করুন</x-button>
            <x-button :href="route('admin.campaigns.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>
</x-layouts.admin>
