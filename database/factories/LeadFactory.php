<?php

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => LeadType::Wizard,
            'status' => LeadStatus::New,
            'student_name' => fake()->firstName(),
            'guardian_name' => fake()->name(),
            'phone' => '+8801'.fake()->numerify('7########'),
            'class_level' => fake()->numberBetween(3, 10),
            'source' => LeadSource::Website,
            'locale' => 'bn',
            'consent_at' => now(),
        ];
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn (array $attributes) => ['assigned_to' => $user->id, 'assigned_at' => now()]);
    }
}
