<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\Media\MediaStorage;
use App\Services\Media\MediaStorageException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Image fields in admin forms accept either an uploaded file (stored with
 * the active media storage: local, ImgBB or R2) or a pasted URL, as
 * rendered by <x-form.image>.
 */
trait HandlesImageInput
{
    /**
     * @return array<string, array<int, string>> Validation rules for one image field.
     */
    protected static function imageRules(string $field): array
    {
        return [
            "{$field}_file" => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            "{$field}_url" => ['nullable', 'url', 'max:2048'],
            "{$field}_remove" => ['nullable', 'boolean'],
        ];
    }

    /**
     * The URL to save for an image field, given what is stored now.
     *
     * @throws ValidationException When the storage provider rejects the upload.
     */
    protected function resolveImageUrl(Request $request, string $field, ?string $current = null): ?string
    {
        if ($request->boolean("{$field}_remove")) {
            return null;
        }

        if ($request->hasFile("{$field}_file")) {
            try {
                return app(MediaStorage::class)
                    ->upload($request->file("{$field}_file"), 'images', $request->user()?->id)
                    ->url;
            } catch (MediaStorageException $exception) {
                throw ValidationException::withMessages(["{$field}_file" => $exception->getMessage()]);
            }
        }

        return $request->filled("{$field}_url") ? $request->string("{$field}_url")->toString() : $current;
    }
}
