<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\PaymentAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentAccount>
 */
class PaymentAccountFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'method' => PaymentMethod::Bkash,
            'account_type' => 'personal',
            'account_name' => 'Chintor',
            'account_number' => '01'.fake()->numerify('#########'),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
