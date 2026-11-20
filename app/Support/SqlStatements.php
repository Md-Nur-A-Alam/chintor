<?php

namespace App\Support;

/**
 * Helpers for the developer SQL console.
 */
class SqlStatements
{
    /**
     * Statements that only read data.
     */
    private const READ_KEYWORDS = ['select', 'show', 'describe', 'desc', 'explain', 'pragma', 'with'];

    /**
     * Split a script into statements on ";", ignoring semicolons inside
     * quotes and comments.
     *
     * @return list<string>
     */
    public static function split(string $sql): array
    {
        $statements = [];
        $current = '';
        $quote = null;
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            $char = $sql[$index];
            $next = $sql[$index + 1] ?? '';

            if ($quote === null && $char === '-' && $next === '-') {
                $index = self::skipUntil($sql, $index, "\n");

                continue;
            }

            if ($quote === null && $char === '/' && $next === '*') {
                $end = strpos($sql, '*/', $index + 2);
                $index = $end === false ? $length : $end + 1;

                continue;
            }

            if (in_array($char, ["'", '"', '`'], true)) {
                if ($quote === null) {
                    $quote = $char;
                } elseif ($quote === $char && ($sql[$index - 1] ?? '') !== '\\') {
                    $quote = null;
                }
            }

            if ($char === ';' && $quote === null) {
                $statements[] = trim($current);
                $current = '';

                continue;
            }

            $current .= $char;
        }

        $statements[] = trim($current);

        return array_values(array_filter($statements, fn (string $statement) => $statement !== ''));
    }

    public static function isRead(string $statement): bool
    {
        $firstWord = strtolower((string) strtok(ltrim($statement, " \t\n\r("), " \t\n\r("));

        return in_array($firstWord, self::READ_KEYWORDS, true);
    }

    private static function skipUntil(string $sql, int $from, string $needle): int
    {
        $position = strpos($sql, $needle, $from);

        return $position === false ? strlen($sql) : $position;
    }
}
