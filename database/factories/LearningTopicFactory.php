<?php

namespace Database\Factories;

use App\Models\LearningTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningTopic>
 */
class LearningTopicFactory extends Factory
{
    /**
     * A subject (no parent). Use topicOf() for a topic inside a subject.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'parent_id' => null,
            'slug' => $name.'-'.fake()->unique()->numberBetween(1, 99999),
            'name' => ['bn' => $name, 'en' => $name],
            'sort_order' => 0,
        ];
    }

    public function topicOf(LearningTopic $subject): static
    {
        return $this->state(['parent_id' => $subject->id]);
    }
}
