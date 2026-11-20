<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => 'exam-'.fake()->unique()->numberBetween(1, 999999),
            'title' => ['bn' => 'সাপ্তাহিক গণিত পরীক্ষা', 'en' => 'Weekly maths test'],
            'description' => null,
            'subject_id' => null,
            'class_level' => null,
            'duration_minutes' => 5,
            'question_count' => 2,
            'results_at' => null,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
