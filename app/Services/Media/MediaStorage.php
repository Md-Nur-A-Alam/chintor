<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Services\SiteSettings;
use Illuminate\Http\UploadedFile;

/**
 * Chooses where images are stored, based on the media settings in the
 * admin panel, and records every upload as a Media row.
 *
 * "auto" picks the best configured provider: Cloudflare R2, then ImgBB,
 * then this server.
 */
class MediaStorage
{
    public const DRIVERS = ['auto', Media::ProviderR2, Media::ProviderImgbb, Media::ProviderLocal];

    /**
     * Why the last upload fell back to local storage (null when it did not).
     */
    public ?string $lastFallbackReason = null;

    public function __construct(private readonly SiteSettings $settings) {}

    public function activeProvider(): string
    {
        $driver = $this->settings->media()['driver'];

        if ($driver !== 'auto') {
            return $driver;
        }

        return match (true) {
            $this->isConfigured(Media::ProviderR2) => Media::ProviderR2,
            $this->isConfigured(Media::ProviderImgbb) => Media::ProviderImgbb,
            default => Media::ProviderLocal,
        };
    }

    public function isConfigured(string $provider): bool
    {
        $media = $this->settings->media();
        $secrets = $this->settings->mediaSecrets();

        return match ($provider) {
            Media::ProviderR2 => filled($media['r2_endpoint'])
                && filled($media['r2_bucket'])
                && filled($media['r2_public_url'])
                && filled($secrets['r2_access_key_id'])
                && filled($secrets['r2_secret_access_key']),
            Media::ProviderImgbb => filled($secrets['imgbb_api_key']),
            Media::ProviderLocal => true,
            default => false,
        };
    }

    /**
     * @throws MediaStorageException
     */
    public function store(string $provider): ImageStore
    {
        if (! $this->isConfigured($provider)) {
            throw new MediaStorageException("“{$provider}” স্টোরেজের ক্রেডেনশিয়াল সেট করা নেই।");
        }

        $media = $this->settings->media();
        $secrets = $this->settings->mediaSecrets();

        return match ($provider) {
            Media::ProviderR2 => new R2ImageStore(
                endpoint: $media['r2_endpoint'],
                bucket: $media['r2_bucket'],
                accessKeyId: $secrets['r2_access_key_id'],
                secretAccessKey: $secrets['r2_secret_access_key'],
                publicUrl: $media['r2_public_url'],
            ),
            Media::ProviderImgbb => new ImgbbImageStore($secrets['imgbb_api_key']),
            default => new LocalImageStore,
        };
    }

    /**
     * @throws MediaStorageException
     */
    public function upload(UploadedFile $file, string $directory = 'images', ?int $uploadedBy = null): Media
    {
        $this->lastFallbackReason = null;
        $provider = $this->activeProvider();

        try {
            $stored = $this->store($provider)->store($file, $directory);
        } catch (MediaStorageException $exception) {
            // In "auto" mode a cloud failure must not block staff: keep the image on this server.
            if ($this->settings->media()['driver'] !== 'auto' || $provider === Media::ProviderLocal) {
                throw $exception;
            }

            $this->lastFallbackReason = $exception->getMessage();
            $stored = $this->store(Media::ProviderLocal)->store($file, $directory);
        }

        [$width, $height] = $this->dimensions($file);

        return Media::create([
            'provider' => $stored->provider,
            'path' => $stored->path,
            'url' => $stored->url,
            'delete_url' => $stored->deleteUrl,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    /**
     * Delete from the provider the file was stored with, even if the active
     * provider has changed since.
     *
     * @throws MediaStorageException
     */
    public function delete(Media $media): void
    {
        if ($this->isConfigured($media->provider)) {
            $this->store($media->provider)->delete($media);
        }

        $media->delete();
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function dimensions(UploadedFile $file): array
    {
        $size = @getimagesize($file->getRealPath());

        return $size === false ? [null, null] : [$size[0], $size[1]];
    }
}
