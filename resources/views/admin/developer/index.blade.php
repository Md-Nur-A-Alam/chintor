<x-layouts.admin title="ডেভেলপার হাব">
    <x-admin.page-header title="ডেভেলপার হাব" description="ডেটাবেস, স্কিমা, এরর ও স্টাফ সাপোর্ট টুলস। এখানের প্রতিটি কাজ লগ হয়।" />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['admin.developer.database', 'database', 'ডেটাবেস কনসোল', 'একাধিক SQL কোয়েরি চালান; write-এর জন্য পাসওয়ার্ড লাগে।'],
            ['admin.developer.schema', 'layers', 'ER ডায়াগ্রাম', 'পুরো ডেটাবেস বা নির্দিষ্ট টেবিলের সম্পর্ক।'],
            ['admin.developer.errors', 'alert', 'এরর লগ', $recentErrorCount.'টি সাম্প্রতিক error।'],
            ['admin.developer.staff', 'key', 'স্টাফ অ্যাক্সেস', 'যেকোনো প্রোফাইলে সুইচ ও পাসওয়ার্ড রিসেট।'],
            ['admin.developer.guide', 'file', 'ডেভেলপার গাইড', 'আর্কিটেকচার, কনভেনশন ও ডিপ্লয়।'],
            ['admin.guide', 'help', 'রোলভিত্তিক নির্দেশিকা', 'স্টাফরা যা দেখেন।'],
        ] as [$route, $icon, $title, $body])
            <a href="{{ route($route) }}" class="rounded-card bg-surface p-5 shadow-card ring-1 ring-line transition hover:ring-line-strong">
                <span class="grid size-10 place-items-center rounded-xl bg-brand-soft text-brand"><x-icon :name="$icon" class="size-5" /></span>
                <h2 class="mt-3 font-bold text-ink">{{ $title }}</h2>
                <p class="mt-1 text-sm text-ink-muted">{{ $body }}</p>
            </a>
        @endforeach
    </div>

    <x-card title="সিস্টেম" class="mt-6">
        <dl class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($system as $label => $value)
                <div><dt class="text-ink-subtle">{{ $label }}</dt><dd class="font-mono font-medium text-ink">{{ $value }}</dd></div>
            @endforeach
        </dl>
    </x-card>
</x-layouts.admin>
