<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Stores images in a Cloudflare R2 bucket through its S3-compatible API.
 * The disk is built at runtime from credentials saved in the admin panel.
 */
class R2ImageStore implements ImageStore
{
    private ?Filesystem $disk = null;

    public function __construct(
        private readonly string $endpoint,
        private readonly string $bucket,
        private readonly string $accessKeyId,
        private readonly string $secretAccessKey,
        private readonly string $publicUrl,
    ) {}

    public function provider(): string
    {
        return Media::ProviderR2;
    }

    public function store(UploadedFile $file, string $directory): StoredImage
    {
        try {
            $path = $this->disk()->putFile($directory, $file);
        } catch (Throwable $exception) {
            throw $this->friendlyException($exception);
        }

        if ($path === false) {
            throw new MediaStorageException('Cloudflare R2-তে ছবিটি আপলোড করা যায়নি।');
        }

        return new StoredImage(
            provider: $this->provider(),
            url: rtrim($this->publicUrl, '/').'/'.$path,
            path: $path,
        );
    }

    public function delete(Media $media): void
    {
        if ($media->path === null) {
            return;
        }

        try {
            $this->disk()->delete($media->path);
        } catch (Throwable $exception) {
            throw $this->friendlyException($exception);
        }
    }

    public function checkConnection(): void
    {
        try {
            $this->disk()->put('.chintor-connection-check', 'ok');
            $this->disk()->delete('.chintor-connection-check');
        } catch (Throwable $exception) {
            throw $this->friendlyException($exception);
        }
    }

    private function disk(): Filesystem
    {
        return $this->disk ??= Storage::build([
            'driver' => 's3',
            'key' => $this->accessKeyId,
            'secret' => $this->secretAccessKey,
            'region' => 'auto',
            'bucket' => $this->bucket,
            'endpoint' => $this->endpoint,
            'url' => $this->publicUrl,
            'use_path_style_endpoint' => true,
            'throw' => true,
        ]);
    }

    private function friendlyException(Throwable $exception): MediaStorageException
    {
        $message = $exception->getPrevious()?->getMessage() ?? $exception->getMessage();

        // Never echo credentials back; keep only the provider's short reason.
        $reason = str($message)->replace([$this->accessKeyId, $this->secretAccessKey], '***')->limit(160);

        return new MediaStorageException("Cloudflare R2 সংযোগ ব্যর্থ: {$reason}", previous: $exception);
    }
}
