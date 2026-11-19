<?php

namespace App\Http\Requests\Admin;

use App\Support\Districts;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveDeliveryZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'zone_name.bn' => ['required', 'string', 'max:60'],
            'zone_name.en' => ['nullable', 'string', 'max:60'],
            'charge' => ['required', 'integer', 'min:0', 'max:10000'],
            'districts' => ['nullable', 'array'],
            'districts.*' => [Rule::in(Districts::keys())],
            'estimated_days' => ['nullable', 'string', 'max:30'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
            'zone_sort_order' => ['nullable', 'integer', 'min:0', 'max:99'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            'name' => TranslatableInput::text($this->validated('zone_name')),
            'charge' => $this->validated('charge'),
            'districts' => array_values($this->validated('districts') ?? []),
            'estimated_days' => $this->validated('estimated_days'),
            'is_default' => $this->validated('is_default'),
            'is_active' => $this->validated('is_active'),
            'sort_order' => (int) $this->validated('zone_sort_order'),
        ];
    }
}
