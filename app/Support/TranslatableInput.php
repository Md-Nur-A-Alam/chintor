<?php

namespace App\Support;

/**
 * Converts admin form input into the JSON shape stored in translatable
 * columns, and back for re-display in the form.
 *
 *   text():  ['bn' => ' নাম ', 'en' => ''] → ['bn' => 'নাম', 'en' => null]
 *   lines(): ['bn' => "এক\nদুই"]          → ['bn' => ['এক', 'দুই'], 'en' => []]
 */
class TranslatableInput
{
    private const LOCALES = ['bn', 'en'];

    /**
     * @param  array<string, ?string>|null  $input
     * @return array{bn: ?string, en: ?string}|null Null when every locale is empty.
     */
    public static function text(?array $input): ?array
    {
        $values = [];

        foreach (self::LOCALES as $locale) {
            $value = trim((string) ($input[$locale] ?? ''));
            $values[$locale] = $value === '' ? null : $value;
        }

        return array_filter($values) === [] ? null : $values;
    }

    /**
     * One item per non-empty line, per locale.
     *
     * @param  array<string, ?string>|null  $input
     * @return array{bn: list<string>, en: list<string>}|null
     */
    public static function lines(?array $input): ?array
    {
        $values = [];

        foreach (self::LOCALES as $locale) {
            $lines = preg_split('/\R/u', (string) ($input[$locale] ?? '')) ?: [];
            $values[$locale] = array_values(array_filter(array_map('trim', $lines), fn (string $line) => $line !== ''));
        }

        return $values['bn'] === [] && $values['en'] === [] ? null : $values;
    }

    /**
     * Stored list → textarea text, for editing.
     *
     * @param  array<string, list<string>>|null  $stored
     * @return array<string, string>
     */
    public static function linesForForm(?array $stored): array
    {
        $text = [];

        foreach (self::LOCALES as $locale) {
            $text[$locale] = implode("\n", $stored[$locale] ?? []);
        }

        return $text;
    }
}
