<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One entry in a lead's timeline: a note, call, status change, assignment…
 */
class LeadActivity extends Model
{
    public const TypeNote = 'note';

    public const TypeCall = 'call';

    public const TypeStatusChange = 'status_change';

    public const TypeAssignment = 'assignment';

    public const TypeFormResubmitted = 'form_resubmitted';

    /**
     * Call outcomes a sales executive can log.
     *
     * @var array<string, string>
     */
    public const CALL_OUTCOMES = [
        'answered' => 'কথা হয়েছে',
        'no_answer' => 'ফোন ধরেনি',
        'busy' => 'ব্যস্ত',
        'callback_requested' => 'পরে কল করতে বলেছে',
        'wrong_number' => 'ভুল নম্বর',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'outcome',
        'body',
        'meta',
    ];

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
