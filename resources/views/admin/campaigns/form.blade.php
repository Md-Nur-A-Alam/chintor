@use('App\Enums\CampaignChannel')
@use('App\Support\HeroBanner')

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

                <x-form.image name="image" label="বর্গাকার ব্যানার" :value="$campaign->image_url" help="১০৮০×১০৮০ px (Facebook/Instagram পোস্টের মাপ)। হিরোর ডান পাশে স্লাইডারে ও ক্যাম্পেইন সেকশনে দেখায়।" />

                <fieldset x-data="{ overlay: @js((int) old('banner_overlay', $campaign->banner_overlay ?? HeroBanner::DEFAULT_OVERLAY)) }" class="grid gap-4 rounded-2xl p-4 ring-1 ring-line ring-inset">
                    <legend class="px-1 text-sm font-semibold text-ink">পূর্ণ হিরো ব্যানার (ঐচ্ছিক)</legend>
                    <p class="text-sm text-ink-muted">দিলে ক্যাম্পেইন চলাকালীন পুরো হিরো জুড়ে এই ব্যানার দেখাবে, তার ওপরে ক্যাম্পেইনের শিরোনাম, সংক্ষেপ, বাটন ও কাউন্টডাউন। মাপ: ডেস্কটপ <strong class="text-ink">{{ HeroBanner::DESKTOP_SIZE }} px</strong>, মোবাইল <strong class="text-ink">{{ HeroBanner::MOBILE_SIZE }} px</strong>। লেখা যেদিকে থাকবে, ব্যানারের মূল ছবি তার উল্টো দিকে রাখুন।</p>
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-form.image name="banner" label="ডেস্কটপ ব্যানার" :value="$campaign->banner_url" />
                        <x-form.image name="banner_mobile" label="মোবাইল ব্যানার (ঐচ্ছিক)" :value="$campaign->banner_mobile_url" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-form.select name="banner_align" label="লেখা কোথায় থাকবে" :value="$campaign->banner_align ?? 'left'" :options="HeroBanner::ALIGNMENTS" />
                        <div class="grid gap-1.5">
                            <label for="campaign-overlay" class="text-sm font-medium text-ink">ওভারলের গাঢ়ত্ব: <span x-text="overlay + '%'"></span></label>
                            <input id="campaign-overlay" type="range" name="banner_overlay" min="0" max="{{ HeroBanner::MAX_OVERLAY }}" step="5" x-model="overlay" value="{{ old('banner_overlay', $campaign->banner_overlay ?? HeroBanner::DEFAULT_OVERLAY) }}" class="h-11 accent-[var(--accent)]">
                        </div>
                    </div>
                </fieldset>

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
