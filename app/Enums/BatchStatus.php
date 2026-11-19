<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Lifecycle of a live course batch.
 */
enum BatchStatus: string
{
    use HasOptions;

    case Upcoming = 'upcoming';

    case Enrolling = 'enrolling';

    case Full = 'full';

    case Running = 'running';

    case Completed = 'completed';

    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Upcoming => 'শিগগিরই আসছে',
            self::Enrolling => 'ভর্তি চলছে',
            self::Full => 'আসন পূর্ণ',
            self::Running => 'ক্লাস চলছে',
            self::Completed => 'সম্পন্ন',
            self::Cancelled => 'বাতিল',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Upcoming => 'info',
            self::Enrolling => 'success',
            self::Full => 'warning',
            self::Running => 'info',
            self::Completed => 'neutral',
            self::Cancelled => 'danger',
        };
    }
}
