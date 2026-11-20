@use('App\Services\HomePageContent')

<x-layouts.admin title="হোমপেজ">
    <x-admin.page-header title="হোমপেজ" description="হিরো সেকশনের লেখা ও বাটন, পরিসংখ্যান, এবং কোন সেকশন কোন ক্রমে দেখাবে। ফাঁকা লেখা রাখলে ডিফল্ট লেখা দেখায়।">
        <x-slot:actions>
            <x-button :href="route('home')" target="_blank" variant="secondary" size="sm"><x-icon name="external" class="size-4" /> ওয়েবসাইটে দেখুন</x-button>
            <x-button :href="route('admin.campaigns.index')" variant="secondary" size="sm"><x-icon name="megaphone" class="size-4" /> ক্যাম্পেইন</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.homepage.update') }}" class="grid gap-6">
        @csrf
        @method('PUT')

        <x-card title="হিরো সেকশন" description="ওয়েবসাইটে ঢুকেই প্রথম যা চোখে পড়ে। ডান পাশে চলমান ক্যাম্পেইনের ব্যানার দেখায়; ক্যাম্পেইন না থাকলে ভিডিও, ভিডিও না থাকলে লোগো।">
            <div class="grid gap-4">
                <x-form.translatable name="hero[eyebrow]" label="ছোট লাইন (শিরোনামের ওপরে)" :value="$hero['eyebrow']" :help="'ডিফল্ট: '.__('site.home.eyebrow', [], 'bn')" />
                <x-form.translatable name="hero[title]" label="শিরোনাম" :value="$hero['title']" :help="'ডিফল্ট: '.__('site.home.headline', [], 'bn')" />
                <x-form.translatable name="hero[subtitle]" label="সাবটাইটেল" :value="$hero['subtitle']" :multiline="true" :rows="3" />

                <fieldset class="grid gap-3 rounded-2xl p-4 ring-1 ring-line ring-inset">
                    <legend class="px-1 text-sm font-semibold text-ink">বাটন (সর্বোচ্চ {{ HomePageContent::MAX_BUTTONS }}টি)</legend>
                    <p class="text-xs text-ink-subtle">সাইটের পেজের লিংক "/" দিয়ে লিখুন (যেমন <code>/start</code>, <code>/courses</code>, <code>/start?assessment=1</code>) — ইংরেজি সাইটে নিজে থেকেই <code>/en</code> যোগ হবে। বাইরের লিংক https:// দিয়ে। বাটন সরাতে লেখা ও লিংক মুছে দিন।</p>
                    @foreach ($buttons as $index => $button)
                        <div class="grid gap-3 rounded-xl bg-surface-muted p-3 md:grid-cols-[1fr_1fr_1.3fr_auto]">
                            <x-form.field :name="'buttons['.$index.'][label][bn]'" label="লেখা (বাংলা)" :value="$button['label']['bn'] ?? null" />
                            <x-form.field :name="'buttons['.$index.'][label][en]'" label="Label (English)" :value="$button['label']['en'] ?? null" />
                            <x-form.field :name="'buttons['.$index.'][url]'" label="লিংক" :value="$button['url'] ?? null" placeholder="/start" />
                            <x-form.select :name="'buttons['.$index.'][style]'" label="ধরন" :value="$button['style'] ?? 'primary'" :options="HomePageContent::ButtonStyles" />
                        </div>
                    @endforeach
                </fieldset>

                <x-form.field name="hero[video_url]" type="url" label="পরিচিতি ভিডিও (YouTube, unlisted চলবে)" :value="$hero['video_url']" help="চলমান ক্যাম্পেইন ব্যানার না থাকলে হিরোর ডান পাশে এই ভিডিও দেখাবে।" />
                <x-form.checkbox name="hero[show_trust_chips]" label="বাটনের নিচে নীতিমালার চিপ দেখান (লাইভ ক্লাস · ফ্রি অ্যাসেসমেন্ট · রিফান্ড গ্যারান্টি)" :checked="$hero['show_trust_chips']" />
            </div>
        </x-card>

        <x-card title="পরিসংখ্যান" description="যেমন: ১২০০+ শিক্ষার্থী, ৫০টি ব্যাচ, ৪.৮/৫ অভিভাবক রেটিং। শুধু সত্যিকারের, যাচাইযোগ্য সংখ্যা দিন — ভুল সংখ্যা ধরা পড়লে আস্থা পুরোপুরি নষ্ট হয়। কোনো সংখ্যা না দিলে সেকশনটি দেখাবে না।">
            <div class="grid gap-3">
                @foreach ($stats as $index => $stat)
                    <div class="grid gap-3 rounded-xl bg-surface-muted p-3 md:grid-cols-[0.6fr_1fr_1fr]">
                        <x-form.field :name="'stats['.$index.'][value]'" label="সংখ্যা" :value="$stat['value'] ?? null" placeholder="1200+" />
                        <x-form.field :name="'stats['.$index.'][label][bn]'" label="পরিচিতি (বাংলা)" :value="$stat['label']['bn'] ?? null" placeholder="শিক্ষার্থী শিখেছে" />
                        <x-form.field :name="'stats['.$index.'][label][en]'" label="Label (English)" :value="$stat['label']['en'] ?? null" placeholder="students taught" />
                    </div>
                @endforeach
                <p class="text-xs text-ink-subtle">সংখ্যা ইংরেজি অঙ্কে লিখলে বাংলা সাইটে নিজে থেকেই বাংলা অঙ্কে দেখাবে।</p>
            </div>
        </x-card>

        <x-card title="সেকশন" description="টিক তুলে দিলে সেকশনটি লুকানো থাকবে। “ক্রম” নম্বর অনুযায়ী ওপর থেকে নিচে সাজানো হয়। কোনো সেকশনে কনটেন্ট না থাকলে (যেমন রিভিউ নেই) সেটি নিজে থেকেই দেখাবে না।">
            <ul class="grid gap-3">
                @foreach ($sections as $index => $section)
                    @php($key = $section['key'])
                    <li class="rounded-2xl ring-1 ring-line ring-inset">
                        <div class="flex flex-wrap items-center gap-4 p-4">
                            <label class="flex items-center gap-2 text-sm text-ink-muted">
                                ক্রম
                                <input type="number" name="sections[{{ $key }}][order]" value="{{ old("sections.{$key}.order", $index + 1) }}" min="0" max="99" class="h-10 w-16 rounded-lg bg-surface px-2 text-center text-ink ring-1 ring-line-strong ring-inset">
                            </label>
                            <x-form.checkbox :name="'sections['.$key.'][visible]'" :label="HomePageContent::SECTIONS[$key]" :checked="$section['visible']" />
                        </div>
                        <details class="border-t border-line">
                            <summary class="cursor-pointer list-none px-4 py-2.5 text-sm font-medium text-accent-ink">শিরোনাম বদলান (ঐচ্ছিক)</summary>
                            <div class="grid gap-3 p-4 pt-1">
                                <x-form.translatable :name="'sections['.$key.'][title]'" label="শিরোনাম" :value="$section['title']" :help="'ডিফল্ট: '.__('site.home.sections.'.$key.'.title', [], 'bn')" />
                                <x-form.translatable :name="'sections['.$key.'][subtitle]'" label="সাবটাইটেল" :value="$section['subtitle']" />
                            </div>
                        </details>
                    </li>
                @endforeach
            </ul>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">সেভ করুন</x-button>
        </div>
    </form>
</x-layouts.admin>
