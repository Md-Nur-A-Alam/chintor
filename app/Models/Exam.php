<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ExamFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A ready-made exam set by staff: its questions, how many each sitting
 * gets (a random subset when more are attached), the time limit, and an
 * optional time after which results are shown.
 */
class Exam extends Model
{
    /** @use HasFactory<ExamFactory> */
    use HasFactory, HasTranslations;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'title',
        'description',
        'subject_id',
        'class_level',
        'duration_minutes',
        'question_count',
        'results_at',
        'is_published',
        'sort_order',
    ];

    /**
     * @return BelongsTo<LearningTopic, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(LearningTopic::class, 'subject_id');
    }

    /**
     * @return BelongsToMany<Question, $this>
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }

    /**
     * @return HasMany<ExamAttempt, $this>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function resultsArePublished(): bool
    {
        return $this->results_at === null || $this->results_at->isPast();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
            'results_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }
}
