<?php

namespace App\Http\Requests\Site;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Starting a ready-made Exam Arena exam: the student's name, and
 * optionally a guardian's phone (with consent) to get advice after.
 */
class StartExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $phone = $this->string('phone')->trim()->toString();

        $this->merge(['phone' => $phone === '' ? null : PhoneNumber::normalize($phone)]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'student_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'regex:/^\+\d{10,15}$/'],
            'consent' => [Rule::excludeIf(fn (): bool => blank($this->input('phone'))), 'accepted'],
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
            'student_name' => __('site.arena.student_name'),
            'phone' => __('site.arena.phone'),
            'consent' => __('validation.attributes.consent'),
        ];
    }

    /**
     * @return array{student_name: string, phone: ?string}
     */
    public function student(): array
    {
        return [
            'student_name' => $this->validated('student_name'),
            'phone' => $this->validated('phone'),
        ];
    }
}
