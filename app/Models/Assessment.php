<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A free one-to-one assessment with a teacher before enrolling
 * (requirements §3.3.1). The sales team fixes the time by phone.
 */
class Assessment extends Model
{
    use HasUlids;

    /**
     * @var array<string, string>
     */
    public const LEVELS = [
        'beginner' => 'শুরুর পর্যায়',
        'intermediate' => 'মাঝারি',
        'advanced' => 'এগিয়ে আছে',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'lead_id',
        'course_id',
        'teacher_id',
        'scheduled_by',
        'status',
        'scheduled_at',
        'duration_minutes',
        'meeting_url',
        'result_level',
        'teacher_notes',
        'recommended_course_id',
    ];

    /**
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function recommendedCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'recommended_course_id');
    }

    protected function casts(): array
    {
        return [
            'status' => AssessmentStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }
}
