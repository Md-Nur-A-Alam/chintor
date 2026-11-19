<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'name' => ['bn' => $name, 'en' => $name],
            'designation' => ['bn' => 'সিনিয়র শিক্ষক', 'en' => 'Senior Teacher'],
            'summary' => ['bn' => 'গণিতে ১০ বছরের অভিজ্ঞতা।', 'en' => '10 years of teaching maths.'],
            'details_mode' => Teacher::DetailsRich,
            'details_rich' => ['bn' => 'বিস্তারিত পরিচিতি।', 'en' => 'Detailed profile.'],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
