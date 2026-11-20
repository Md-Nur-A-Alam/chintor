<x-layouts.admin title="নোটিফিকেশন">
    <x-admin.page-header title="নোটিফিকেশন" description="আপনার কাজের সাথে সম্পর্কিত সব ঘটনা।">
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                <x-button type="submit" variant="secondary" size="sm"><x-icon name="check" class="size-4" /> সব পড়া হয়েছে</x-button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <x-card class="p-0 sm:p-0">
        <ul class="divide-y divide-line">
            @forelse ($notifications as $notification)
                <li>
                    <a href="{{ route('admin.notifications.open', $notification->id) }}" @class(['flex gap-3 px-5 py-4 hover:bg-surface-muted', 'bg-accent-soft/40' => ! $notification->read_at])>
                        <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-brand-soft text-brand"><x-icon :name="$notification->data['icon'] ?? 'star'" class="size-4" /></span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-semibold text-ink">{{ $notification->data['title'] ?? '' }}</span>
                            @if (! empty($notification->data['body']))
                                <span class="block text-sm text-ink-muted">{{ $notification->data['body'] }}</span>
                            @endif
                            <span class="block text-xs text-ink-subtle">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                        @unless ($notification->read_at)
                            <span class="mt-2 size-2.5 shrink-0 rounded-full bg-accent" aria-label="নতুন"></span>
                        @endunless
                    </a>
                </li>
            @empty
                <li class="px-5 py-12 text-center text-ink-subtle">কোনো নোটিফিকেশন নেই।</li>
            @endforelse
        </ul>
    </x-card>
    <div class="mt-6">{{ $notifications->links() }}</div>
</x-layouts.admin>
