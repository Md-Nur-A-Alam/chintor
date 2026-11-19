<?php

namespace App\Models;

use App\Enums\BatchStatus;
use App\Enums\PublishStatus;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use App\Services\SiteSettings;
use App\Support\Localization;
use App\Support\YouTube;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory, HasTranslations, Publishable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'program_id',
        'title',
        'subtitle',
        'summary',
        'description',
        'class_from',
        'class_to',
        'age_from',
        'age_to',
        'live_class_count',
        'class_duration_minutes',
        'duration_months',
        'learning_outcomes',
        'highlights',
        'syllabus',
        'explainer_video_url',
        'thumbnail_url',
        'refund_policy',
        'seo_title',
        'seo_description',
        'status',
        'published_at',
        'is_featured',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsToMany<Teacher, $this>
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class)->withPivot('sort_order')->orderByPivot('sort_order');
    }

    /**
     * @return HasMany<CourseVariation, $this>
     */
    public function variations(): HasMany
    {
        return $this->hasMany(CourseVariation::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<CourseBatch, $this>
     */
    public function batches(): HasMany
    {
        return $this->hasMany(CourseBatch::class)->orderBy('starts_on');
    }

    /**
     * Batches a visitor can still join.
     *
     * @return HasMany<CourseBatch, $this>
     */
    public function openBatches(): HasMany
    {
        return $this->batches()->whereIn('status', [BatchStatus::Upcoming, BatchStatus::Enrolling]);
    }

    /**
     * @return MorphMany<Faq, $this>
     */
    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Requirements §17: a course cannot go live without an explainer video.
     */
    public function canBePublished(): bool
    {
        return YouTube::isValid($this->explainer_video_url);
    }

    /**
     * The cheapest active variation, used for "from ৳…" on cards.
     */
    public function startingVariation(): ?CourseVariation
    {
        return $this->variations
            ->where('is_active', true)
            ->sortBy(fn (CourseVariation $variation) => $variation->payablePrice())
            ->first();
    }

    /**
     * Course-specific refund policy, or the site-wide default.
     *
     * @return array{mode: string, window: int, percent: int}
     */
    public function effectiveRefundPolicy(): array
    {
        return $this->refund_policy ?: app(SiteSettings::class)->refundPolicy();
    }

    /**
     * "৩য়–৫ম শ্রেণি" style label, built from class_from/class_to.
     */
    public function classRangeLabel(): ?string
    {
        if ($this->class_from === null) {
            return null;
        }

        return __('site.course.class_range', [
            'from' => Localization::digits($this->class_from),
            'to' => Localization::digits($this->class_to ?? $this->class_from),
        ]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'summary' => 'array',
            'description' => 'array',
            'learning_outcomes' => 'array',
            'highlights' => 'array',
            'syllabus' => 'array',
            'refund_policy' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'status' => PublishStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'class_from' => 'integer',
            'class_to' => 'integer',
        ];
    }
}
