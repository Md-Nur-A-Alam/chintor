<?php

namespace App\Http\Requests\Admin;

use App\Enums\CampaignChannel;
use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCampaignRequest extends FormRequest
{
    use HandlesImageInput;

    public function authorize(): bool
    {
        return $this->user()?->can('homepage.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_in_hero' => $this->boolean('show_in_hero'),
            'show_in_bar' => $this->boolean('show_in_bar'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title.bn' => ['required', 'string', 'max:120'],
            'title.en' => ['nullable', 'string', 'max:120'],
            'summary.*' => ['nullable', 'string', 'max:300'],
            'button_label.*' => ['nullable', 'string', 'max:40'],
            'link_url' => ['nullable', 'string', 'max:500', 'regex:/^(\/|https?:\/\/|tel:|mailto:|#)/i'],
            'channel' => ['required', Rule::enum(CampaignChannel::class)],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'show_in_hero' => ['boolean'],
            'show_in_bar' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...self::imageRules('image'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'link_url.regex' => 'লিংক "/" দিয়ে (সাইটের পেজ), অথবা https://, tel:, mailto: বা # দিয়ে শুরু করুন।',
            'ends_at.after' => 'শেষের সময় শুরুর পরে হতে হবে।',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            ...$this->safe()->only(['channel', 'starts_at', 'ends_at', 'show_in_hero', 'show_in_bar', 'is_active']),
            'title' => TranslatableInput::text($this->validated('title')),
            'summary' => TranslatableInput::text($this->validated('summary')),
            'button_label' => TranslatableInput::text($this->validated('button_label')),
            'link_url' => $this->filled('link_url') ? trim($this->validated('link_url')) : null,
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
