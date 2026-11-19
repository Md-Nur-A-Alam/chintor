<?php

namespace App\Support;

class Money
{
    /**
     * Format a whole-taka amount using South Asian digit grouping
     * (1,00,000) and locale-aware digits, e.g. "৳১,২৫,০০০".
     */
    public static function taka(int $amount, ?string $locale = null): string
    {
        $sign = $amount < 0 ? '-' : '';

        return $sign.'৳'.Localization::digits(self::groupDigits(abs($amount)), $locale);
    }

    /**
     * Group digits as thousands first, then pairs: 1234567 becomes "12,34,567".
     */
    public static function groupDigits(int $amount): string
    {
        $digits = (string) $amount;

        if (strlen($digits) <= 3) {
            return $digits;
        }

        $lastThree = substr($digits, -3);
        $leading = substr($digits, 0, -3);
        $leadingGroups = str_split(strrev($leading), 2);

        return strrev(implode(',', $leadingGroups)).','.$lastThree;
    }

    /**
     * Whole-number percentage saved by paying the discounted price.
     */
    public static function discountPercent(int $regular, int $discounted): int
    {
        if ($regular <= 0 || $discounted >= $regular) {
            return 0;
        }

        return (int) floor((($regular - $discounted) / $regular) * 100);
    }
}
