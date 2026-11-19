<?php

namespace App\Models\Concerns;

use App\Support\Localization;
use Illuminate\Support\Facades\App;

/**
 * Translatable attributes are JSON columns shaped like
 * {"bn": "…", "en": "…"} (cast to array on the model).
 *
 * Usage: $course->translate('title'). A missing English value falls back
 * to Bangla, so a page never renders empty.
 */
trait HasTranslations
{
    /**
     * @return mixed The value for the locale (string, list, …) or null.
     */
    public function translate(string $attribute, ?string $locale = null): mixed
    {
        $values = $this->getAttribute($attribute);

        if (! is_array($values)) {
            return $values;
        }

        $locale ??= App::getLocale();
        $value = $values[$locale] ?? null;

        if (blank($value)) {
            $value = $values[Localization::defaultLocale()] ?? null;
        }

        return $value;
    }
}
