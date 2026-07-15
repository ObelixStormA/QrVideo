<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacResourceTest extends TestCase
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

    public function test_super_admin_can_view_user_resource_pages(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(UserResource::getUrl('index'))->assertOk();
        $this->get(UserResource::getUrl('create'))->assertOk();
    }

    public function test_super_admin_can_view_role_resource_pages(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(RoleResource::getUrl('index'))->assertOk();
        $this->get(RoleResource::getUrl('create'))->assertOk();
    }

    public function test_super_admin_can_view_permission_resource_pages(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(PermissionResource::getUrl('index'))->assertOk();
        $this->get(PermissionResource::getUrl('create'))->assertOk();
    }

    public function test_user_without_permission_cannot_access_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(UserResource::getUrl('index'))
            ->assertForbidden();
    }
}
