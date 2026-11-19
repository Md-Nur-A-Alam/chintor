<?php

namespace Database\Factories;

use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryZone>
 */
class DeliveryZoneFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ['bn' => 'ঢাকার বাইরে', 'en' => 'Outside Dhaka'],
            'charge' => 120,
            'districts' => [],
            'estimated_days' => '3-5',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ];
    }

    public function insideDhaka(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => ['bn' => 'ঢাকার ভেতরে', 'en' => 'Inside Dhaka'],
            'charge' => 70,
            'districts' => ['Dhaka'],
            'estimated_days' => '1-2',
            'is_default' => false,
            'sort_order' => 0,
        ]);
    }
}
