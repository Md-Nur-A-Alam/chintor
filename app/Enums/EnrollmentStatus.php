<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * State of a course enrollment (the future LMS access record).
 */
enum EnrollmentStatus: string
{
    use HasOptions;

    case PendingPayment = 'pending_payment';

    case Active = 'active';

    case Completed = 'completed';

    case RefundRequested = 'refund_requested';

    case Refunded = 'refunded';

    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'পেমেন্ট যাচাই বাকি',
            self::Active => 'সক্রিয়',
            self::Completed => 'সম্পন্ন',
            self::RefundRequested => 'রিফান্ড অনুরোধ',
            self::Refunded => 'রিফান্ড হয়েছে',
            self::Cancelled => 'বাতিল',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::PendingPayment => 'warning',
            self::Active => 'success',
            self::Completed => 'neutral',
            self::RefundRequested => 'warning',
            self::Refunded => 'neutral',
            self::Cancelled => 'danger',
        };
    }
}
