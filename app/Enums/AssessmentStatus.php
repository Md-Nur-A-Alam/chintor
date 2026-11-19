<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * State of a free one-to-one assessment.
 */
enum AssessmentStatus: string
{
    use HasOptions;

    case Requested = 'requested';

    case Scheduled = 'scheduled';

    case Completed = 'completed';

    case NoShow = 'no_show';

    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'অনুরোধ এসেছে',
            self::Scheduled => 'সময় নির্ধারিত',
            self::Completed => 'সম্পন্ন',
            self::NoShow => 'উপস্থিত হয়নি',
            self::Cancelled => 'বাতিল',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Requested => 'info',
            self::Scheduled => 'warning',
            self::Completed => 'success',
            self::NoShow => 'danger',
            self::Cancelled => 'neutral',
        };
    }
}
