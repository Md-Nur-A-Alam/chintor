<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Models\Program;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Create and update a program. attributesForModel() maps the form to
 * model attributes; images are resolved by the controller.
 */
class SaveProgramRequest extends FormRequest
{
    use HandlesImageInput;

    public function authorize(): bool
    {
        return $this->user()?->can('catalog.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('name.en', '')),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('programs', 'slug')->ignore($this->route('program'))],
            'name.bn' => ['required', 'string', 'max:150'],
            'name.en' => ['nullable', 'string', 'max:150'],
            'tagline' => ['nullable', 'array'],
            'tagline.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string', 'max:3000'],
            'icon' => ['nullable', 'string', 'max:30'],
            'accent' => ['required', Rule::in(Program::ACCENTS)],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...self::imageRules('image'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            'slug' => $this->validated('slug'),
            'name' => TranslatableInput::text($this->validated('name')),
            'tagline' => TranslatableInput::text($this->validated('tagline')),
            'description' => TranslatableInput::text($this->validated('description')),
            'icon' => $this->validated('icon'),
            'accent' => $this->validated('accent'),
            'status' => $this->validated('status'),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
