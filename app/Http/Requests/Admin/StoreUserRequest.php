<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => PhoneNumber::normalizeBangladeshMobile($this->string('phone')->toString()) ?? $this->input('phone'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            ...$this->profileRules(),
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'নাম',
            'phone' => 'ফোন নম্বর',
            'email' => 'ইমেইল',
            'office_id' => 'অফিস আইডি',
            'designation' => 'পদবি',
            'joined_on' => 'যোগদানের তারিখ',
            'date_of_birth' => 'জন্মতারিখ',
            'roles' => 'রোল',
            'password' => 'পাসওয়ার্ড',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'সঠিক বাংলাদেশি মোবাইল নম্বর দিন (01XXXXXXXXX)।',
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->assignsSuperAdmin() && ! $this->user()->isSuperAdmin()) {
                    $validator->errors()->add('roles', 'শুধু সুপার অ্যাডমিন অন্য কাউকে সুপার অ্যাডমিন করতে পারেন।');
                }
            },
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function profileRules(?User $ignoring = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^\+8801[3-9]\d{8}$/', Rule::unique('users', 'phone')->ignore($ignoring)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoring)],
            'office_id' => ['nullable', 'string', 'max:20', Rule::unique('users', 'office_id')->ignore($ignoring)],
            'designation' => ['nullable', 'string', 'max:255'],
            'joined_on' => ['nullable', 'date'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'is_active' => ['boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }

    protected function assignsSuperAdmin(): bool
    {
        $superAdminRoleId = Role::where('name', Role::SuperAdmin)->value('id');

        return in_array($superAdminRoleId, array_map('intval', (array) $this->input('roles', [])), true);
    }
}
