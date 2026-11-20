<?php

namespace App\Models;

use App\Enums\TestimonialAudience;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A real review from a parent or student. It is shown only when the
 * person's consent is recorded and it is published (requirements §1).
 */
class Testimonial extends Model
{
    use HasTranslations;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'author_name',
        'author_role',
        'audience',
        'body',
        'rating',
        'photo_url',
        'video_url',
        'course_id',
        'consent_confirmed',
        'is_published',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('is_published', true)->where('consent_confirmed', true)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'author_role' => 'array',
            'audience' => TestimonialAudience::class,
            'body' => 'array',
            'consent_confirmed' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
