<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Support\TranslatableInput;
use App\Support\YouTube;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveCourseRequest extends FormRequest
{
    use HandlesImageInput;

    public function authorize(): bool
    {
        return $this->user()?->can('catalog.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('title.en', '')),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'program_id' => ['required', Rule::exists('programs', 'id')->whereNull('deleted_at')],
            'slug' => ['required', 'alpha_dash', 'max:120', Rule::unique('courses', 'slug')->ignore($this->route('course'))],
            'title.bn' => ['required', 'string', 'max:200'],
            'title.en' => ['nullable', 'string', 'max:200'],
            'subtitle.*' => ['nullable', 'string', 'max:255'],
            'summary.*' => ['nullable', 'string', 'max:500'],
            'description.*' => ['nullable', 'string', 'max:5000'],
            'learning_outcomes.*' => ['nullable', 'string', 'max:3000'],
            'highlights.*' => ['nullable', 'string', 'max:3000'],
            'syllabus.*' => ['nullable', 'string', 'max:5000'],
            'seo_title.*' => ['nullable', 'string', 'max:70'],
            'seo_description.*' => ['nullable', 'string', 'max:170'],
            'class_from' => ['nullable', 'integer', 'between:0,12'],
            'class_to' => ['nullable', 'integer', 'between:0,12', 'gte:class_from'],
            'age_from' => ['nullable', 'integer', 'between:3,25'],
            'age_to' => ['nullable', 'integer', 'between:3,25'],
            'live_class_count' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'class_duration_minutes' => ['nullable', 'integer', 'min:0', 'max:600'],
            'duration_months' => ['nullable', 'integer', 'min:0', 'max:60'],
            'explainer_video_url' => ['nullable', 'url', 'max:255'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['integer', Rule::exists('teachers', 'id')],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...self::imageRules('thumbnail'),
        ];
    }

    /**
     * Requirements §17: a course cannot be published without an explainer video.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('explainer_video_url') && ! YouTube::isValid($this->input('explainer_video_url'))) {
                    $validator->errors()->add('explainer_video_url', 'সঠিক YouTube লিংক দিন (Unlisted ভিডিও হলেও চলবে)।');
                }

                if ($this->input('status') === PublishStatus::Published->value && ! YouTube::isValid($this->input('explainer_video_url'))) {
                    $validator->errors()->add('status', 'এক্সপ্লেইনিং ভিডিও ছাড়া কোর্স প্রকাশ করা যাবে না। আগে ভিডিও লিংক দিন।');
                }
            },
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        $status = PublishStatus::from($this->validated('status'));

        return [
            'program_id' => $this->validated('program_id'),
            'slug' => $this->validated('slug'),
            'title' => TranslatableInput::text($this->validated('title')),
            'subtitle' => TranslatableInput::text($this->validated('subtitle')),
            'summary' => TranslatableInput::text($this->validated('summary')),
            'description' => TranslatableInput::text($this->validated('description')),
            'learning_outcomes' => TranslatableInput::lines($this->validated('learning_outcomes')),
            'highlights' => TranslatableInput::lines($this->validated('highlights')),
            'syllabus' => TranslatableInput::lines($this->validated('syllabus')),
            'seo_title' => TranslatableInput::text($this->validated('seo_title')),
            'seo_description' => TranslatableInput::text($this->validated('seo_description')),
            'class_from' => $this->validated('class_from'),
            'class_to' => $this->validated('class_to'),
            'age_from' => $this->validated('age_from'),
            'age_to' => $this->validated('age_to'),
            'live_class_count' => $this->validated('live_class_count'),
            'class_duration_minutes' => $this->validated('class_duration_minutes'),
            'duration_months' => $this->validated('duration_months'),
            'explainer_video_url' => $this->validated('explainer_video_url'),
            'status' => $status,
            'is_featured' => $this->validated('is_featured'),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
