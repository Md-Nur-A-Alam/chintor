<?php

namespace App\Models;

use Database\Factories\ExamAttemptFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * One sitting in the Exam Arena: a ready-made exam (exam_id set) or a
 * custom practice (settings holds the chosen subject/topics/class). The
 * ULID in the URL is the student's private link to the attempt and result.
 */
class ExamAttempt extends Model
{
    /** @use HasFactory<ExamAttemptFactory> */
    use HasFactory, HasUlids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'exam_id',
        'lead_id',
        'student_name',
        'phone',
        'settings',
        'question_ids',
        'answers',
        'duration_minutes',
        'started_at',
        'deadline_at',
        'submitted_at',
        'correct_count',
        'wrong_count',
        'skipped_count',
        'ip_address',
    ];

    /**
     * The ULID lives in its own column; the numeric id stays the key.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    /**
     * @return BelongsTo<Exam, $this>
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * The attempt's questions in the order they were dealt.
     *
     * @return Collection<int, Question>
     */
    public function orderedQuestions(): Collection
    {
        $questions = Question::query()->with(['subject', 'topic'])->findMany($this->question_ids)->keyBy('id');

        return collect($this->question_ids)->map(fn (int $id) => $questions->get($id))->filter()->values();
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function totalQuestions(): int
    {
        return count($this->question_ids);
    }

    public function scorePercent(): int
    {
        return $this->totalQuestions() === 0 ? 0 : (int) round($this->correct_count / $this->totalQuestions() * 100);
    }

    public function resultsArePublished(): bool
    {
        return $this->exam === null || $this->exam->resultsArePublished();
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'question_ids' => 'array',
            'answers' => 'array',
            'started_at' => 'datetime',
            'deadline_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }
}
