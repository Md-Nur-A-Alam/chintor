<?php

namespace App\Http\Requests\Admin;

use App\Models\LearningTopic;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A subject (no parent) or a topic inside a subject. The slug is the
 * English name used in website links, e.g. /learn?subject=math&topic=geometry.
 */
class SaveLearningTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('learning.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => strtolower(trim((string) $this->input('slug')))]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $topic = $this->route('learning_topic');

        return [
            'parent_id' => ['nullable', 'integer', Rule::exists('learning_topics', 'id')->whereNull('parent_id')],
            'name.bn' => ['required', 'string', 'max:80'],
            'name.en' => ['nullable', 'string', 'max:80'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:60', Rule::unique('learning_topics', 'slug')->ignore($topic instanceof LearningTopic ? $topic->id : null)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.alpha_dash' => 'লিংক-নাম শুধু ইংরেজি অক্ষর, সংখ্যা ও - দিয়ে লিখুন (যেমন: math, geometry)।',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            'name' => TranslatableInput::text($this->validated('name')),
            'slug' => $this->validated('slug'),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
