<?php

namespace Database\Factories;

use App\Enums\BatchStatus;
use App\Models\Course;
use App\Models\CourseBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseBatch>
 */
class CourseBatchFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'code' => 'B-'.fake()->unique()->numberBetween(1000, 99999),
            'name' => ['bn' => 'সন্ধ্যার ব্যাচ', 'en' => 'Evening batch'],
            'class_days' => ['sat', 'mon', 'wed'],
            'class_start_time' => '18:00',
            'class_end_time' => '19:00',
            'starts_on' => now()->addWeeks(2)->toDateString(),
            'enrollment_deadline' => now()->addWeeks(2)->toDateString(),
            'seat_limit' => 30,
            'seats_taken' => 0,
            'status' => BatchStatus::Enrolling,
        ];
    }
}
