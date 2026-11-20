<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Where a campaign or event mainly runs. The website shows a matching
 * icon and label so families know where to follow it.
 */
enum CampaignChannel: string
{
    use HasOptions;

    case Website = 'website';

    case Facebook = 'facebook';

    case Instagram = 'instagram';

    case YouTube = 'youtube';

    case Event = 'event';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'ওয়েবসাইট অফার',
            self::Facebook => 'Facebook',
            self::Instagram => 'Instagram',
            self::YouTube => 'YouTube',
            self::Event => 'ইভেন্ট',
        };
    }

    /**
     * Icon name for <x-icon>.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Website => 'megaphone',
            self::Facebook => 'facebook',
            self::Instagram => 'instagram',
            self::YouTube => 'youtube',
            self::Event => 'calendar',
        };
    }

    /**
     * Badge tone used by <x-badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Website => 'info',
            self::Event => 'warning',
            default => 'neutral',
        };
    }
}
