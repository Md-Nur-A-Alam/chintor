<?php

namespace App\Support;

/**
 * A full-width background banner for the homepage hero, used both by the
 * homepage's own brand banner and by campaigns.
 *
 * Image sizes (shown to staff in the admin forms):
 *   desktop  1920×900 px (about 2.13:1); keep the banner's own artwork on
 *            the side opposite the text, and anything important away from
 *            the outer 10 % (it is cropped on narrower screens).
 *   phone    1080×1350 px (4:5), optional; without it the desktop image is
 *            cropped around its centre.
 *
 * The text block sits left, centre or right over a black-to-transparent
 * overlay that starts from the text's side, so it stays readable on any
 * artwork; "overlay" is the darkest point in percent (0–90).
 */
class HeroBanner
{
    public const DESKTOP_SIZE = '1920×900';

    public const MOBILE_SIZE = '1080×1350';

    public const DEFAULT_OVERLAY = 55;

    public const MAX_OVERLAY = 90;

    /**
     * @var array<string, string>
     */
    public const ALIGNMENTS = [
        'left' => 'বামে',
        'center' => 'মাঝখানে',
        'right' => 'ডানে',
    ];

    /**
     * @return array{desktop: ?string, mobile: ?string, align: string, overlay: int}
     */
    public static function settings(?string $desktop, ?string $mobile, ?string $align, ?int $overlay): array
    {
        return [
            'desktop' => $desktop,
            'mobile' => $mobile,
            'align' => array_key_exists((string) $align, self::ALIGNMENTS) ? $align : 'left',
            'overlay' => max(0, min(self::MAX_OVERLAY, $overlay ?? self::DEFAULT_OVERLAY)),
        ];
    }

    /**
     * CSS background for the overlay on wide screens: darkest at the text's
     * side, fading to clear on the artwork's side.
     */
    public static function overlayCss(string $align, int $overlay): string
    {
        $dark = self::black($overlay);
        $soft = self::black((int) round($overlay * 0.55));

        return match ($align) {
            'right' => "linear-gradient(to left, {$dark} 0%, {$soft} 45%, rgb(0 0 0 / 0) 80%)",
            'center' => "radial-gradient(ellipse 70% 80% at 50% 50%, {$dark} 0%, {$soft} 55%, rgb(0 0 0 / 0.1) 100%)",
            default => "linear-gradient(to right, {$dark} 0%, {$soft} 45%, rgb(0 0 0 / 0) 80%)",
        };
    }

    /**
     * On phones the text covers the whole image, so the overlay is even.
     */
    public static function mobileOverlayCss(int $overlay): string
    {
        return 'linear-gradient(to top, '.self::black($overlay).', '.self::black((int) round($overlay * 0.7)).')';
    }

    private static function black(int $percent): string
    {
        return 'rgb(0 0 0 / '.round($percent / 100, 2).')';
    }
}
