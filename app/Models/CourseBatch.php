<?php

namespace App\Models;

use App\Enums\BatchStatus;
use App\Models\Concerns\HasTranslations;
use Database\Factories\CourseBatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A live batch of a course: schedule, start date and seats.
 */
class CourseBatch extends Model
{
    /** @use HasFactory<CourseBatchFactory> */
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * Day keys stored in `class_days`, in week order (Saturday first, as in Bangladesh).
     *
     * @var array<string, string>
     */
    public const DAYS = [
        'sat' => 'শনি',
        'sun' => 'রবি',
        'mon' => 'সোম',
        'tue' => 'মঙ্গল',
        'wed' => 'বুধ',
        'thu' => 'বৃহস্পতি',
        'fri' => 'শুক্র',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'code',
        'name',
        'class_days',
        'class_start_time',
        'class_end_time',
        'orientation_at',
        'starts_on',
        'ends_on',
        'enrollment_deadline',
        'seat_limit',
        'seats_taken',
        'status',
        'meeting_url',
    ];

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function seatsLeft(): ?int
    {
        return $this->seat_limit === null ? null : max(0, $this->seat_limit - $this->seats_taken);
    }

    public function isOpenForEnrollment(): bool
    {
        return in_array($this->status, [BatchStatus::Upcoming, BatchStatus::Enrolling], true)
            && ($this->seatsLeft() === null || $this->seatsLeft() > 0)
            && ($this->enrollment_deadline === null || $this->enrollment_deadline->endOfDay()->isFuture());
    }

    /**
     * "শনি · সোম · বুধ" (Bangla) or "Sat · Mon · Wed" (English).
     */
    public function daysLabel(): string
    {
        return collect($this->class_days ?? [])
            ->map(fn (string $day) => app()->getLocale() === 'bn' ? (self::DAYS[$day] ?? $day) : ucfirst($day))
            ->join(' · ');
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'class_days' => 'array',
            'orientation_at' => 'datetime',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'enrollment_deadline' => 'date',
            'status' => BatchStatus::class,
            'seat_limit' => 'integer',
            'seats_taken' => 'integer',
        ];
    }
}
