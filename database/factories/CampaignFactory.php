<?php

namespace Database\Factories;

use App\Enums\CampaignChannel;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ['bn' => 'শীতকালীন ভর্তি অফার', 'en' => 'Winter admission offer'],
            'summary' => ['bn' => 'নির্দিষ্ট ব্যাচে বিশেষ ছাড়।', 'en' => 'A special discount on selected batches.'],
            'image_url' => 'https://example.com/campaign.jpg',
            'link_url' => '/start',
            'button_label' => ['bn' => 'বিস্তারিত', 'en' => 'Details'],
            'channel' => CampaignChannel::Website,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addWeek(),
            'show_in_hero' => true,
            'show_in_bar' => false,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inBar(): static
    {
        return $this->state(['show_in_bar' => true]);
    }

    public function ended(): static
    {
        return $this->state(['starts_at' => now()->subMonth(), 'ends_at' => now()->subDay()]);
    }
}
