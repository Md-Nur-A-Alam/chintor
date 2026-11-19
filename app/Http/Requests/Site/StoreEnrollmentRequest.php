<?php

namespace App\Http\Requests\Site;

use App\Enums\PaymentMethod;
use App\Models\Course;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => PhoneNumber::normalize($this->string('phone')->toString()),
            'sender_number' => PhoneNumber::normalize($this->string('sender_number')->toString()),
            'transaction_id' => $this->string('transaction_id')->trim()->upper()->toString() ?: null,
        ]);
    }

    /**
     * Payment details are optional: a family may pay later after a call.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Course $course */
        $course = $this->route('course');

        return [
            'course_variation_id' => ['required', Rule::exists('course_variations', 'id')->where('course_id', $course->id)->where('is_active', true)],
            'course_batch_id' => ['nullable', Rule::exists('course_batches', 'id')->where('course_id', $course->id)->whereNull('deleted_at')],
            'guardian_name' => ['required', 'string', 'max:100'],
            'student_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+\d{10,15}$/'],
            'class_level' => ['nullable', 'integer', 'between:0,12'],
            'payment_method' => ['nullable', 'required_with:transaction_id', Rule::enum(PaymentMethod::class)],
            'sender_number' => ['nullable', 'string', 'max:20'],
            'transaction_id' => [
                'nullable', 'string', 'max:50', 'alpha_num',
                Rule::unique('payments', 'transaction_id')->where('method', $this->input('payment_method')),
            ],
            'agree_refund' => ['accepted'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'transaction_id.unique' => __('validation.custom.transaction_id.unique'),
        ];
    }
}
