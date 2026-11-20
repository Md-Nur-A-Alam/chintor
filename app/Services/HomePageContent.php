<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\Localization;
use Illuminate\Support\Facades\Cache;

/**
 * The editable homepage (admin → Website → Homepage), stored as one JSON
 * row in `settings` under the "homepage" key.
 *
 *   hero      eyebrow/title/subtitle ({bn, en}), up to 3 buttons, an optional
 *             explainer video and the policy trust chips.
 *   stats     up to 4 impact numbers ("1200+" / "students taught"). Empty
 *             until staff enter real figures, so nothing is ever invented.
 *   sections  every homepage section in display order, each with a visible
 *             flag and optional title/subtitle overrides.
 *
 * Empty texts fall back to the built-in wording in lang/{locale}/site.php,
 * so a fresh install shows a complete homepage.
 */
class HomePageContent
{
    public const SettingKey = 'homepage';

    public const MAX_BUTTONS = 3;

    public const MAX_STATS = 4;

    /**
     * <x-button> variants offered for hero buttons.
     */
    public const ButtonStyles = ['primary' => 'মূল (কোরাল)', 'brand' => 'ব্র্যান্ড (টিল)', 'secondary' => 'সাধারণ (সাদা)'];

    /**
     * Section key => admin label, in the default display order.
     *
     * @var array<string, string>
     */
    public const SECTIONS = [
        'stats' => 'পরিসংখ্যান (কতজনকে সেবা দিয়েছি)',
        'campaigns' => 'চলমান ক্যাম্পেইন ও ইভেন্ট',
        'programs' => 'প্রোগ্রাম',
        'courses' => 'জনপ্রিয় কোর্স',
        'steps' => 'কীভাবে শুরু করবেন',
        'why' => 'কেন চিন্তর (রিফান্ড গ্যারান্টিসহ)',
        'teachers' => 'শিক্ষক',
        'parent_reviews' => 'অভিভাবকদের রিভিউ',
        'student_reviews' => 'শিক্ষার্থীদের রিভিউ',
        'books' => 'বই',
        'community' => 'সোশ্যাল মিডিয়া কমিউনিটি',
        'faq' => 'সাধারণ জিজ্ঞাসা',
        'cta' => 'কল ব্যাক ফর্ম',
    ];

    private const CACHE_KEY = 'settings.homepage';

    /**
     * Stored content merged over the defaults.
     *
     * @return array{hero: array<string, mixed>, stats: list<array{value: string, label: array<string, ?string>}>, sections: list<array{key: string, visible: bool, title: ?array<string, ?string>, subtitle: ?array<string, ?string>}>}
     */
    public function all(): array
    {
        $stored = Cache::rememberForever(
            self::CACHE_KEY,
            fn (): array => Setting::firstWhere('key', self::SettingKey)?->value ?? [],
        );

        $defaults = $this->defaults();

        return [
            'hero' => [...$defaults['hero'], ...array_intersect_key($stored['hero'] ?? [], $defaults['hero'])],
            'stats' => $stored['stats'] ?? $defaults['stats'],
            'sections' => $this->mergeSections($stored['sections'] ?? []),
        ];
    }

