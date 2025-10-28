<?php

namespace Database\Seeders;

use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @param UserService $userService
     * @param RoleService $roleService
     */
    public function __construct(private readonly UserService $userService, private readonly RoleService $roleService)
    {
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = $this->userService->countUsers();
        if (!$user) {
            $this->command->info('Creating user...');
            $user = $this->userService->createUser([
                'name' => config('permission.default_user.name', 'Sirajul Islam'),
                'email' => config('permission.default_user.email', 'sislam98@bou.ac.bd'),
                'password' => config('permission.default_user.password', 'Welcome'),//Hash::make(config('permission.default_user.password')),
            ]);
            $this->command->info('User created successfully');
            $this->command->info('Creating role...');
            $role = $this->roleService->createRole(config('permission.default_role_name'));
            $user->assignRole($role);
            $this->command->info('Role created successfully');
        }
    }
}
