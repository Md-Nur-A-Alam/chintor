<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Channel a lead came from.
 */
enum LeadSource: string
{
    use HasOptions;

    case Website = 'website';

    case Facebook = 'facebook';

    case WhatsApp = 'whatsapp';

    case Phone = 'phone';

    case Referral = 'referral';

    case WalkIn = 'walk_in';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'ওয়েবসাইট',
            self::Facebook => 'Facebook',
            self::WhatsApp => 'WhatsApp',
            self::Phone => 'ফোন কল',
            self::Referral => 'রেফারেল',
            self::WalkIn => 'অফিসে এসে',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Website => 'info',
            self::Facebook => 'info',
            self::WhatsApp => 'info',
            self::Phone => 'neutral',
            self::Referral => 'success',
            self::WalkIn => 'neutral',
        };
    }
}
