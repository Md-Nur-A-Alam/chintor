<?php

namespace App\Http\Requests\Admin;

use App\Services\Media\MediaStorage;
use App\Services\SiteSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    /**
     * Secret fields are optional: leaving one blank keeps the saved value.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'driver' => ['required', Rule::in(MediaStorage::DRIVERS)],
            'imgbb_api_key' => ['nullable', 'string', 'max:100'],
            'r2_endpoint' => ['nullable', 'url:https', 'max:255'],
            'r2_bucket' => ['nullable', 'string', 'max:63', 'regex:/^[a-z0-9][a-z0-9-]{1,61}[a-z0-9]$/'],
            'r2_public_url' => ['nullable', 'url:https', 'max:255'],
            'r2_access_key_id' => ['nullable', 'string', 'max:128'],
            'r2_secret_access_key' => ['nullable', 'string', 'max:128'],
            'clear' => ['nullable', 'array'],
            'clear.*' => [Rule::in(array_keys(SiteSettings::MEDIA_SECRET_DEFAULTS))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'r2_bucket.regex' => 'বাকেটের নামে শুধু ছোট হাতের অক্ষর, সংখ্যা ও - থাকতে পারে।',
        ];
    }
}
