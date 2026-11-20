<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class Localization
{
    private const BANGLA_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    /**
     * @return array<string, array{name: string, short: string, hreflang: string, og_locale: string}>
     */
    public static function supportedLocales(): array
    {
        return config('chintor.locales');
    }

    public static function defaultLocale(): string
    {
        return config('chintor.default_locale');
    }

    public static function isSupported(string $locale): bool
    {
        return array_key_exists($locale, self::supportedLocales());
    }

    /**
     * Route names of non-default locales carry a "{locale}." prefix, so
     * "courses.show" in English is registered as "en.courses.show".
     */
    public static function routeName(string $name, ?string $locale = null): string
    {
        $locale ??= App::getLocale();

        return $locale === self::defaultLocale() ? $name : "{$locale}.{$name}";
    }

    /**
     * Strip any locale prefix from a registered route name.
     */
    public static function baseRouteName(string $name): string
    {
        foreach (array_keys(self::supportedLocales()) as $locale) {
            if (str_starts_with($name, "{$locale}.")) {
                return substr($name, strlen($locale) + 1);
            }
        }

        return $name;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function route(string $name, array $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        return route(self::routeName($name, $locale), $parameters, $absolute);
    }

    /**
     * The URL of the current page in another locale. Falls back to that
     * locale's home page when the current route has no localized twin.
     */
    public static function currentUrlFor(string $locale): string
    {
        $currentRoute = Route::current();
        $currentName = $currentRoute?->getName();

        if ($currentName !== null) {
            $targetName = self::routeName(self::baseRouteName($currentName), $locale);

            if (Route::has($targetName)) {
                // Only URI parameters: route defaults (e.g. Route::view's "view") must not leak into the query string.
                $uriParameters = Arr::only($currentRoute->originalParameters(), $currentRoute->parameterNames());

                return route($targetName, $uriParameters);
            }
        }

        return self::route('home', locale: $locale);
    }

    /**
     * Localize an admin-entered link. On-site paths ("/start?assessment=1")
     * get the locale prefix ("/en/start?assessment=1"); full URLs, "#anchors",
     * "tel:" and "mailto:" links are returned unchanged.
     */
    public static function path(string $link, ?string $locale = null): string
    {
        $locale ??= App::getLocale();

        if (! str_starts_with($link, '/') || str_starts_with($link, '//')) {
            return $link;
        }

        if ($locale === self::defaultLocale() || str_starts_with($link, "/{$locale}/") || $link === "/{$locale}") {
            return url($link);
        }

        return url($link === '/' ? "/{$locale}" : "/{$locale}{$link}");
    }

    /**
     * Convert Latin digits to Bangla digits when the active locale is Bangla.
     */
    public static function digits(string|int|float $value, ?string $locale = null): string
    {
        $value = (string) $value;

        if (($locale ?? App::getLocale()) !== 'bn') {
            return $value;
        }

        return str_replace(range(0, 9), self::BANGLA_DIGITS, $value);
    }
}
