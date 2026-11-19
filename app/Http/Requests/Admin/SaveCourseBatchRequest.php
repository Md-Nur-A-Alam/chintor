<?php

namespace App\Http\Requests\Admin;

use App\Enums\BatchStatus;
use App\Models\CourseBatch;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCourseBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('catalog.manage') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:30', Rule::unique('course_batches', 'code')->ignore($this->route('batch'))],
            'name.*' => ['nullable', 'string', 'max:60'],
            'class_days' => ['nullable', 'array'],
            'class_days.*' => [Rule::in(array_keys(CourseBatch::DAYS))],
            'class_start_time' => ['nullable', 'date_format:H:i'],
            'class_end_time' => ['nullable', 'date_format:H:i', 'after:class_start_time'],
            'orientation_at' => ['nullable', 'date'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'enrollment_deadline' => ['nullable', 'date'],
            'seat_limit' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'batch_status' => ['required', Rule::enum(BatchStatus::class)],
            'meeting_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            ...$this->safe()->except(['name', 'batch_status']),
            'status' => $this->validated('batch_status'),
            'name' => TranslatableInput::text($this->validated('name')),
            'class_days' => array_values($this->validated('class_days') ?? []),
        ];
    }
}
