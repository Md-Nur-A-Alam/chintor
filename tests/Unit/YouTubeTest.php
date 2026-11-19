<?php

use App\Support\YouTube;

test('video IDs are extracted from every common YouTube link format', function (string $url) {
    expect(YouTube::videoId($url))->toBe('dQw4w9WgXcQ');
})->with([
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtube.com/watch?v=dQw4w9WgXcQ&t=42s',
    'https://m.youtube.com/watch?feature=share&v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ?si=AbCdEf',
    'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0',
    'https://www.youtube.com/shorts/dQw4w9WgXcQ',
    'https://www.youtube.com/live/dQw4w9WgXcQ',
    'dQw4w9WgXcQ',
]);

test('non-YouTube or malformed links are rejected', function (?string $url) {
    expect(YouTube::videoId($url))->toBeNull();
})->with([
    null,
    '',
    'https://vimeo.com/123456',
    'https://www.youtube.com/watch?v=short',
    'https://evil.example/watch?v=dQw4w9WgXcQ',
    'https://youtube.com.evil.example/watch?v=dQw4w9WgXcQ',
]);

test('embed and thumbnail URLs use privacy-friendly hosts', function () {
    expect(YouTube::embedUrl('dQw4w9WgXcQ'))->toStartWith('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1')
        ->and(YouTube::thumbnailUrl('dQw4w9WgXcQ'))->toBe('https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg');
});
