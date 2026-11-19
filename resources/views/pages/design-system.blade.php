{{--
    Internal design-system reference (not available in production).
    Use the theme switcher in the header to review every token in both themes.
--}}
@php
    $brandScales = [
        'teal' => [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950],
        'coral' => [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950],
        'sun' => [300, 400, 500],
    ];

    $semanticTokens = [
        'canvas' => 'bg-canvas',
        'surface' => 'bg-surface',
        'surface-muted' => 'bg-surface-muted',
        'line' => 'bg-line',
        'ink' => 'bg-ink',
        'ink-muted' => 'bg-ink-muted',
        'ink-subtle' => 'bg-ink-subtle',
        'brand' => 'bg-brand',
        'brand-soft' => 'bg-brand-soft',
        'accent' => 'bg-accent',
        'accent-soft' => 'bg-accent-soft',
        'accent-ink' => 'bg-accent-ink',
    ];

    $priceExamples = [
        ['label' => 'শুধু মেইন প্রাইস', 'regular' => 2500, 'discounted' => null],
        ['label' => 'মেইন + ডিসকাউন্টেড', 'regular' => 3000, 'discounted' => 2400],
        ['label' => 'ভুল ডেটা: ডিসকাউন্ট ≥ মেইন (শুধু মেইন দেখাবে)', 'regular' => 1500, 'discounted' => 1800],
        ['label' => 'লাখ গ্রুপিং', 'regular' => 125000, 'discounted' => 99000],
    ];
@endphp

