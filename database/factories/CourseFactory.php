<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\Course;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'program_id' => Program::factory(),
            'title' => ['bn' => "কোর্স {$title}", 'en' => Str::title($title)],
            'summary' => ['bn' => 'লাইভ ক্লাসে ধাপে ধাপে শেখা।', 'en' => 'Step-by-step learning in live classes.'],
            'class_from' => 3,
            'class_to' => 5,
            'live_class_count' => 48,
            'class_duration_minutes' => 60,
            'duration_months' => 6,
            'learning_outcomes' => ['bn' => ['সমস্যা সমাধান'], 'en' => ['Problem solving']],
            'explainer_video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'status' => PublishStatus::Published,
            'published_at' => now(),
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PublishStatus::Draft, 'published_at' => null]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }
}
