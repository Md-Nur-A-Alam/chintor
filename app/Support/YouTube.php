<?php

namespace App\Support;

/**
 * Helpers for YouTube (including unlisted) video links.
 */
class YouTube
{
    private const ID_PATTERN = '[A-Za-z0-9_-]{11}';

    /**
     * Extract the 11-character video ID from any common YouTube URL:
     * watch?v=, youtu.be/, /embed/, /shorts/, /live/ and youtube-nocookie.
     */
    public static function videoId(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('/^'.self::ID_PATTERN.'$/', $url) === 1) {
            return $url;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^(www\.|m\.|music\.)/', '', $host);

        if (! in_array($host, ['youtube.com', 'youtu.be', 'youtube-nocookie.com'], true)) {
            return null;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);

        if ($host === 'youtu.be' && preg_match('#^/('.self::ID_PATTERN.')#', $path, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('#^/(?:embed|shorts|live|v)/('.self::ID_PATTERN.')#', $path, $matches) === 1) {
            return $matches[1];
        }

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $candidate = $query['v'] ?? null;

        return is_string($candidate) && preg_match('/^'.self::ID_PATTERN.'$/', $candidate) === 1 ? $candidate : null;
    }

    public static function isValid(?string $url): bool
    {
        return self::videoId($url) !== null;
    }

    public static function thumbnailUrl(string $videoId): string
    {
        return "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg";
    }

    /**
     * Privacy-enhanced embed; works for unlisted videos.
     */
    public static function embedUrl(string $videoId, bool $autoplay = true): string
    {
        $query = http_build_query(array_filter([
            'autoplay' => $autoplay ? 1 : null,
            'rel' => 0,
            'modestbranding' => 1,
        ], fn ($value) => $value !== null));

        return "https://www.youtube-nocookie.com/embed/{$videoId}?{$query}";
    }

    public static function watchUrl(string $videoId): string
    {
        return "https://www.youtube.com/watch?v={$videoId}";
    }
}
