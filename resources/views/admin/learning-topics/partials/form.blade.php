{{-- Add or edit one subject/topic. Delete is offered on existing ones. --}}
<form method="POST" action="{{ $action }}" class="grid gap-3 md:grid-cols-[1fr_1fr_0.8fr_0.4fr_auto] md:items-end">
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    @if ($parentId) <input type="hidden" name="parent_id" value="{{ $parentId }}"> @endif

    <x-form.field name="name[bn]" label="নাম (বাংলা)" :value="$topic->name['bn'] ?? null" required />
    <x-form.field name="name[en]" label="Name (English)" :value="$topic->name['en'] ?? null" />
    <x-form.field name="slug" label="লিংক-নাম" :value="$topic->slug" placeholder="geometry" required />
    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$topic->sort_order ?? 0" />
    <div class="flex gap-2">
        <x-button type="submit" size="sm">{{ $topic->exists ? 'সেভ' : 'যোগ করুন' }}</x-button>
        @if ($topic->exists)
            <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink" form="delete-topic-{{ $topic->id }}" aria-label="মুছুন"><x-icon name="trash" class="size-4" /></x-button>
        @endif
    </div>
</form>

@if ($topic->exists)
    <form id="delete-topic-{{ $topic->id }}" method="POST" action="{{ route('admin.learning-topics.destroy', $topic) }}" onsubmit="return confirm('“{{ $topic->translate('name', 'bn') }}” মুছে ফেলবেন?')" class="hidden">
        @csrf @method('DELETE')
    </form>
@endif
