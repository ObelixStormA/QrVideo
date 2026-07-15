<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Tags\TagResource;
use App\Filament\Resources\Videos\VideoResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentResourceTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsSuperAdmin(): User
    {
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        return $user;
    }

    public function test_super_admin_can_view_category_resource_pages(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(CategoryResource::getUrl('index'))->assertOk();
        $this->get(CategoryResource::getUrl('create'))->assertOk();
    }

    public function test_super_admin_can_view_tag_resource_pages(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(TagResource::getUrl('index'))->assertOk();
        $this->get(TagResource::getUrl('create'))->assertOk();
    }

    public function test_super_admin_can_view_video_resource_pages(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(VideoResource::getUrl('index'))->assertOk();
        $this->get(VideoResource::getUrl('create'))->assertOk();
    }
}
