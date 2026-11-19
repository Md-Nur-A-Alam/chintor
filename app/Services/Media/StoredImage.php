<?php

namespace App\Services\Media;

/**
 * The result of storing one image with an ImageStore.
 */
final readonly class StoredImage
{
    public function __construct(
        public string $provider,
        public string $url,
        public ?string $path = null,
        public ?string $deleteUrl = null,
    ) {}
}
