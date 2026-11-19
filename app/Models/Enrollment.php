<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use App\Models\Concerns\HasReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A student's place in a course batch. When the LMS arrives, an active
 * enrollment is what grants access to the course content.
 */
class Enrollment extends Model
{
    use HasReference, SoftDeletes;

    protected const REFERENCE_CODE = 'E';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'guardian_id',
        'student_id',
        'course_id',
        'course_batch_id',
        'course_variation_id',
        'lead_id',
        'sold_by',
        'status',
        'list_price',
        'discount_amount',
        'payable_amount',
        'refund_policy',
        'first_class_at',
        'refund_eligible_until',
        'activated_at',
        'notes',
    ];

    /**
     * @return BelongsTo<Guardian, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<CourseBatch, $this>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'course_batch_id');
    }

    /**
     * @return BelongsTo<CourseVariation, $this>
     */
    public function variation(): BelongsTo
    {
        return $this->belongsTo(CourseVariation::class, 'course_variation_id');
    }

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    /**
     * @return MorphMany<Payment, $this>
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable')->latest();
    }

    protected function casts(): array
    {
        return [
            'status' => EnrollmentStatus::class,
            'refund_policy' => 'array',
            'first_class_at' => 'datetime',
            'refund_eligible_until' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }
}
