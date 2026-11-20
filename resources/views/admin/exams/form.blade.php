@php
    $isEditing = $exam->exists;
    $checkedIds = array_map('intval', old('question_ids', $selectedIds));
@endphp

<x-layouts.admin :title="$isEditing ? 'পরীক্ষা এডিট' : 'নতুন পরীক্ষা'">
    <x-admin.page-header :title="$isEditing ? $exam->translate('title', 'bn') : 'নতুন পরীক্ষা'" />

    <form method="POST" action="{{ $isEditing ? route('admin.exams.update', $exam) : route('admin.exams.store') }}" class="grid gap-6" x-data="{
            picked: {{ count($checkedIds) }},
            filter: { subject: '', topic: '', level: '', search: '', onlyPicked: false },
            shows(row) {
                const f = this.filter;
                return (! f.subject || f.subject == row.subject)
                    && (! f.topic || f.topic == row.topic)
                    && (! f.level || f.level == row.level)
                    && (! f.search || row.text.includes(f.search.toLowerCase()))
                    && (! f.onlyPicked || row.checked);
            },
        }">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card>
            <div class="grid gap-4">
                <x-form.translatable name="title" label="শিরোনাম" :value="$exam->title" required />
                <x-form.translatable name="description" label="বিবরণ (ঐচ্ছিক)" :value="$exam->description" :multiline="true" :rows="2" />
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.field name="slug" label="লিংক-নাম" :value="$exam->slug" placeholder="weekly-math-1" required help="ইংরেজিতে; ওয়েবসাইটের লিংকে বসবে।" />
                    <x-form.select name="subject_id" label="বিষয় (ঐচ্ছিক)" :value="$exam->subject_id" placeholder="—" :options="$subjects->mapWithKeys(fn ($subject) => [$subject->id => $subject->translate('name', 'bn')])->all()" />
                    <x-form.select name="class_level" label="শ্রেণি (ঐচ্ছিক)" :value="$exam->class_level" placeholder="—" :options="collect(range(1, 12))->mapWithKeys(fn ($class) => [$class => $class.' শ্রেণি'])->all()" />
                </div>
                <div class="grid gap-4 md:grid-cols-4">
                    <x-form.field name="duration_minutes" type="number" label="সময় (মিনিট)" :value="$exam->duration_minutes" required />
                    <x-form.field name="question_count" type="number" label="প্রতিবার প্রশ্ন" :value="$exam->question_count" required />
                    <x-form.field name="results_at" type="datetime-local" label="ফলাফল প্রকাশ (ঐচ্ছিক)" :value="$exam->results_at?->format('Y-m-d\TH:i')" help="ফাঁকা = জমা দিলেই ফলাফল।" />
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$exam->sort_order ?? 0" />
                </div>
                <x-form.checkbox name="is_published" label="ওয়েবসাইটে প্রকাশ করুন" :checked="$exam->is_published" />
            </div>
        </x-card>

        <x-card title="প্রশ্ন বাছাই" description="টিক দেওয়া প্রশ্নগুলো এই পরীক্ষার। প্রশ্নসংখ্যার চেয়ে বেশি বাছলে প্রতিবার তার থেকে এলোমেলোভাবে বাছাই হবে।">
            {{-- In-page filter: hides non-matching rows and keeps every tick. --}}
            <div class="mb-4 grid gap-3 rounded-2xl bg-surface-muted p-3 md:grid-cols-4">
                <label class="grid gap-1 text-sm font-medium text-ink">বিষয়
                    <select x-model="filter.subject" class="h-10 rounded-lg bg-surface px-2 ring-1 ring-line-strong ring-inset">
                        <option value="">সব</option>
                        @foreach ($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->translate('name', 'bn') }}</option>@endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-medium text-ink">টপিক
                    <select x-model="filter.topic" class="h-10 rounded-lg bg-surface px-2 ring-1 ring-line-strong ring-inset">
                        <option value="">সব</option>
                        @foreach ($subjects as $subject)
                            @foreach ($subject->children as $topic)<option value="{{ $topic->id }}">{{ $subject->translate('name', 'bn') }} › {{ $topic->translate('name', 'bn') }}</option>@endforeach
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-medium text-ink">শ্রেণি
                    <select x-model="filter.level" class="h-10 rounded-lg bg-surface px-2 ring-1 ring-line-strong ring-inset">
                        <option value="">সব</option>
                        @foreach (range(1, 12) as $class)<option value="{{ $class }}">{{ $class }}</option>@endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-medium text-ink">খুঁজুন
                    <input type="search" x-model="filter.search" class="h-10 rounded-lg bg-surface px-3 ring-1 ring-line-strong ring-inset">
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-ink md:col-span-4">
                    <input type="checkbox" x-model="filter.onlyPicked" class="size-4 accent-[var(--brand)]"> শুধু বাছাই করাগুলো দেখান
                </label>
            </div>
            <p class="mb-3 text-sm font-semibold text-ink">বাছাই করা: <span x-text="picked"></span>টি</p>
            @error('question_ids') <p class="mb-3 text-sm text-accent-ink">{{ $message }}</p> @enderror

            <div class="grid max-h-[32rem] gap-2 overflow-y-auto pr-1">
                @forelse ($questions as $question)
                    <label
                        x-data="{ row: { subject: {{ $question->subject_id }}, topic: {{ (int) $question->topic_id }}, level: {{ (int) $question->class_level }}, text: @js(mb_strtolower($question->body)), checked: @js(in_array($question->id, $checkedIds, true)) } }"
                        x-show="shows(row)"
                        class="flex cursor-pointer gap-3 rounded-xl bg-surface-muted p-3 text-sm ring-1 ring-line has-[:checked]:bg-brand-soft has-[:checked]:ring-brand"
                    >
                        <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" @checked(in_array($question->id, $checkedIds, true)) x-on:change="row.checked = $event.target.checked; picked += $event.target.checked ? 1 : -1" class="mt-0.5 size-4 shrink-0 accent-[var(--brand)]">
                        <span>
                            <span class="block font-medium text-ink">{{ $question->body }}</span>
                            <span class="text-xs text-ink-subtle">{{ $question->subject->translate('name', 'bn') }}@if ($question->topic) › {{ $question->topic->translate('name', 'bn') }}@endif @if ($question->class_level) · {{ $question->class_level }} শ্রেণি @endif</span>
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-ink-subtle">এই ফিল্টারে কোনো চালু প্রশ্ন নেই।</p>
                @endforelse
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">সেভ করুন</x-button>
            <x-button :href="route('admin.exams.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>
</x-layouts.admin>
