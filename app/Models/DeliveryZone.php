<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\DeliveryZoneFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A delivery charge band, e.g. "inside Dhaka" and "outside Dhaka".
 */
class DeliveryZone extends Model
{
    /** @use HasFactory<DeliveryZoneFactory> */
    use HasFactory, HasTranslations;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'charge',
        'districts',
        'estimated_days',
        'is_default',
        'is_active',
        'sort_order',
    ];

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * The zone for a district: the zone listing it, else the default zone.
     */
    public static function forDistrict(string $district): ?self
    {
        $zones = self::query()->active()->get();

        return $zones->first(fn (self $zone) => in_array($district, $zone->districts ?? [], true))
            ?? $zones->firstWhere('is_default', true)
            ?? $zones->first();
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'districts' => 'array',
            'charge' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
