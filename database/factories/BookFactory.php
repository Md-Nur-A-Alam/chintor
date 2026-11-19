<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'title' => ['bn' => "বই {$title}", 'en' => Str::title($title)],
            'description' => ['bn' => 'অনুশীলনসহ সম্পূর্ণ গাইড।', 'en' => 'A complete guide with practice.'],
            'pages' => 240,
            'language' => 'bn',
            'regular_price' => 450,
            'discounted_price' => 380,
            'stock_quantity' => 50,
            'status' => PublishStatus::Published,
            'published_at' => now(),
            'sort_order' => 0,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => ['stock_quantity' => 0]);
    }
}
