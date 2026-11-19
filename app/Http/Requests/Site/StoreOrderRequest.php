<?php

namespace App\Http\Requests\Site;

use App\Enums\PaymentMethod;
use App\Models\Book;
use App\Support\Districts;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public const MAX_QUANTITY = 20;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => PhoneNumber::normalize($this->string('phone')->toString()),
            'alt_phone' => PhoneNumber::normalize($this->string('alt_phone')->toString()),
            'sender_number' => PhoneNumber::normalize($this->string('sender_number')->toString()),
            'transaction_id' => $this->string('transaction_id')->trim()->upper()->toString() ?: null,
        ]);
    }

    /**
     * Books ship only after payment, so the transaction ID is required.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Book $book */
        $book = $this->route('book');

        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:'.min(self::MAX_QUANTITY, max(1, $book->stock_quantity))],
            'customer_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+8801[3-9]\d{8}$/'],
            'alt_phone' => ['nullable', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'district' => ['required', Rule::in(Districts::keys())],
            'upazila' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'sender_number' => ['required', 'string', 'max:20'],
            'transaction_id' => [
                'required', 'string', 'max:50', 'alpha_num',
                Rule::unique('payments', 'transaction_id')->where('method', $this->input('payment_method')),
            ],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => __('validation.custom.phone.bangladeshi'),
            'transaction_id.unique' => __('validation.custom.transaction_id.unique'),
        ];
    }
}
