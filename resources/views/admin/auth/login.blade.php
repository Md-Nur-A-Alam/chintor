<x-layouts.admin title="লগইন">
    <div class="grid min-h-dvh place-items-center px-4 py-12">
        <div class="w-full max-w-sm">
            <div class="flex flex-col items-center text-center">
                <x-logo variant="stacked" class="h-24 w-auto" />
                <h1 class="mt-6 text-2xl font-bold text-ink">স্টাফ লগইন</h1>
                <p class="mt-1 text-sm text-ink-muted">ফোন নম্বর ও পাসওয়ার্ড দিয়ে প্রবেশ করুন</p>
            </div>

            <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 grid gap-4 rounded-card bg-surface p-6 shadow-card ring-1 ring-line">
                @csrf

                @error('phone')
                    <p role="alert" class="rounded-xl bg-accent-soft px-4 py-3 text-sm font-medium text-accent-ink">{{ $message }}</p>
                @enderror

                <label class="grid gap-1.5">
                    <span class="text-sm font-medium text-ink">ফোন নম্বর</span>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        inputmode="tel"
                        autocomplete="username"
                        placeholder="01XXXXXXXXX"
                        required
                        autofocus
                        class="h-12 rounded-xl bg-surface px-4 text-ink ring-1 ring-line outline-none ring-inset placeholder:text-ink-subtle focus:ring-2 focus:ring-focus"
                    >
                </label>

                <label class="grid gap-1.5">
                    <span class="text-sm font-medium text-ink">পাসওয়ার্ড</span>
                    <input
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                        class="h-12 rounded-xl bg-surface px-4 text-ink ring-1 ring-line outline-none ring-inset focus:ring-2 focus:ring-focus"
                    >
                    @error('password')
                        <span class="text-sm text-accent-ink">{{ $message }}</span>
                    @enderror
                </label>

                <label class="flex items-center gap-2 text-sm text-ink-muted">
                    <input type="checkbox" name="remember" value="1" class="size-4 accent-[var(--accent)]">
                    এই ডিভাইসে লগইন রাখুন
                </label>

                <x-button type="submit" size="lg" class="w-full">লগইন</x-button>
            </form>
        </div>
    </div>
</x-layouts.admin>
