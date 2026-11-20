<?php

namespace App\Services;

use App\Enums\LeadType;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Starts and grades Exam Arena sittings.
 *
 * A ready-made exam deals question_count of its questions (a random subset
 * when more are attached). A custom practice deals random active questions
 * from the chosen subject, topics and class, with at most
 * chintor.arena.max_minutes_per_question minutes per question.
 *
 * When the student leaves a phone number (with consent) the sitting also
 * becomes a lead for the sales team.
 */
class ExamArena
{
    public function __construct(private readonly LeadCapture $leads) {}

    /**
     * @param  array{student_name: string, phone?: ?string}  $student
     */
    public function startExam(Exam $exam, array $student, Request $request): ExamAttempt
    {
        $questionIds = $exam->questions()->active()->inRandomOrder()->limit($exam->question_count)->pluck('questions.id')->all();

        if ($questionIds === []) {
            throw ValidationException::withMessages(['exam' => __('site.arena.no_questions')]);
        }

        return $this->begin($questionIds, $exam->duration_minutes, $student, $request, $exam, null);
    }

    /**
     * @param  array{subject_id: int, topic_ids?: list<int>, class_level?: ?int, question_count: int, duration_minutes: int}  $settings
     * @param  array{student_name: string, phone?: ?string}  $student
     */
    public function startPractice(array $settings, array $student, Request $request): ExamAttempt
    {
        $maxMinutes = $settings['question_count'] * config('chintor.arena.max_minutes_per_question');

        if ($settings['duration_minutes'] > $maxMinutes) {
            throw ValidationException::withMessages(['duration_minutes' => __('site.arena.too_many_minutes', ['max' => $maxMinutes])]);
        }

        $questionIds = Question::query()
            ->active()
            ->where('subject_id', $settings['subject_id'])
            ->when($settings['topic_ids'] ?? [], fn ($query, array $topicIds) => $query->whereIn('topic_id', $topicIds))
            ->when($settings['class_level'] ?? null, fn ($query, int $classLevel) => $query->where('class_level', $classLevel))
            ->inRandomOrder()
            ->limit($settings['question_count'])
            ->pluck('id')
            ->all();

        if ($questionIds === []) {
            throw ValidationException::withMessages(['subject_id' => __('site.arena.no_questions')]);
        }

        return $this->begin($questionIds, $settings['duration_minutes'], $student, $request, null, $settings);
    }

    /**
     * Grade the submitted answers. Answers arriving later than the grace
     * period after the deadline are not counted.
     *
     * @param  array<int|string, mixed>  $answers  question id => chosen option index
     */
    public function submit(ExamAttempt $attempt, array $answers): ExamAttempt
    {
        if ($attempt->isSubmitted()) {
            return $attempt;
        }

        $isLate = now()->greaterThan($attempt->deadline_at->copy()->addSeconds(config('chintor.arena.late_grace_seconds')));
        $questions = Question::query()->findMany($attempt->question_ids)->keyBy('id');

        $kept = [];
        $correct = 0;
        $wrong = 0;

        foreach ($attempt->question_ids as $questionId) {
            $question = $questions->get($questionId);
            $choice = $answers[$questionId] ?? null;

            if ($isLate || $question === null || ! is_numeric($choice) || ! array_key_exists((int) $choice, $question->options)) {
                continue;
            }

            $kept[$questionId] = (int) $choice;
            $kept[$questionId] === $question->correct_option ? $correct++ : $wrong++;
        }

        $attempt->forceFill([
            'answers' => $kept,
            'submitted_at' => now(),
            'correct_count' => $correct,
            'wrong_count' => $wrong,
            'skipped_count' => $attempt->totalQuestions() - $correct - $wrong,
        ])->save();

        return $attempt;
    }

    /**
     * @param  list<int>  $questionIds
     * @param  array{student_name: string, phone?: ?string}  $student
     * @param  array<string, mixed>|null  $settings
     */
    private function begin(array $questionIds, int $minutes, array $student, Request $request, ?Exam $exam, ?array $settings): ExamAttempt
    {
        shuffle($questionIds);

        $lead = filled($student['phone'] ?? null)
            ? $this->leads->capture(LeadType::ExamArena, [
                'student_name' => $student['student_name'],
                'phone' => $student['phone'],
                'message' => $exam ? 'এক্সাম এরিনা: '.$exam->translate('title', 'bn') : 'এক্সাম এরিনা: নিজের মতো প্র্যাকটিস',
            ], $request)
            : null;

        return ExamAttempt::create([
            'exam_id' => $exam?->id,
            'lead_id' => $lead?->id,
            'student_name' => $student['student_name'],
            'phone' => $student['phone'] ?? null,
            'settings' => $settings,
            'question_ids' => $questionIds,
            'duration_minutes' => $minutes,
            'started_at' => now(),
            'deadline_at' => now()->addMinutes($minutes),
            'ip_address' => $request->ip(),
        ]);
    }
}
