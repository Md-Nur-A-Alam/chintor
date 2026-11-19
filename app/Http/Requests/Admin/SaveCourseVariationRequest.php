<?php

namespace App\Http\Requests\Admin;

use App\Models\CourseVariation;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A pricing option of a course (requirements §3.3): main price required,
 * discounted price optional but always lower.
 *
 * Field names are prefixed (plan_…) where they would clash with the course
 * form on the same page.
 */
class SaveCourseVariationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('catalog.manage') ?? false;
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
            'label.bn' => ['required', 'string', 'max:60'],
            'label.en' => ['nullable', 'string', 'max:60'],
            'badge.*' => ['nullable', 'string', 'max:30'],
            'plan_duration_months' => ['nullable', 'integer', 'min:0', 'max:60'],
            'billing_type' => ['required', Rule::in([CourseVariation::BillingOneTime, CourseVariation::BillingMonthly])],
            'regular_price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'discounted_price' => ['nullable', 'integer', 'min:0', 'lt:regular_price'],
            'is_active' => ['boolean'],
            'plan_sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'discounted_price.lt' => 'ডিসকাউন্টেড প্রাইস অবশ্যই মেইন প্রাইসের চেয়ে কম হতে হবে।',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            'label' => TranslatableInput::text($this->validated('label')),
            'badge' => TranslatableInput::text($this->validated('badge')),
            'duration_months' => $this->validated('plan_duration_months'),
            'billing_type' => $this->validated('billing_type'),
            'regular_price' => $this->validated('regular_price'),
            'discounted_price' => $this->validated('discounted_price'),
            'is_active' => $this->validated('is_active'),
            'sort_order' => (int) $this->validated('plan_sort_order'),
        ];
    }
}
