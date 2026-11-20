<?php

namespace App\Http\Requests\Admin;

use App\Models\LearningVideo;
use App\Support\TranslatableInput;
use App\Support\YouTube;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * A learning video. At most LearningVideo::MAX_FEATURED may be featured.
 */
class SaveLearningVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('learning.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title.bn' => ['required', 'string', 'max:150'],
            'title.en' => ['nullable', 'string', 'max:150'],
            'description.*' => ['nullable', 'string', 'max:1000'],
            'youtube_url' => ['required', 'string', 'max:255', fn (string $attribute, mixed $value, Closure $fail) => YouTube::isValid($value) ? null : $fail('সঠিক YouTube লিংক দিন (watch, youtu.be বা shorts)।')],
            'orientation' => ['nullable', Rule::in([LearningVideo::Vertical, LearningVideo::Horizontal])],
            'subject_id' => ['required', 'integer', Rule::exists('learning_topics', 'id')->whereNull('parent_id')],
            'topic_id' => ['nullable', 'integer', Rule::exists('learning_topics', 'id')->where('parent_id', $this->integer('subject_id'))],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'topic_id.exists' => 'টপিকটি বাছাই করা বিষয়ের ভেতরের হতে হবে।',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->boolean('is_featured')) {
                    return;
                }

                $video = $this->route('learning_video');
                $otherFeatured = LearningVideo::query()
                    ->where('is_featured', true)
                    ->when($video instanceof LearningVideo, fn ($query) => $query->whereKeyNot($video->id))
                    ->count();

                if ($otherFeatured >= LearningVideo::MAX_FEATURED) {
                    $validator->errors()->add('is_featured', 'সর্বোচ্চ '.LearningVideo::MAX_FEATURED.'টি ভিডিও ফিচার করা যায়। আগে অন্য একটির টিক তুলে দিন।');
                }
            },
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        $url = trim($this->validated('youtube_url'));

        return [
            ...$this->safe()->only(['subject_id', 'topic_id', 'is_featured', 'is_published']),
            'title' => TranslatableInput::text($this->validated('title')),
            'description' => TranslatableInput::text($this->validated('description')),
            'youtube_url' => $url,
            'orientation' => $this->validated('orientation') ?: LearningVideo::orientationFor($url),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
