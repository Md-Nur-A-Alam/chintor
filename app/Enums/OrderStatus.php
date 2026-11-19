<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Fulfilment state of a book order.
 */
enum OrderStatus: string
{
    use HasOptions;

    case PendingVerification = 'pending_verification';

    case Confirmed = 'confirmed';

    case Packed = 'packed';

    case Shipped = 'shipped';

    case Delivered = 'delivered';

    case Cancelled = 'cancelled';

    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::PendingVerification => 'পেমেন্ট যাচাই বাকি',
            self::Confirmed => 'কনফার্মড',
            self::Packed => 'প্যাক হয়েছে',
            self::Shipped => 'কুরিয়ারে পাঠানো',
            self::Delivered => 'ডেলিভারড',
            self::Cancelled => 'বাতিল',
            self::Returned => 'ফেরত এসেছে',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::PendingVerification => 'warning',
            self::Confirmed => 'info',
            self::Packed => 'info',
            self::Shipped => 'info',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
            self::Returned => 'danger',
        };
    }
}
