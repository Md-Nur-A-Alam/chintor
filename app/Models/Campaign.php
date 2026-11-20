<?php

namespace App\Models;

use App\Enums\CampaignChannel;
use App\Models\Concerns\HasTranslations;
use App\Support\HeroBanner;
use App\Support\Localization;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * A campaign or event: an offer, a Facebook/YouTube live session, a contest.
 *
 * It is "running" while active and inside its optional start/end dates, so
 * staff can schedule one in advance and it disappears by itself afterwards.
 * Where it shows is controlled by show_in_hero (homepage hero banner) and
 * show_in_bar (thin announcement bar on every public page).
 */
class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory, HasTranslations;

    /**
     * Cache key for the announcement-bar campaign (see AppServiceProvider).
     */
    public const BarCacheKey = 'campaigns.announcement_bar';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'summary',
        'image_url',
        'banner_url',
        'banner_mobile_url',
        'banner_align',
        'banner_overlay',
        'link_url',
        'button_label',
        'channel',
        'starts_at',
        'ends_at',
        'show_in_hero',
        'show_in_bar',
        'is_active',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::BarCacheKey));
        static::deleted(fn () => Cache::forget(self::BarCacheKey));
    }

    /**
     * Active and inside its schedule right now.
     */
    #[Scope]
    protected function running(Builder $query): void
    {
        $now = now();

        $query->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('sort_order')
            ->latest('id');
    }

    public function isRunning(): bool
    {
        return $this->is_active
            && ($this->starts_at === null || $this->starts_at->isPast())
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Scheduling state for the admin list.
     *
     * @return array{label: string, tone: string}
     */
    public function scheduleState(): array
    {
        return match (true) {
            ! $this->is_active => ['label' => 'বন্ধ', 'tone' => 'neutral'],
            $this->starts_at?->isFuture() === true => ['label' => 'নির্ধারিত', 'tone' => 'info'],
            $this->ends_at?->isPast() === true => ['label' => 'শেষ', 'tone' => 'warning'],
            default => ['label' => 'চলছে', 'tone' => 'success'],
        };
    }

    /**
     * Full-width hero banner settings, or null when it has no wide banner.
     *
     * @return array{desktop: ?string, mobile: ?string, align: string, overlay: int}|null
     */
    public function heroBanner(): ?array
    {
        return $this->banner_url
            ? HeroBanner::settings($this->banner_url, $this->banner_mobile_url, $this->banner_align, $this->banner_overlay)
            : null;
    }

    /**
     * The link with the language prefix added to on-site paths ("/start" → "/en/start").
     */
    public function localizedLink(): ?string
    {
        return $this->link_url ? Localization::path($this->link_url) : null;
    }

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'summary' => 'array',
            'button_label' => 'array',
            'channel' => CampaignChannel::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'banner_overlay' => 'integer',
            'show_in_hero' => 'boolean',
            'show_in_bar' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
