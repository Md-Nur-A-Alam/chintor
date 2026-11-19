<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public const MAX_KILOBYTES = 5120;

    public function authorize(): bool
    {
        return $this->user()?->can('media.manage') ?? false;
    }

    /**
     * SVG is excluded on purpose: it can carry scripts.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:'.self::MAX_KILOBYTES],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.required' => 'একটি ছবি বাছাই করুন।',
            'image.image' => 'শুধু ছবি আপলোড করা যাবে।',
            'image.mimes' => 'JPG, PNG, WebP বা GIF ছবি দিন।',
            'image.max' => 'ছবির আকার সর্বোচ্চ ৫ MB হতে পারে।',
        ];
    }
}
