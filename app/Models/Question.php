<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * One MCQ in the Exam Arena question bank. `options` is a list of answer
 * texts; `correct_option` is the 0-based index of the right one. After an
 * exam the result page shows the explanation (text, image and/or a Google
 * Drive PDF link).
 */
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    public const MIN_OPTIONS = 2;

    public const MAX_OPTIONS = 5;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'subject_id',
        'topic_id',
        'class_level',
        'body',
        'image_url',
        'options',
        'correct_option',
        'explanation',
        'explanation_image_url',
        'explanation_pdf_url',
        'is_active',
    ];

    /**
     * @return BelongsTo<LearningTopic, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(LearningTopic::class, 'subject_id');
    }

    /**
     * @return BelongsTo<LearningTopic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(LearningTopic::class, 'topic_id');
    }

    /**
     * @return BelongsToMany<Exam, $this>
     */
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function hasExplanation(): bool
    {
        return filled($this->explanation) || filled($this->explanation_image_url) || filled($this->explanation_pdf_url);
    }

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_option' => 'integer',
            'class_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
