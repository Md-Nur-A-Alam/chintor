<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Verification state of a submitted payment.
 */
enum PaymentStatus: string
{
    use HasOptions;

    case Pending = 'pending';

    case Verified = 'verified';

    case Rejected = 'rejected';

    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'যাচাই বাকি',
            self::Verified => 'যাচাই হয়েছে',
            self::Rejected => 'বাতিল',
            self::Refunded => 'রিফান্ড',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Verified => 'success',
            self::Rejected => 'danger',
            self::Refunded => 'neutral',
        };
    }
}
