<?php

namespace App\Services;

use App\Http\Requests\RoleRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @return Role|null
     */
    public function getRoleByName(string $name): ?Role
    {
        return Role::query()->where('name', $name)->first();
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return Role::query()->orderBy('name')->get();
    }

    /**
     * @param Role $role
     * @param $permissions
     * @return bool
     */
    public function roleHasPermissions(Role $role, $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission)) {
                return false;
            }
        }
        return true;
    }

    public function countRoles(): int
    {
        return Role::query()->count();
    }

    /**
     * @return Collection
     */
    public function getAllRoles(): Collection
    {
        $roles = Role::query()->get();
        // Add user count to each role.
        foreach ($roles as $role) {
            $userCount = $this->countUsersInRole($role);
            $role->setAttribute('user_count', $userCount);
        }
        return $roles;
    }

    /**
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedRoles(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Role::query();

        if ($search) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        return $query->paginate($perPage);
    }

    /**
     * @param RoleRequest $request
     * @return Role
     */
    public function create(RoleRequest $request): Role
    {
        $name = $request->validated()['name'];
        $permissions = $request->validated()['permissions'] ?? [];
        return $this->createRole($name, $permissions);
    }

    /**
     * @param int $id
     * @return Role|null
     */
    public function getRole(int $id): ?Role
    {
        return Role::query()->with('permissions')->find($id);
    }

    /**
     * @param Role $role
     * @param RoleRequest $request
     * @return Role
     */
    public function update(Role $role, RoleRequest $request): Role
    {
        $name = $request->validated()['name'];
        $permissions = $request->validated()['permissions'] ?? [];
        return $this->updateRole($role, $name, $permissions);
    }


    /**
     * @param string $name
     * @param array $permissions
     * @return Role
     */
    public function createRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name, 'guard_name' => config('auth.defaults.guard')]);
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }
        return $role;
    }

    /**
     * @param Role $role
     * @param string $name
     * @param array $permissions
     * @return Role
     */
    public function updateRole(Role $role, string $name, array $permissions = []): Role
    {
        $role->name = $name;
        $role->save();

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }
        return $role;
    }

    /**
     * @param Role $role
     * @return bool
     */
    public function deleteRole(Role $role): bool
    {
//        $role->syncPermissions([]);
        $role->users()->detach();
        $role->permissions()->detach();
        return $role->delete();
    }


    /**
     * @param Role|string $role
     * @return int
     */
    public function countUsersInRole(Role|string $role): int
    {
        if (is_string($role)) {
            $role = Role::query()->where('name', $role)->first();
            if (!$role) {
                return 0;
            }
        }

        return $role->users->count();
    }

}
