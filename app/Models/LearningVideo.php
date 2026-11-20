<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\YouTube;
use Database\Factories\LearningVideoFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A short YouTube explainer (geometry, puzzles, IQ, science…) in the free
 * video library. Up to MAX_FEATURED can be featured on the homepage.
 */
class LearningVideo extends Model
{
    /** @use HasFactory<LearningVideoFactory> */
    use HasFactory, HasTranslations;

    public const MAX_FEATURED = 6;

    public const Vertical = 'vertical';

    public const Horizontal = 'horizontal';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'youtube_url',
        'orientation',
        'subject_id',
        'topic_id',
        'is_featured',
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
     * @return BelongsTo<LearningTopic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(LearningTopic::class, 'topic_id');
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function isVertical(): bool
    {
        return $this->orientation !== self::Horizontal;
    }

    /**
     * Shorts links (/shorts/…) are vertical; other links are treated as wide.
     */
    public static function orientationFor(string $url): string
    {
        return str_contains(strtolower($url), '/shorts/') ? self::Vertical : self::Horizontal;
    }

    public function videoId(): ?string
    {
        return YouTube::videoId($this->youtube_url);
    }

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
