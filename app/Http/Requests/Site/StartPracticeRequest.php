<?php

namespace App\Http\Requests\Site;

use Illuminate\Validation\Rule;

/**
 * A custom Exam Arena practice: the student picks a subject, optionally
 * some of its topics and a class, how many questions and how many minutes
 * (at most chintor.arena.max_minutes_per_question per question; checked in
 * App\Services\ExamArena so the message can show the exact limit).
 */
class StartPracticeRequest extends StartExamRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'subject_id' => ['required', 'integer', Rule::exists('learning_topics', 'id')->whereNull('parent_id')],
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['integer', Rule::exists('learning_topics', 'id')->where('parent_id', $this->integer('subject_id'))],
            'class_level' => ['nullable', 'integer', 'between:1,12'],
            'question_count' => ['required', 'integer', 'min:1', 'max:'.config('chintor.arena.max_questions')],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array{subject_id: int, topic_ids: list<int>, class_level: ?int, question_count: int, duration_minutes: int}
     */
    public function settings(): array
    {
        return [
            'subject_id' => (int) $this->validated('subject_id'),
            'topic_ids' => array_map('intval', $this->validated('topic_ids') ?? []),
            'class_level' => $this->validated('class_level') !== null ? (int) $this->validated('class_level') : null,
            'question_count' => (int) $this->validated('question_count'),
            'duration_minutes' => (int) $this->validated('duration_minutes'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            ...parent::attributes(),
            'subject_id' => __('site.arena.subject'),
            'question_count' => __('site.arena.question_count'),
            'duration_minutes' => __('site.arena.minutes'),
        ];
    }
}
