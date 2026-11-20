<?php

namespace Database\Factories;

use App\Models\LearningTopic;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Option index 1 ("৪") is correct.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => LearningTopic::factory(),
            'topic_id' => null,
            'class_level' => 5,
            'body' => '২ + ২ = ? ('.fake()->unique()->numberBetween(1, 999999).')',
            'image_url' => null,
            'options' => ['৩', '৪', '৫', '৬'],
            'correct_option' => 1,
            'explanation' => 'দুই আর দুই মিলে চার।',
            'explanation_image_url' => null,
            'explanation_pdf_url' => null,
            'is_active' => true,
        ];
    }
}
