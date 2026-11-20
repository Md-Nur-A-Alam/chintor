<?php

namespace App\Http\Requests\Admin;

use App\Models\Exam;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * A ready-made exam: texts, time limit, how many questions each sitting
 * gets, optional results time, and the attached questions (at least as many
 * as question_count; with more, each sitting gets a random subset).
 */
class SaveExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('learning.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => strtolower(trim((string) $this->input('slug'))),
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $exam = $this->route('exam');

        return [
            'title.bn' => ['required', 'string', 'max:150'],
            'title.en' => ['nullable', 'string', 'max:150'],
            'description.*' => ['nullable', 'string', 'max:1000'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:80', Rule::unique('exams', 'slug')->ignore($exam instanceof Exam ? $exam->id : null)],
            'subject_id' => ['nullable', 'integer', Rule::exists('learning_topics', 'id')->whereNull('parent_id')],
            'class_level' => ['nullable', 'integer', 'between:1,12'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:300'],
            'question_count' => ['required', 'integer', 'min:1', 'max:200'],
            'results_at' => ['nullable', 'date'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['integer', Rule::exists('questions', 'id')],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $attached = count($this->input('question_ids', []) ?: []);

                if ($this->boolean('is_published') && $attached < $this->integer('question_count')) {
                    $validator->errors()->add('question_ids', "প্রকাশ করতে অন্তত {$this->integer('question_count')}টি প্রশ্ন বাছুন (এখন {$attached}টি)।");
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
            'slug.alpha_dash' => 'লিংক-নাম শুধু ইংরেজি অক্ষর, সংখ্যা ও - দিয়ে লিখুন (যেমন: weekly-math-1)।',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            ...$this->safe()->only(['slug', 'subject_id', 'class_level', 'duration_minutes', 'question_count', 'results_at', 'is_published']),
            'title' => TranslatableInput::text($this->validated('title')),
            'description' => TranslatableInput::text($this->validated('description')),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }

    /**
     * @return list<int>
     */
    public function questionIds(): array
    {
        return array_values(array_unique(array_map('intval', $this->validated('question_ids') ?? [])));
    }
}
