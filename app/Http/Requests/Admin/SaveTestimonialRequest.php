<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Only real reviews: a review can be published only with recorded consent.
 */
class SaveTestimonialRequest extends FormRequest
{
    use HandlesImageInput;

    public function authorize(): bool
    {
        return $this->user()?->can('content.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'consent_confirmed' => $this->boolean('consent_confirmed'),
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:100'],
            'author_role.*' => ['nullable', 'string', 'max:120'],
            'body.bn' => ['required', 'string', 'max:2000'],
            'body.en' => ['nullable', 'string', 'max:2000'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'course_id' => ['nullable', Rule::exists('courses', 'id')],
            'consent_confirmed' => ['boolean'],
            'is_published' => ['boolean', Rule::prohibitedIf(fn () => $this->boolean('is_published') && ! $this->boolean('consent_confirmed'))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...self::imageRules('photo'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'is_published.prohibited' => 'সম্মতি নিশ্চিত না করে রিভিউ প্রকাশ করা যাবে না।',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            ...$this->safe()->only(['author_name', 'rating', 'video_url', 'course_id', 'consent_confirmed', 'is_published']),
            'author_role' => TranslatableInput::text($this->validated('author_role')),
            'body' => TranslatableInput::text($this->validated('body')),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
