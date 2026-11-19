<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Concerns\HasReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A book order placed without logging in; delivered by courier.
 */
class Order extends Model
{
    use HasReference, SoftDeletes;

    protected const REFERENCE_CODE = 'O';

    /**
     * Couriers commonly used in Bangladesh.
     *
     * @var array<string, string>
     */
    public const COURIERS = [
        'steadfast' => 'Steadfast',
        'pathao' => 'Pathao',
        'redx' => 'RedX',
        'sundarban' => 'সুন্দরবন কুরিয়ার',
        'sa_paribahan' => 'এস এ পরিবহন',
        'other' => 'অন্যান্য',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'guardian_id',
        'lead_id',
        'customer_name',
        'phone',
        'alt_phone',
        'shipping_address',
        'district',
        'upazila',
        'delivery_zone_id',
        'status',
        'subtotal',
        'discount_amount',
        'delivery_charge',
        'total',
        'courier',
        'tracking_code',
        'shipped_at',
        'delivered_at',
        'notes',
        'staff_notes',
        'locale',
        'ip_address',
    ];

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return BelongsTo<DeliveryZone, $this>
     */
    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    /**
     * @return BelongsTo<Guardian, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
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
            'status' => OrderStatus::class,
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'subtotal' => 'integer',
            'delivery_charge' => 'integer',
            'total' => 'integer',
        ];
    }
}
