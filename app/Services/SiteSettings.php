<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Admin-editable site settings stored in the `settings` table.
 *
 * Public values (contact channels) are cached. Credentials (media storage)
 * are never cached, because the cache store is not encrypted.
 */
class SiteSettings
{
    public const ContactKey = 'contact';

    public const MediaKey = 'media';

    public const RefundPolicyKey = 'refund_policy';

    private const CONTACT_CACHE_KEY = 'settings.contact';

    private const REFUND_CACHE_KEY = 'settings.refund_policy';

    /**
     * Site-wide refund policy (requirements §3.3.1). "classes" mode stays
     * disabled until the LMS can count attended classes.
     *
     * @var array{mode: string, window: int, percent: int}
     */
    public const REFUND_DEFAULTS = [
        'mode' => 'days',
        'window' => 7,
        'percent' => 70,
    ];

    /**
     * @var array{hotline: ?string, whatsapp: ?string, messenger_url: ?string, facebook_url: ?string, youtube_url: ?string, instagram_url: ?string, email: ?string, office_hours: ?string, address: ?string}
     */
    public const CONTACT_DEFAULTS = [
        'hotline' => null,
        'whatsapp' => null,
        'messenger_url' => null,
        'facebook_url' => null,
        'youtube_url' => null,
        'instagram_url' => null,
        'email' => null,
        'office_hours' => '09:00-22:00',
        'address' => null,
    ];

    /**
     * @var array{driver: string, r2_endpoint: ?string, r2_bucket: ?string, r2_public_url: ?string}
     */
    public const MEDIA_DEFAULTS = [
        'driver' => 'auto',
        'r2_endpoint' => null,
        'r2_bucket' => null,
        'r2_public_url' => null,
    ];

    /**
     * @var array{imgbb_api_key: ?string, r2_access_key_id: ?string, r2_secret_access_key: ?string}
     */
    public const MEDIA_SECRET_DEFAULTS = [
        'imgbb_api_key' => null,
        'r2_access_key_id' => null,
        'r2_secret_access_key' => null,
    ];

    /**
     * @return array<string, ?string>
     */
    public function contact(): array
    {
        $stored = Cache::rememberForever(
            self::CONTACT_CACHE_KEY,
            fn (): array => Setting::firstWhere('key', self::ContactKey)?->value ?? [],
        );

        return [...self::CONTACT_DEFAULTS, ...array_intersect_key($stored, self::CONTACT_DEFAULTS)];
    }

    /**
     * @param  array<string, ?string>  $values
     */
    public function updateContact(array $values, ?int $updatedBy = null): void
    {
        Setting::updateOrCreate(
            ['key' => self::ContactKey],
            ['value' => array_intersect_key($values, self::CONTACT_DEFAULTS), 'updated_by' => $updatedBy],
        );

        Cache::forget(self::CONTACT_CACHE_KEY);
    }

    /**
     * @return array{mode: string, window: int, percent: int}
     */
    public function refundPolicy(): array
    {
        $stored = Cache::rememberForever(
            self::REFUND_CACHE_KEY,
            fn (): array => Setting::firstWhere('key', self::RefundPolicyKey)?->value ?? [],
        );

        return [...self::REFUND_DEFAULTS, ...array_intersect_key($stored, self::REFUND_DEFAULTS)];
    }

    /**
     * @param  array{mode: string, window: int, percent: int}  $policy
     */
    public function updateRefundPolicy(array $policy, ?int $updatedBy = null): void
    {
        Setting::updateOrCreate(
            ['key' => self::RefundPolicyKey],
            ['value' => array_intersect_key($policy, self::REFUND_DEFAULTS), 'updated_by' => $updatedBy],
        );

        Cache::forget(self::REFUND_CACHE_KEY);
    }

    /**
     * Legal identifiers stay in the environment file (config/chintor.php).
     *
     * @return array{trade_license: ?string, tin: ?string}
     */
    public function legal(): array
    {
        return config('chintor.legal');
    }

    /**
     * @return array{driver: string, r2_endpoint: ?string, r2_bucket: ?string, r2_public_url: ?string}
     */
    public function media(): array
    {
        $stored = $this->mediaSetting()?->value ?? [];

        return [...self::MEDIA_DEFAULTS, ...array_intersect_key($stored, self::MEDIA_DEFAULTS)];
    }

    /**
     * @return array{imgbb_api_key: ?string, r2_access_key_id: ?string, r2_secret_access_key: ?string}
     */
    public function mediaSecrets(): array
    {
        $stored = $this->mediaSetting()?->secrets ?? [];

        return [...self::MEDIA_SECRET_DEFAULTS, ...array_intersect_key($stored, self::MEDIA_SECRET_DEFAULTS)];
    }

    /**
     * Blank secrets keep their saved value, so the admin form never has to
     * echo credentials back into the page.
     *
     * @param  array<string, ?string>  $values
     * @param  array<string, ?string>  $secrets
     * @param  list<string>  $secretsToClear
     */
    public function updateMedia(array $values, array $secrets, array $secretsToClear = [], ?int $updatedBy = null): void
    {
        $mergedSecrets = $this->mediaSecrets();

        foreach (array_intersect_key($secrets, self::MEDIA_SECRET_DEFAULTS) as $name => $secret) {
            if (filled($secret)) {
                $mergedSecrets[$name] = $secret;
            }
        }

        foreach ($secretsToClear as $name) {
            if (array_key_exists($name, self::MEDIA_SECRET_DEFAULTS)) {
                $mergedSecrets[$name] = null;
            }
        }

        Setting::updateOrCreate(
            ['key' => self::MediaKey],
            [
                'value' => array_intersect_key($values, self::MEDIA_DEFAULTS),
                'secrets' => $mergedSecrets,
                'updated_by' => $updatedBy,
            ],
        );
    }

    private function mediaSetting(): ?Setting
    {
        return Setting::firstWhere('key', self::MediaKey);
    }
}
