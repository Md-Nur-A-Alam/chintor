<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const MAX_ATTEMPTS = 5;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'phone' => 'ফোন নম্বর',
            'password' => 'পাসওয়ার্ড',
        ];
    }

    /**
     * Authenticate an active staff member by phone and password.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $phone = PhoneNumber::normalizeBangladeshMobile($this->string('phone')->toString());

        $credentials = [
            'phone' => $phone,
            'password' => $this->string('password')->toString(),
            'type' => User::TypeStaff,
            'is_active' => true,
        ];

        if ($phone === null || ! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'phone' => 'ফোন নম্বর বা পাসওয়ার্ড সঠিক নয়।',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'phone' => "অনেকবার ভুল চেষ্টা হয়েছে। {$seconds} সেকেন্ড পরে আবার চেষ্টা করুন।",
        ]);
    }

    /**
     * Keyed by phone and IP, so one office network cannot lock out every
     * account and one account cannot be brute-forced from many attempts.
     */
    private function throttleKey(): string
    {
        $phone = PhoneNumber::normalizeBangladeshMobile($this->string('phone')->toString())
            ?? $this->string('phone')->lower()->toString();

        return 'admin-login|'.$phone.'|'.$this->ip();
    }
}
