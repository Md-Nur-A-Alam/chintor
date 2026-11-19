<?php

namespace App\Enums\Concerns;

/**
 * Shared helpers for labelled, string-backed enums.
 */
trait HasOptions
{
    /**
     * Value => label pairs for <select> inputs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
