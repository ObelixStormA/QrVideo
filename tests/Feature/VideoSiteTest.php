<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_lists_published_videos(): void
    {
        $video = Video::factory()->create(['status' => 'published', 'published_at' => now()]);
        Video::factory()->create(['status' => 'draft', 'published_at' => null]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee($video->title);
    }

    public function test_home_page_can_filter_by_category(): void
    {
        $music = Category::factory()->create(['name' => 'Musiqa', 'slug' => 'music']);
        $tech = Category::factory()->create(['name' => 'Texnologiya', 'slug' => 'tech']);

        $musicVideo = Video::factory()->create(['category_id' => $music->id, 'title' => 'Music Video']);
        $techVideo = Video::factory()->create(['category_id' => $tech->id, 'title' => 'Tech Video']);

        $response = $this->get(route('home', ['category' => 'music']));

        $response->assertOk();
        $response->assertSee($musicVideo->title);
        $response->assertDontSee($techVideo->title);
    }

    public function test_home_page_can_search_videos(): void
    {
        $match = Video::factory()->create(['title' => 'Laravel bilan API yaratish']);
        $other = Video::factory()->create(['title' => 'Mutlaqo boshqa mavzu']);

        $response = $this->get(route('home', ['search' => 'Laravel']));

        $response->assertOk();
        $response->assertSee($match->title);
        $response->assertDontSee($other->title);
    }

    public function test_watch_page_shows_published_video_and_increments_views(): void
    {
        $video = Video::factory()->create(['views_count' => 5]);

        $response = $this->get(route('videos.show', $video));

        $response->assertOk();
        $response->assertSee($video->title);
        $this->assertSame(6, $video->fresh()->views_count);
    }

    public function test_watch_page_returns_404_for_draft_video(): void
    {
        $video = Video::factory()->create(['status' => 'draft', 'published_at' => null]);

        $this->get(route('videos.show', $video))->assertNotFound();
    }
}
