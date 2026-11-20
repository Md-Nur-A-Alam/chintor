<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * One MCQ. Options are posted as up to MAX_OPTIONS text boxes (blank ones
 * are dropped); "correct_option" is the index of the right box. The
 * explanation PDF must be a Google Drive / Docs link.
 */
class SaveQuestionRequest extends FormRequest
{
    use HandlesImageInput;

    public function authorize(): bool
    {
        return $this->user()?->can('learning.manage') ?? false;
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
            'subject_id' => ['required', 'integer', Rule::exists('learning_topics', 'id')->whereNull('parent_id')],
            'topic_id' => ['nullable', 'integer', Rule::exists('learning_topics', 'id')->where('parent_id', $this->integer('subject_id'))],
            'class_level' => ['nullable', 'integer', 'between:1,12'],
            'body' => ['required', 'string', 'max:3000'],
            'options' => ['required', 'array', 'max:'.Question::MAX_OPTIONS],
            'options.*' => ['nullable', 'string', 'max:500'],
            'correct_option' => ['required', 'integer', 'min:0', 'max:'.(Question::MAX_OPTIONS - 1)],
            'explanation' => ['nullable', 'string', 'max:5000'],
            'explanation_pdf_url' => ['nullable', 'url:https', 'max:500', 'regex:#^https://(drive|docs)\.google\.com/#i'],
            'is_active' => ['boolean'],
            ...self::imageRules('image'),
            ...self::imageRules('explanation_image'),
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $options = $this->input('options', []);
                $filled = array_filter(is_array($options) ? $options : [], fn ($option) => trim((string) $option) !== '');

                if (count($filled) < Question::MIN_OPTIONS) {
                    $validator->errors()->add('options', 'অন্তত '.Question::MIN_OPTIONS.'টি উত্তর-বিকল্প লিখুন।');
                }

                if (! array_key_exists($this->integer('correct_option'), $filled)) {
                    $validator->errors()->add('correct_option', 'সঠিক উত্তর হিসেবে একটি লেখা-থাকা বিকল্প বাছুন।');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'explanation_pdf_url.regex' => 'PDF-এর জন্য Google Drive-এর লিংক দিন (https://drive.google.com/…)।',
            'topic_id.exists' => 'টপিকটি বাছাই করা বিষয়ের ভেতরের হতে হবে।',
        ];
    }

    /**
     * Blank option boxes are removed and the correct index re-counted.
     *
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        $options = [];
        $correct = 0;

        foreach ($this->validated('options') as $index => $option) {
            if (trim((string) $option) === '') {
                continue;
            }

            if ((int) $index === (int) $this->validated('correct_option')) {
                $correct = count($options);
            }

            $options[] = trim($option);
        }

        return [
            ...$this->safe()->only(['subject_id', 'topic_id', 'class_level', 'is_active']),
            'body' => trim($this->validated('body')),
            'options' => $options,
            'correct_option' => $correct,
            'explanation' => $this->filled('explanation') ? trim($this->validated('explanation')) : null,
            'explanation_pdf_url' => $this->validated('explanation_pdf_url'),
        ];
    }
}
