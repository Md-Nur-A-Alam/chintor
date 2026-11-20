<x-layouts.admin title="স্টাফ অ্যাক্সেস">
    <x-admin.page-header title="স্টাফ অ্যাক্সেস" description="কারও সমস্যা দেখতে তাঁর প্রোফাইলে সুইচ করুন, অথবা পাসওয়ার্ড ভুলে গেলে নতুন পাসওয়ার্ড দিন। দুটো কাজই লগ হয়।" />

    @unless ($canSwitchProfiles)
        <p class="mb-6 rounded-2xl bg-surface-muted p-4 text-sm text-ink-muted ring-1 ring-line ring-inset">
            প্রোফাইল সুইচ শুধু <strong class="text-ink">ডেভেলপার</strong> রোলের অ্যাকাউন্ট থেকে করা যায়। এখান থেকে আপনি পাসওয়ার্ড রিসেট করতে পারবেন।
        </p>
    @endunless

    @if (session('generated_password'))
        @php($generated = session('generated_password'))
        <div x-data="{ copied: false }" class="mb-6 rounded-card bg-sun-300/25 p-5 ring-1 ring-sun-500/40 ring-inset">
            <p class="font-semibold text-ink">{{ $generated['user'] }} ({{ $generated['phone'] }}) — নতুন পাসওয়ার্ড:</p>
            <div class="mt-2 flex items-center gap-3">
                <code class="rounded-lg bg-surface px-3 py-2 font-mono text-lg font-bold tracking-wider text-ink">{{ $generated['password'] }}</code>
                <x-button type="button" variant="secondary" size="sm" x-on:click="navigator.clipboard.writeText(@js($generated['password'])); copied = true">
                    <x-icon name="copy" class="size-4" /> <span x-text="copied ? 'কপি হয়েছে' : 'কপি'">কপি</span>
                </x-button>
            </div>
            <p class="mt-2 text-xs text-ink-muted">পেজ ছাড়লে এটি আর দেখা যাবে না। তাঁকে লগইনের পর নিজের পাসওয়ার্ড বদলাতে বলুন।</p>
        </div>
    @endif

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">স্টাফ</th><th class="px-4 py-3">রোল</th><th class="px-4 py-3">শেষ লগইন</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @foreach ($users as $user)
            <tr>
                <td class="px-4 py-3"><p class="font-semibold text-ink">{{ $user->name }}</p><p class="text-xs text-ink-subtle">{{ $user->phone }} · {{ $user->office_id }}</p></td>
                <td class="px-4 py-3"><div class="flex flex-wrap gap-1">@foreach ($user->roles as $role)<x-badge>{{ $role->label }}</x-badge>@endforeach @unless ($user->is_active)<x-badge tone="danger">নিষ্ক্রিয়</x-badge>@endunless</div></td>
                <td class="px-4 py-3 text-ink-muted">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        @if ($canSwitchProfiles && ! $user->is(auth()->user()) && $user->is_active)
                            <form method="POST" action="{{ route('admin.developer.staff.impersonate', $user) }}">
                                @csrf
                                <x-button type="submit" variant="secondary" size="sm"><x-icon name="switch" class="size-4" /> সুইচ</x-button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.developer.staff.reset-password', $user) }}" onsubmit="return confirm('{{ $user->name }}-এর পাসওয়ার্ড রিসেট করবেন? পুরোনো পাসওয়ার্ড আর কাজ করবে না।')">
                            @csrf
                            <x-button type="submit" variant="ghost" size="sm"><x-icon name="key" class="size-4" /> পাসওয়ার্ড রিসেট</x-button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>
</x-layouts.admin>
