<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\CourseVariationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One pricing option of a course (e.g. monthly, 3 months, full course).
 * The number and kind of variations differ per course (requirements §3.3).
 */
class CourseVariation extends Model
{
    /** @use HasFactory<CourseVariationFactory> */
    use HasFactory, HasTranslations;

    public const BillingOneTime = 'one_time';

    public const BillingMonthly = 'monthly';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'label',
        'duration_months',
        'billing_type',
        'regular_price',
        'discounted_price',
        'badge',
        'is_active',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function hasDiscount(): bool
    {
        return $this->discounted_price !== null && $this->discounted_price < $this->regular_price;
    }

    public function payablePrice(): int
    {
        return $this->hasDiscount() ? $this->discounted_price : $this->regular_price;
    }

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'badge' => 'array',
            'regular_price' => 'integer',
            'discounted_price' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
