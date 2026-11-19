<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'name' => ['bn' => "প্রোগ্রাম {$name}", 'en' => Str::title($name).' Program'],
            'tagline' => ['bn' => 'ধাপে ধাপে শেখা', 'en' => 'Learn step by step'],
            'icon' => 'target',
            'accent' => 'teal',
            'status' => PublishStatus::Published,
            'sort_order' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PublishStatus::Draft]);
    }
}
