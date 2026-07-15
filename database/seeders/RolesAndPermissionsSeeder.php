<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    private const array RESOURCES = ['user', 'role', 'permission', 'category', 'tag', 'video'];

    private const array ACTIONS = ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'];

    public function run(): void
    {
        $permissions = collect(self::RESOURCES)
            ->crossJoin(self::ACTIONS)
            ->map(fn (array $pair) => "{$pair[1]}_{$pair[0]}")
            ->push('access_admin_panel');

        $permissions->each(
            fn (string $name) => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
        );

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($permissions->all());

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions->reject(fn (string $name) => $name === 'delete_any_role')->all());

        User::where('email', 'admin@admin.com')->first()?->syncRoles([$superAdmin]);
    }
}
