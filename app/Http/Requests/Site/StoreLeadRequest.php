<?php

namespace App\Http\Requests\Site;

use App\Enums\LeadType;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The "চলো একসাথে শিখি" wizard and the short call-back forms.
 */
class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => PhoneNumber::normalize($this->string('phone')->toString()),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'form' => ['required', Rule::in([LeadType::Wizard->value, LeadType::Callback->value, LeadType::Assessment->value, LeadType::Contact->value, LeadType::Waitlist->value])],
            'student_name' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+\d{10,15}$/'],
            'class_level' => ['nullable', 'integer', 'between:0,12'],
            'age' => ['nullable', 'integer', 'between:3,25'],
            'message' => ['nullable', 'string', 'max:1000'],
            'course_ids' => ['nullable', 'array', 'max:10'],
            'course_ids.*' => ['integer', Rule::exists('courses', 'id')],
            'program_ids' => ['nullable', 'array', 'max:10'],
            'program_ids.*' => ['integer', Rule::exists('programs', 'id')],
            'wants_assessment' => ['nullable', 'boolean'],
            'consent' => ['accepted'],
            // Honeypot: real people never fill this hidden field.
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'phone' => __('site.lead.phone'),
            'student_name' => __('site.lead.student_name'),
            'guardian_name' => __('site.lead.guardian_name'),
            'consent' => __('validation.attributes.consent'),
        ];
    }

    public function leadType(): LeadType
    {
        if ($this->boolean('wants_assessment')) {
            return LeadType::Assessment;
        }

        return LeadType::from($this->validated('form'));
    }

    /**
     * @return array{student_name: ?string, guardian_name: ?string, phone: string, class_level: ?int, age: ?int, message: ?string}
     */
    public function contactDetails(): array
    {
        return [
            'student_name' => $this->validated('student_name'),
            'guardian_name' => $this->validated('guardian_name'),
            'phone' => $this->validated('phone'),
            'class_level' => $this->validated('class_level'),
            'age' => $this->validated('age'),
            'message' => $this->validated('message'),
        ];
    }
}