    /**
     * @param  array{hero: array<string, mixed>, stats: list<array<string, mixed>>, sections: list<array<string, mixed>>}  $content
     */
    public function update(array $content, ?int $updatedBy = null): void
    {
        Setting::updateOrCreate(
            ['key' => self::SettingKey],
            ['value' => $content, 'updated_by' => $updatedBy],
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * The hero, ready for the current locale.
     *
     * @return array{eyebrow: string, title: string, subtitle: string, buttons: list<array{label: string, url: string, style: string}>, video_url: ?string, show_trust_chips: bool}
     */
    public function hero(): array
    {
        $hero = $this->all()['hero'];

        return [
            'eyebrow' => $this->text($hero['eyebrow'], 'site.home.eyebrow'),
            'title' => $this->text($hero['title'], 'site.home.headline'),
            'subtitle' => $this->text($hero['subtitle'], 'site.home.subheadline'),
            'buttons' => array_values(array_map(fn (array $button): array => [
                'label' => $this->text($button['label'] ?? null, ''),
                'url' => Localization::path($button['url']),
                'style' => $button['style'] ?? 'primary',
            ], $hero['buttons'])),
            'video_url' => $hero['video_url'],
            'show_trust_chips' => (bool) $hero['show_trust_chips'],
        ];
    }

    /**
     * Impact numbers that have both a value and a label.
     *
     * @return list<array{value: string, label: string}>
     */
    public function stats(): array
    {
        $stats = [];

        foreach ($this->all()['stats'] as $stat) {
            $label = $this->text($stat['label'] ?? null, '');

            if (filled($stat['value'] ?? null) && $label !== '') {
                $stats[] = ['value' => Localization::digits($stat['value']), 'label' => $label];
            }
        }

        return $stats;
    }

    /**
     * Visible sections in display order with their headings resolved.
     *
     * @return list<array{key: string, title: string, subtitle: ?string}>
     */
    public function visibleSections(): array
    {
        $sections = [];

        foreach ($this->all()['sections'] as $section) {
            if (! $section['visible']) {
                continue;
            }

            $subtitle = $this->text($section['subtitle'], "site.home.sections.{$section['key']}.subtitle");

            $sections[] = [
                'key' => $section['key'],
                'title' => $this->text($section['title'], "site.home.sections.{$section['key']}.title"),
                'subtitle' => $subtitle === '' ? null : $subtitle,
            ];
        }

        return $sections;
    }

    /**
     * @return array{hero: array{eyebrow: null, title: null, subtitle: null, buttons: list<array{label: array<string, string>, url: string, style: string}>, video_url: null, show_trust_chips: bool}, stats: list<never>}
     */
    public function defaults(): array
    {
        return [
            'hero' => [
                'eyebrow' => null,
                'title' => null,
                'subtitle' => null,
                'buttons' => [
                    ['label' => $this->bothLocales('site.cta.start_learning'), 'url' => '/start', 'style' => 'primary'],
                    ['label' => $this->bothLocales('site.cta.free_assessment'), 'url' => '/start?assessment=1', 'style' => 'secondary'],
                ],
                'video_url' => null,
                'show_trust_chips' => true,
            ],
            'stats' => [],
        ];
    }

    /**
     * Saved section settings in their saved order; sections added in a later
     * release (not in the saved list yet) are appended at the end, visible.
     *
     * @param  list<array<string, mixed>>  $stored
     * @return list<array{key: string, visible: bool, title: ?array<string, ?string>, subtitle: ?array<string, ?string>}>
     */
    private function mergeSections(array $stored): array
    {
        $sections = [];

        foreach ($stored as $section) {
            $key = $section['key'] ?? null;

            if (is_string($key) && array_key_exists($key, self::SECTIONS) && ! isset($sections[$key])) {
                $sections[$key] = [
                    'key' => $key,
                    'visible' => (bool) ($section['visible'] ?? true),
                    'title' => $section['title'] ?? null,
                    'subtitle' => $section['subtitle'] ?? null,
                ];
            }
        }

        foreach (array_keys(self::SECTIONS) as $key) {
            $sections[$key] ??= ['key' => $key, 'visible' => true, 'title' => null, 'subtitle' => null];
        }

        return array_values($sections);
    }

    /**
     * A stored {bn, en} text in the current locale, else Bangla, else the translation default.
     *
     * @param  array<string, ?string>|null  $value
     */
    private function text(?array $value, string $fallbackKey): string
    {
        $locale = app()->getLocale();
        $text = $value[$locale] ?? $value[Localization::defaultLocale()] ?? null;

        if (filled($text)) {
            return $text;
        }

        $translated = $fallbackKey === '' ? null : __($fallbackKey);

        return is_string($translated) && $translated !== $fallbackKey ? $translated : '';
    }

    /**
     * @return array<string, string>
     */
    private function bothLocales(string $key): array
    {
        $texts = [];

        foreach (array_keys(Localization::supportedLocales()) as $locale) {
            $texts[$locale] = __($key, [], $locale);
        }

        return $texts;
    }
}
