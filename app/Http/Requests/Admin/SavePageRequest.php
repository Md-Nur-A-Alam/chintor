<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Models\Page;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SavePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('content.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->input('slug') ?: $this->input('title.en', ''))]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('pages', 'slug')->ignore($this->route('page'))],
            'type' => ['required', Rule::in(array_keys(Page::TYPES))],
            'title.bn' => ['required', 'string', 'max:150'],
            'title.en' => ['nullable', 'string', 'max:150'],
            'body.*' => ['nullable', 'string', 'max:50000'],
            'seo_description.*' => ['nullable', 'string', 'max:170'],
            'status' => ['required', Rule::enum(PublishStatus::class)],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            'slug' => $this->validated('slug'),
            'type' => $this->validated('type'),
            'title' => TranslatableInput::text($this->validated('title')),
            'body' => TranslatableInput::text($this->validated('body')),
            'seo_description' => TranslatableInput::text($this->validated('seo_description')),
            'status' => $this->validated('status'),
        ];
    }
}
