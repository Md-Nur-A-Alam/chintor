@php
    $isEditing = $user->exists;
    $assignedRoleIds = collect(old('roles', $isEditing ? $user->roles->pluck('id')->all() : []))->map(fn ($id) => (int) $id);
@endphp

<x-layouts.admin :title="$isEditing ? 'স্টাফ এডিট' : 'নতুন স্টাফ'">
    <x-admin.page-header
        :title="$isEditing ? $user->name : 'নতুন স্টাফ অ্যাকাউন্ট'"
        description="লগইন হবে ফোন নম্বর ও পাসওয়ার্ড দিয়ে।"
    />

    <form method="POST" action="{{ $isEditing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing)
            @method('PUT')
        @endif

        <x-card title="প্রোফাইল">
            <div class="grid gap-4 md:grid-cols-2">
                <x-form.field name="name" label="পুরো নাম" :value="$user->name" required />
                <x-form.field name="phone" label="মোবাইল নম্বর (লগইন আইডি)" type="tel" :value="$user->phone" placeholder="01XXXXXXXXX" required />
                <x-form.field name="email" label="ইমেইল" type="email" :value="$user->email" />
                <x-form.field name="office_id" label="অফিস আইডি" :value="$user->office_id" placeholder="CNT-02" />
                <x-form.field name="designation" label="পদবি" :value="$user->designation" />
                <x-form.field name="joined_on" label="যোগদানের তারিখ" type="date" :value="$user->joined_on?->toDateString()" />
                <x-form.field name="date_of_birth" label="জন্মতারিখ" type="date" :value="$user->date_of_birth?->toDateString()" />
            </div>
            <div class="mt-4">
                <x-form.checkbox name="is_active" label="সক্রিয় (লগইন করতে পারবেন)" :checked="$user->is_active" />
            </div>
        </x-card>

        <x-card title="রোল" description="একাধিক রোল দেওয়া যায়। অ্যাডমিন রোল শুধু অন্য অ্যাডমিন দিতে পারেন।">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($roles as $role)
                    <label class="flex cursor-pointer gap-3 rounded-2xl p-3 ring-1 ring-line ring-inset has-[:checked]:bg-accent-soft has-[:checked]:ring-accent-ink/30">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked($assignedRoleIds->contains($role->id)) class="mt-1 size-4 accent-[var(--accent)]">
                        <span>
                            <span class="block text-sm font-semibold text-ink">{{ $role->label }}</span>
                            <span class="block text-xs text-ink-subtle">{{ $role->description }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('roles')
                <p class="mt-3 text-sm text-accent-ink">{{ $message }}</p>
            @enderror
        </x-card>

        <x-card :title="$isEditing ? 'পাসওয়ার্ড বদলান' : 'পাসওয়ার্ড'" :description="$isEditing ? 'খালি রাখলে আগের পাসওয়ার্ডই থাকবে।' : 'অন্তত ৮ অক্ষর।'">
            <div class="grid gap-4 md:grid-cols-2">
                <x-form.field name="password" label="পাসওয়ার্ড" type="password" autocomplete="new-password" :required="! $isEditing" />
                <x-form.field name="password_confirmation" label="আবার লিখুন" type="password" autocomplete="new-password" :required="! $isEditing" />
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">{{ $isEditing ? 'আপডেট করুন' : 'অ্যাকাউন্ট তৈরি করুন' }}</x-button>
            <x-button :href="route('admin.users.index')" variant="secondary">বাতিল</x-button>
        </div>
    </form>
</x-layouts.admin>
