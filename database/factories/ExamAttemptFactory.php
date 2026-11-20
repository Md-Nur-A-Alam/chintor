<?php

namespace Database\Factories;

use App\Models\ExamAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamAttempt>
 */
class ExamAttemptFactory extends Factory
{
    /**
     * A custom practice with no questions; tests set question_ids.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_id' => null,
            'student_name' => fake()->name(),
            'phone' => null,
            'settings' => [],
            'question_ids' => [],
            'answers' => null,
            'duration_minutes' => 5,
            'started_at' => now(),
            'deadline_at' => now()->addMinutes(5),
        ];
    }
}
