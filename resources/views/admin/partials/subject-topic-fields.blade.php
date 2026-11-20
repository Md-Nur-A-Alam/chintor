{{--
    Subject + topic selects where the topic list follows the subject.
    Expects $subjects (with children), $subjectId, $topicId.
    Without JavaScript all topics show; the server checks they match.
--}}
@php
    $topicsBySubject = $subjects->mapWithKeys(fn ($subject) => [
        $subject->id => $subject->children->map(fn ($topic) => ['id' => $topic->id, 'name' => $topic->translate('name', 'bn')])->values(),
    ]);
@endphp

<div x-data="{ subject: @js((string) old('subject_id', $subjectId)), topic: @js((string) old('topic_id', $topicId)), topics: @js($topicsBySubject) }" class="grid gap-4 md:grid-cols-2">
    <div class="grid gap-1.5">
        <label for="field-subject_id" class="text-sm font-medium text-ink">বিষয় <span class="text-accent-ink" aria-hidden="true">*</span></label>
        <select id="field-subject_id" name="subject_id" x-model="subject" x-on:change="topic = ''" required class="h-11 rounded-xl bg-surface px-3 text-ink ring-1 ring-line-strong ring-inset">
            <option value="">— বিষয় বাছুন —</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected((string) old('subject_id', $subjectId) === (string) $subject->id)>{{ $subject->translate('name', 'bn') }}</option>
            @endforeach
        </select>
        @error('subject_id') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
    </div>
    <div class="grid gap-1.5">
        <label for="field-topic_id" class="text-sm font-medium text-ink">টপিক</label>
        <select id="field-topic_id" name="topic_id" x-model="topic" class="h-11 rounded-xl bg-surface px-3 text-ink ring-1 ring-line-strong ring-inset">
            <option value="">— টপিক ছাড়া —</option>
            <template x-for="item in (topics[subject] || [])" :key="item.id">
                <option x-bind:value="String(item.id)" x-text="item.name" x-bind:selected="String(item.id) === topic"></option>
            </template>
        </select>
        @error('topic_id') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
    </div>
</div>
