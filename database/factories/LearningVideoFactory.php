<?php

namespace Database\Factories;

use App\Models\LearningTopic;
use App\Models\LearningVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningVideo>
 */
class LearningVideoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ['bn' => 'ত্রিভুজের কোণের সমষ্টি', 'en' => 'Angles of a triangle'],
            'description' => null,
            'youtube_url' => 'https://www.youtube.com/shorts/aqz-KE-bpKQ',
            'orientation' => LearningVideo::Vertical,
            'subject_id' => LearningTopic::factory(),
            'topic_id' => null,
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
