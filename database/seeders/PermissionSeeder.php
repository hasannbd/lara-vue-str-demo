<?php

namespace Database\Seeders;

use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * @param PermissionService $permissionService
     * @param RoleService $rolesService
     */
    public function __construct(private readonly PermissionService $permissionService, private readonly RoleService $rolesService)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        $this->command->info('Creating permissions...');
        $this->permissionService->createStaticPermissions();
        $this->command->info('Permissions created successfully!');

        $defaultRoleName = config('permission.default_role_name', 'Super Admin');
        $defaultRole = $this->rolesService->getRoleByName($defaultRoleName);
        if (!$defaultRole) {
            $this->command->info('Creating default role...');
            $defaultRole = $this->rolesService->createRole($defaultRoleName);
            $this->command->info('Default role created successfully!');
        }
        $permissions = $this->permissionService->getAllStaticPermissions();
        if (!$this->rolesService->roleHasPermissions($defaultRole, $permissions)) {
            $this->command->info('Syncing role permissions...');
            $defaultRole->syncPermissions($permissions);
            $this->command->info('Synced role permissions successfully!');
        }
    }
}
