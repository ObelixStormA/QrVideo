<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'thumbnail' => 'https://picsum.photos/seed/'.fake()->uuid().'/480/270',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'duration_seconds' => fake()->numberBetween(60, 3600),
            'views_count' => fake()->numberBetween(0, 1_000_000),
            'author_name' => fake()->userName(),
            'is_live' => false,
            'status' => 'published',
            'published_at' => now(),
        ];
    }
}
