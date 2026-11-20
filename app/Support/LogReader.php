<?php

namespace App\Support;

/**
 * Reads recent entries from the Laravel log files for the developer
 * error viewer. Only the tail of each file is read, so large logs stay cheap.
 */
class LogReader
{
    private const TAIL_BYTES = 512 * 1024;

    /**
     * @return list<array{time: string, environment: string, level: string, message: string, context: string, file: string}>
     */
    public static function latest(int $limit = 100, ?string $level = null): array
    {
        $entries = [];

        foreach (self::files() as $file) {
            foreach (self::parse(self::tail($file)) as $entry) {
                if ($level === null || $entry['level'] === $level) {
                    $entries[] = [...$entry, 'file' => basename($file)];
                }
            }
        }

        usort($entries, fn (array $a, array $b) => strcmp($b['time'], $a['time']));

        return array_slice($entries, 0, $limit);
    }

    /**
     * @return list<string>
     */
    public static function files(): array
    {
        return glob(storage_path('logs/*.log')) ?: [];
    }

    public static function clear(): void
    {
        foreach (self::files() as $file) {
            file_put_contents($file, '');
        }
    }

    private static function tail(string $file): string
    {
        $size = filesize($file) ?: 0;
        $handle = fopen($file, 'rb');

        if ($handle === false) {
            return '';
        }

        fseek($handle, max(0, $size - self::TAIL_BYTES));
        $contents = (string) stream_get_contents($handle);
        fclose($handle);

        return $contents;
    }

    /**
     * @return list<array{time: string, environment: string, level: string, message: string, context: string}>
     */
    private static function parse(string $contents): array
    {
        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}[^\]]*)\] (\w+)\.(\w+): (.*?)(?=^\[\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}|\z)/ms';

        preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER);

        return array_map(function (array $match): array {
            $body = rtrim($match[4]);
            $firstLineEnd = strpos($body, "\n");

            return [
                'time' => $match[1],
                'environment' => $match[2],
                'level' => strtolower($match[3]),
                'message' => mb_substr($firstLineEnd === false ? $body : substr($body, 0, $firstLineEnd), 0, 1000),
                'context' => mb_substr($firstLineEnd === false ? '' : substr($body, $firstLineEnd + 1), 0, 6000),
            ];
        }, $matches);
    }
}
