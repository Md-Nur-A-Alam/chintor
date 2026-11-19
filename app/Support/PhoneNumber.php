<?php

namespace App\Support;

class PhoneNumber
{
    /**
     * Bangladeshi mobile numbers: 01 + operator digit (3–9) + 8 digits,
     * optionally written with a +88 / 88 country prefix.
     */
    private const BANGLADESH_MOBILE_PATTERN = '/^(?:\+?88)?(01[3-9]\d{8})$/';

    /**
     * Normalise a Bangladeshi mobile number to E.164 ("+8801XXXXXXXXX").
     * Spaces, dashes, brackets and Bangla digits are accepted.
     */
    public static function normalizeBangladeshMobile(?string $value): ?string
    {
        $digits = self::clean($value);

        if ($digits === '' || preg_match(self::BANGLADESH_MOBILE_PATTERN, $digits, $matches) !== 1) {
            return null;
        }

        return '+88'.$matches[1];
    }

    /**
     * Any phone number to E.164: Bangladeshi mobiles become +8801…, other
     * numbers keep their digits (with a leading +). Returns the input
     * unchanged when it is not a plausible number, so validation can fail.
     */
    public static function normalize(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $bangladeshi = self::normalizeBangladeshMobile($value);

        if ($bangladeshi !== null) {
            return $bangladeshi;
        }

        $digits = self::clean($value);

        if (preg_match('/^\+?(\d{10,15})$/', $digits, $matches) === 1 && ! str_starts_with($matches[1], '0')) {
            return '+'.$matches[1];
        }

        return $value;
    }

    public static function isBangladeshMobile(?string $value): bool
    {
        return self::normalizeBangladeshMobile($value) !== null;
    }

    /**
     * A "tel:" friendly version of any number (hotlines included).
     */
    public static function forTelLink(?string $value): ?string
    {
        $digits = self::clean($value);

        return $digits === '' ? null : $digits;
    }

    public static function whatsappUrl(?string $value, ?string $message = null): ?string
    {
        $number = self::normalizeBangladeshMobile($value) ?? self::forTelLink($value);

        if ($number === null) {
            return null;
        }

        $url = 'https://wa.me/'.ltrim($number, '+');

        return $message === null ? $url : $url.'?text='.rawurlencode($message);
    }

    private static function clean(?string $value): string
    {
        $value = str_replace(['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'], range(0, 9), (string) $value);

        return (string) preg_replace('/(?!^\+)[^\d]/', '', trim($value));
    }
}