<x-layouts.app title="Design system" :noindex="true">
    <div class="container-page grid gap-16 py-12">
        <header>
            <p class="text-sm font-semibold text-accent-ink">ফেজ ০ · ডিজাইন সিস্টেম</p>
            <h1 class="mt-2 text-4xl font-bold">চিন্তর ডিজাইন সিস্টেম</h1>
            <p class="mt-3 max-w-2xl text-ink-muted">রং, টাইপোগ্রাফি, বাটন ও মূল কম্পোনেন্ট। হেডারের থিম সুইচ দিয়ে লাইট/ডার্ক দুটোই দেখে নিন।</p>
        </header>

        <section>
            <h2 class="text-2xl font-bold">লোগো</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="grid place-items-center rounded-card bg-surface p-8 ring-1 ring-line"><x-logo class="h-14 w-auto" /></div>
                <div class="grid place-items-center rounded-card bg-surface p-8 ring-1 ring-line"><x-logo variant="stacked" class="h-40 w-auto" /></div>
                <div class="grid place-items-center rounded-card bg-surface p-8 ring-1 ring-line"><x-logo variant="icon" class="h-24 w-auto" /></div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold">ব্র্যান্ড প্যালেট</h2>
            <div class="mt-6 grid gap-6">
                @foreach ($brandScales as $scale => $steps)
                    <div>
                        <h3 class="text-sm font-semibold text-ink-muted">{{ $scale }}</h3>
                        <div class="mt-2 grid grid-cols-4 gap-2 sm:grid-cols-6 lg:grid-cols-11">
                            @foreach ($steps as $step)
                                <div>
                                    <div class="h-14 rounded-xl ring-1 ring-black/5" style="background: var(--color-{{ $scale }}-{{ $step }})"></div>
                                    <p class="mt-1 text-xs text-ink-subtle">{{ $step }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold">সেমান্টিক টোকেন (থিম অনুযায়ী বদলায়)</h2>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                @foreach ($semanticTokens as $token => $class)
                    <div class="rounded-xl bg-surface p-2 ring-1 ring-line">
                        <div class="{{ $class }} h-12 rounded-lg ring-1 ring-line"></div>
                        <p class="mt-2 text-xs font-medium">{{ $token }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold">টাইপোগ্রাফি — Anek Bangla</h2>
            <div class="mt-6 grid gap-4 rounded-card bg-surface p-6 ring-1 ring-line">
                <p class="text-5xl font-bold">ভাবতে শেখায়, এগিয়ে রাখে</p>
                <p class="text-3xl font-semibold">ম্যাথ অলিম্পিয়াড প্রস্তুতি — ৩য় থেকে ৮ম শ্রেণি</p>
                <p class="text-xl font-medium">Learn to think. Stay ahead. — ইংরেজি ও বাংলা একই ফন্টে।</p>
                <p class="max-w-prose text-ink-muted">বডি টেক্সট: অভিজ্ঞ শিক্ষকের লাইভ ক্লাসে ধাপে ধাপে শেখা। প্রতিটি ব্যাচে সীমিত আসন, তাই প্রত্যেক শিক্ষার্থীর দিকে শিক্ষকের নজর থাকে। সংখ্যা: ০১২৩৪৫৬৭৮৯ / 0123456789।</p>
                <p class="text-sm text-ink-subtle">ছোট টেক্সট / ক্যাপশন — ink-subtle</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold">বাটন</h2>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <x-button>চলো একসাথে শিখি <x-icon name="arrow-right" class="size-4" /></x-button>
                <x-button variant="secondary">ফ্রি অ্যাসেসমেন্ট</x-button>
                <x-button variant="brand">কোর্স দেখুন</x-button>
                <x-button variant="ghost">আরও জানুন</x-button>
                <x-button size="sm">ছোট</x-button>
                <x-button size="lg">বড় বাটন</x-button>
                <x-button disabled>নিষ্ক্রিয়</x-button>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold">প্রাইস কম্পোনেন্ট (§৩.৩ নিয়ম)</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ($priceExamples as $example)
                    <div class="rounded-card bg-surface p-6 ring-1 ring-line">
                        <p class="text-sm text-ink-subtle">{{ $example['label'] }}</p>
                        <x-price class="mt-3" size="lg" :regular="$example['regular']" :discounted="$example['discounted']" />
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold">কোর্স কার্ড (খসড়া)</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([['ম্যাথ অলিম্পিয়াড — প্রাইমারি', '৩য়–৫ম শ্রেণি', 3000, 2400], ['ম্যাথ অলিম্পিয়াড — জুনিয়র', '৬ষ্ঠ–৮ম শ্রেণি', 3500, null], ['৯ম–১০ম বিজ্ঞান', 'সাধারণ গণিতসহ', 4000, 3200]] as [$title, $audience, $regular, $discounted])
                    <article class="flex flex-col overflow-hidden rounded-card bg-surface shadow-card ring-1 ring-line">
                        <div class="relative aspect-video bg-brand-soft">
                            <div class="absolute inset-0 grid place-items-center">
                                <span class="grid size-14 place-items-center rounded-full bg-surface/90 text-accent shadow-card">
                                    <x-icon name="play" class="size-6" />
                                </span>
                            </div>
                            <span class="absolute top-3 left-3 rounded-full bg-surface px-2.5 py-1 text-xs font-semibold text-brand">লাইভ ব্যাচ</span>
                        </div>
                        <div class="flex flex-1 flex-col gap-3 p-5">
                            <p class="text-sm text-ink-subtle">{{ $audience }}</p>
                            <h3 class="text-xl font-semibold">{{ $title }}</h3>
                            <ul class="grid gap-1.5 text-sm text-ink-muted">
                                <li class="flex gap-2"><x-icon name="check" class="size-4 text-brand" /> সপ্তাহে ৩টি লাইভ ক্লাস</li>
                                <li class="flex gap-2"><x-icon name="check" class="size-4 text-brand" /> ফ্রি অ্যাসেসমেন্ট</li>
                            </ul>
                            <div class="mt-auto flex items-end justify-between gap-3 pt-3">
                                <x-price :regular="$regular" :discounted="$discounted" />
                                <x-button size="sm" variant="brand">বিস্তারিত</x-button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold">ফর্ম ইনপুট (খসড়া)</h2>
            <form class="mt-6 grid max-w-md gap-4" onsubmit="return false">
                <label class="grid gap-1.5">
                    <span class="text-sm font-medium">শিক্ষার্থীর নাম</span>
                    <input type="text" class="h-12 rounded-xl bg-surface px-4 ring-1 ring-line outline-none ring-inset placeholder:text-ink-subtle focus:ring-2 focus:ring-focus" placeholder="যেমন: আয়ান রহমান">
                </label>
                <label class="grid gap-1.5">
                    <span class="text-sm font-medium">অভিভাবকের ফোন / WhatsApp</span>
                    <input type="tel" inputmode="tel" autocomplete="tel" class="h-12 rounded-xl bg-surface px-4 ring-1 ring-line outline-none ring-inset placeholder:text-ink-subtle focus:ring-2 focus:ring-focus" placeholder="01XXXXXXXXX">
                </label>
                <x-button type="submit" size="lg">সাবমিট করুন</x-button>
            </form>
        </section>
    </div>
</x-layouts.app>
