<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Concerns\HasReference;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A prospective customer in the central lead queue. Marketing, managers
 * and admins assign leads to sales executives; a sales executive only
 * sees the leads assigned to them.
 */
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory, HasReference, SoftDeletes;

    protected const REFERENCE_CODE = 'L';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'status',
        'student_name',
        'guardian_name',
        'phone',
        'whatsapp',
        'email',
        'class_level',
        'age',
        'message',
        'guardian_id',
        'student_id',
        'assigned_to',
        'assigned_at',
        'lost_reason',
        'next_follow_up_at',
        'source',
        'landing_page',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'locale',
        'consent_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return HasMany<LeadInterest, $this>
     */
    public function interests(): HasMany
    {
        return $this->hasMany(LeadInterest::class);
    }

    /**
     * @return HasMany<LeadActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    /**
     * @return HasOne<Assessment, $this>
     */
    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class)->latestOfMany();
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->latest();
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->can('leads.view_all') || $this->assigned_to === $user->id;
    }

    /**
     * Leads this staff member may see: everything for queue managers,
     * only their own for sales executives.
     */
    #[Scope]
    protected function visibleTo(Builder $query, User $user): void
    {
        if (! $user->can('leads.view_all')) {
            $query->where('assigned_to', $user->id);
        }
    }

    #[Scope]
    protected function unassigned(Builder $query): void
    {
        $query->whereNull('assigned_to');
    }

    #[Scope]
    protected function open(Builder $query): void
    {
        $query->whereNotIn('status', [LeadStatus::Enrolled, LeadStatus::Lost]);
    }

    public function displayName(): string
    {
        return $this->guardian_name ?: ($this->student_name ?: $this->phone);
    }

    protected function casts(): array
    {
        return [
            'type' => LeadType::class,
            'status' => LeadStatus::class,
            'source' => LeadSource::class,
            'assigned_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'consent_at' => 'datetime',
        ];
    }
}
