<?php

namespace App\Http\Requests\Admin;

use App\Models\Course;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('content.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['nullable', Rule::exists('courses', 'id')],
            'question.bn' => ['required', 'string', 'max:300'],
            'question.en' => ['nullable', 'string', 'max:300'],
            'answer.bn' => ['required', 'string', 'max:3000'],
            'answer.en' => ['nullable', 'string', 'max:3000'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * A FAQ without a course is a general FAQ (home page).
     *
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        $courseId = $this->validated('course_id');

        return [
            'faqable_type' => $courseId ? (new Course)->getMorphClass() : null,
            'faqable_id' => $courseId ?: null,
            'question' => TranslatableInput::text($this->validated('question')),
            'answer' => TranslatableInput::text($this->validated('answer')),
            'is_active' => $this->validated('is_active'),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
