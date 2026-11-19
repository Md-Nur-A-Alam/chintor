<x-layouts.admin title="কোর্স">
    <x-admin.page-header title="কোর্স" description="প্রতিটি কোর্সের পেমেন্ট প্ল্যান ও ব্যাচ কোর্সের এডিট পেজে যোগ করুন।">
        <x-slot:actions>
            <x-button :href="route('admin.courses.create')" size="sm"><x-icon name="plus" class="size-4" /> নতুন কোর্স</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="GET" class="mb-4 flex max-w-sm gap-2">
        <x-form.select name="program" label="প্রোগ্রাম" :value="$programId" placeholder="সব প্রোগ্রাম" :options="$programs->mapWithKeys(fn ($program) => [$program->id => $program->translate('name', 'bn')])->all()" onchange="this.form.submit()" />
    </form>

    <x-admin.table :paginator="$courses">
        <x-slot:head>
            <tr>
                <th class="px-4 py-3">কোর্স</th>
                <th class="px-4 py-3">প্রোগ্রাম</th>
                <th class="px-4 py-3">প্ল্যান / ব্যাচ</th>
                <th class="px-4 py-3">এনরোলমেন্ট</th>
                <th class="px-4 py-3">অবস্থা</th>
                <th class="px-4 py-3 text-right">অ্যাকশন</th>
            </tr>
        </x-slot:head>

        @forelse ($courses as $course)
            <tr>
                <td class="px-4 py-3">
                    <p class="font-semibold text-ink">{{ $course->translate('title', 'bn') }}</p>
                    <p class="text-xs text-ink-subtle">{{ $course->classRangeLabel() }}@if ($course->is_featured) · ★ ফিচার্ড @endif</p>
                </td>
                <td class="px-4 py-3 text-ink-muted">{{ $course->program->translate('name', 'bn') }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ $course->variations_count }} / {{ $course->batches_count }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ $course->enrollments_count }}</td>
                <td class="px-4 py-3"><x-badge :tone="$course->status->tone()">{{ $course->status->label() }}</x-badge></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        @if ($course->isPublished())
                            <x-button :href="App\Support\Localization::route('courses.show', ['course' => $course])" target="_blank" variant="ghost" size="sm"><x-icon name="external" class="size-4" /></x-button>
                        @endif
                        <x-button :href="route('admin.courses.edit', $course)" variant="ghost" size="sm"><x-icon name="pencil" class="size-4" /> এডিট</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-ink-subtle">কোনো কোর্স নেই। আগে একটি প্রোগ্রাম তৈরি করুন, তারপর কোর্স।</td></tr>
        @endforelse
    </x-admin.table>
</x-layouts.admin>
