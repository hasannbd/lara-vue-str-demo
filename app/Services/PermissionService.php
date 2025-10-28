<?php

namespace App\Services;


use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PermissionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get all permissions organized by groups
     */
    public function getAllStaticPermissions(): array
    {
        return [
            'user.create',
            'user.view',
            'user.update',
            'user.delete',
            'role.create',
            'role.view',
            'role.update',
            'role.delete',
            'permission.view',
            'permission.delete',
        ];
    }

    public function getAllPermissions(): Collection
    {
        return Permission::query()->orderBy('name')->get();
    }

    /**
     * Create all permissions from the definitions
     *
     * @return array Created permissions
     */
    public function createStaticPermissions(): array
    {
        $createdPermissions = [];
        $permissions = $this->getAllStaticPermissions();

        foreach ($permissions as $permission) {
            $permission = $this->findOrCreatePermission($permission);
            $createdPermissions[] = $permission;
        }

        return $createdPermissions;
    }

    /**
     * Find or create a permission
     */
    public function findOrCreatePermission(string $name): Model|Permission
    {
        return Permission::query()->firstOrCreate(
            ['name' => $name],
            [
                'name' => $name,
                'guard_name' => config('auth.defaults.guard'),
            ]
        );
    }

    /**
     * Get paginated permissions with role count
     */
    public function getPaginatedPermissions(): LengthAwarePaginator
    {
        $sort = request()->query('sort');
        $direction = request()->query('direction', 'asc');
        $perPage = request()->query('per_page', 10);

        return Permission::query()
            ->when(request()->query('search'), function ($query, $search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            })->when($sort, function ($query, $sort) use ($direction) {
                if ($sort == 'name')
                    $query->orderBy($sort, $direction);
            })->with('roles')->paginate($perPage);
    }

    /**
     * Create a new permission
     */
    public function create(string $name, string $groupName): \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Models\Permission
    {
        return Permission::create([
            'name' => $name,
            'group_name' => $groupName,
            'guard_name' => config('auth.defaults.guard'),
        ]);
    }

    /**
     * Get permission by ID
     */
    public function get(int $id): ?Permission
    {
        return Permission::query()->with('roles')->find($id);
    }

    /**
     * Update permission details
     */
    public function update(Permission $permission, string $name, string $groupName): Permission
    {
        $permission->update([
            'name' => $name,
            'group_name' => $groupName
        ]);
        return $permission;
    }

    /**
     * Delete a permission
     */
    public function delete(Permission $permission): bool
    {
        $permission->roles()->detach();
        $permission->users()->detach();
        return $permission->delete();
    }
}
