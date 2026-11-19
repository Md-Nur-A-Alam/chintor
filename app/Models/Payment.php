<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Concerns\HasReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A payment claimed by a customer (mobile banking / bank transfer) or
 * recorded by staff. Staff verify it against the merchant statement
 * (semi-automated payment system, requirements §5.5).
 */
class Payment extends Model
{
    use HasReference, SoftDeletes;

    protected const REFERENCE_CODE = 'P';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'payable_type',
        'payable_id',
        'method',
        'provider',
        'amount',
        'sender_number',
        'transaction_id',
        'receipt_url',
        'status',
        'submitted_by',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'gateway_payload',
    ];

    /**
     * The order or enrollment being paid for.
     *
     * @return MorphTo<Model, $this>
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'amount' => 'integer',
            'verified_at' => 'datetime',
            'gateway_payload' => 'array',
        ];
    }
}
