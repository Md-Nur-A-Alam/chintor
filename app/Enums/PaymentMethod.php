<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * How a customer paid (semi-automated; gateways come later).
 */
enum PaymentMethod: string
{
    use HasOptions;

    case Bkash = 'bkash';

    case Nagad = 'nagad';

    case Upay = 'upay';

    case BankTransfer = 'bank_transfer';

    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Bkash => 'বিকাশ',
            self::Nagad => 'নগদ',
            self::Upay => 'উপায়',
            self::BankTransfer => 'ব্যাংক ট্রান্সফার',
            self::Cash => 'নগদ টাকা (অফিসে)',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Bkash => 'neutral',
            self::Nagad => 'neutral',
            self::Upay => 'neutral',
            self::BankTransfer => 'neutral',
            self::Cash => 'neutral',
        };
    }
}
