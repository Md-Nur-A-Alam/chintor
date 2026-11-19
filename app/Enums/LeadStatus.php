<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Where a lead stands in the sales pipeline.
 */
enum LeadStatus: string
{
    use HasOptions;

    case New = 'new';

    case Contacted = 'contacted';

    case Interested = 'interested';

    case FollowUp = 'follow_up';

    case PaymentPending = 'payment_pending';

    case Enrolled = 'enrolled';

    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::New => 'নতুন',
            self::Contacted => 'যোগাযোগ হয়েছে',
            self::Interested => 'আগ্রহী',
            self::FollowUp => 'ফলো-আপ',
            self::PaymentPending => 'পেমেন্ট বাকি',
            self::Enrolled => 'ভর্তি হয়েছে',
            self::Lost => 'হারিয়েছে',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Contacted => 'neutral',
            self::Interested => 'success',
            self::FollowUp => 'warning',
            self::PaymentPending => 'warning',
            self::Enrolled => 'success',
            self::Lost => 'danger',
        };
    }
}
