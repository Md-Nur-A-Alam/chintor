<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use App\Models\PaymentAccount;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePaymentAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
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
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'account_type' => ['required', Rule::in(array_keys(PaymentAccount::ACCOUNT_TYPES))],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'bank_name' => ['nullable', 'required_if:account_type,bank', 'string', 'max:100'],
            'branch' => ['nullable', 'string', 'max:100'],
            'instructions.*' => ['nullable', 'string', 'max:300'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            ...$this->safe()->except(['instructions', 'sort_order']),
            'instructions' => TranslatableInput::text($this->validated('instructions')),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
