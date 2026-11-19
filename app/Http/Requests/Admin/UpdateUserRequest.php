<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends StoreUserRequest
{
    public function authorize(): bool
    {
        $actor = $this->user();
        $target = $this->targetUser();

        if (! $actor?->can('users.manage')) {
            return false;
        }

        // Only a super admin may edit another super admin.
        return ! $target->isSuperAdmin() || $actor->isSuperAdmin();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            ...$this->profileRules($this->targetUser()),
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            ...parent::after(),
            function (Validator $validator): void {
                $target = $this->targetUser();
                $editingSelf = $target->is($this->user());

                if ($editingSelf && ! $this->boolean('is_active')) {
                    $validator->errors()->add('is_active', 'নিজের অ্যাকাউন্ট নিষ্ক্রিয় করা যাবে না।');
                }

                if ($target->isSuperAdmin() && ! $this->assignsSuperAdmin() && $this->isLastSuperAdmin($target)) {
                    $validator->errors()->add('roles', 'এটিই শেষ সুপার অ্যাডমিন অ্যাকাউন্ট; রোলটি সরানো যাবে না।');
                }
            },
        ];
    }

    private function targetUser(): User
    {
        return $this->route('user');
    }

    private function isLastSuperAdmin(User $user): bool
    {
        return ! User::whereKeyNot($user->getKey())
            ->whereHas('roles', fn ($query) => $query->where('name', Role::SuperAdmin))
            ->exists();
    }
}
