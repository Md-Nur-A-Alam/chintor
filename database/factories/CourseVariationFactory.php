<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseVariation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseVariation>
 */
class CourseVariationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'label' => ['bn' => 'ফুল কোর্স', 'en' => 'Full course'],
            'duration_months' => 6,
            'billing_type' => CourseVariation::BillingOneTime,
            'regular_price' => 6000,
            'discounted_price' => 4800,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
