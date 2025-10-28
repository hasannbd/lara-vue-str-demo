<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * @param RoleService $roleService
     * @param PermissionService $permissionService
     */
    public function __construct(private readonly RoleService $roleService, private readonly PermissionService $permissionService)
    {
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $search = request()->query('search');
        $roles = $this->roleService->getpaginatedRoles($search, 5);
        return Inertia::render('admin/role/Index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $permissions = $this->permissionService->getAllPermissions();
        return Inertia::render('admin/role/Create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        try {
            $this->roleService->create($request);
            return redirect()->route('admin.role.index')->with('success', 'Role created successfully.');
        } catch (\Throwable $exception) {
            return redirect()->route('admin.role.index')->with('error', app()->environment('local') ? $exception->getMessage() : 'Role could not be created.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response|RedirectResponse
    {
        $role = $this->roleService->getRole($id);
        if (!$role) {
            return redirect()->route('admin.role.index')->with('error', 'Role not found.');
        }
        return Inertia::render('admin/role/Show', ['role' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response|RedirectResponse
    {
        $role = $this->roleService->getRole($id);
        if (!$role) {
            return redirect()->route('admin.role.index')->with('error', 'Role not found.');
        }
        $permissions = $this->permissionService->getAllPermissions();
        return Inertia::render('admin/role/Edit', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id): RedirectResponse
    {
        try {
            $role = $this->roleService->getRole($id);
            if (!$role) {
                return redirect()->route('admin.role.index')->with('error', 'Role not found.');
            }
            $this->roleService->update($role, $request);
            return redirect()->route('admin.role.index')->with('success', 'Role updated successfully.');
        } catch (\Throwable $exception) {
            return redirect()->route('admin.role.index')->with('error', app()->environment('local') ? $exception->getMessage() : 'Role could not be updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id): RedirectResponse
    {
        try {
            $role = $this->roleService->getRole($id);
            if (!$role) {
                return redirect()->route('admin.role.index')->with('error', 'Role not found.');
            } else if ($role->name === config('permission.default_role_name')) {
                return redirect()->route('admin.role.index')->with('error', 'Cannot delete default role');
            }
            $this->roleService->deleteRole($role);
            return redirect()->route('admin.role.index')->with('success', 'Role deleted successfully.');
        } catch (\Throwable $exception) {
            return redirect()->route('admin.role.index')->with('error', app()->environment('local') ? $exception->getMessage() : 'Role could not be deleted.');
        }
    }
}
