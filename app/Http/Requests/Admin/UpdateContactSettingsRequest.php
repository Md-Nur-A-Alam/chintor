<?php

namespace App\Http\Requests\Admin;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $whatsapp = $this->string('whatsapp')->trim()->toString();

        $this->merge([
            'whatsapp' => $whatsapp === '' ? null : (PhoneNumber::normalizeBangladeshMobile($whatsapp) ?? $whatsapp),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'hotline' => ['nullable', 'string', 'max:20', 'regex:/^[+\d][\d\s-]{4,18}$/'],
            'whatsapp' => ['nullable', 'string', 'regex:/^\+\d{10,15}$/'],
            'messenger_url' => ['nullable', 'url:https', 'max:255'],
            'facebook_url' => ['nullable', 'url:https', 'max:255'],
            'youtube_url' => ['nullable', 'url:https', 'max:255'],
            'instagram_url' => ['nullable', 'url:https', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'office_hours' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hotline.regex' => 'হেল্পলাইন নম্বরে শুধু সংখ্যা, স্পেস, + বা - থাকতে পারে।',
            'whatsapp.regex' => 'সঠিক WhatsApp নম্বর দিন (যেমন 01XXXXXXXXX বা +44...)।',
        ];
    }
}
