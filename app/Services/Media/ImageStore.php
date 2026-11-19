<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;

interface ImageStore
{
    /**
     * The provider name saved on each Media record (e.g. "imgbb").
     */
    public function provider(): string;

    public function store(UploadedFile $file, string $directory): StoredImage;

    public function delete(Media $media): void;

    /**
     * Verify the credentials by talking to the provider.
     *
     * @throws MediaStorageException
     */
    public function checkConnection(): void;
}
