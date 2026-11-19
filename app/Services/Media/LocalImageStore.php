<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Stores images on this server under public/uploads.
 */
class LocalImageStore implements ImageStore
{
    private const DISK = 'uploads';

    public function provider(): string
    {
        return Media::ProviderLocal;
    }

    public function store(UploadedFile $file, string $directory): StoredImage
    {
        $path = Storage::disk(self::DISK)->putFile($directory, $file);

        if ($path === false) {
            throw new MediaStorageException('ছবিটি সার্ভারে সেভ করা যায়নি।');
        }

        return new StoredImage(
            provider: $this->provider(),
            url: Storage::disk(self::DISK)->url($path),
            path: $path,
        );
    }

    public function delete(Media $media): void
    {
        if ($media->path !== null) {
            Storage::disk(self::DISK)->delete($media->path);
        }
    }

    public function checkConnection(): void
    {
        try {
            Storage::disk(self::DISK)->put('.connection-check', 'ok');
            Storage::disk(self::DISK)->delete('.connection-check');
        } catch (Throwable) {
            throw new MediaStorageException('public/uploads ফোল্ডারে লেখা যাচ্ছে না। ফোল্ডারের পারমিশন দেখুন।');
        }
    }
}
