<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    public function __construct(private readonly PermissionService $permissionService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $permissions = $this->permissionService->getPaginatedPermissions();
        return Inertia::render('admin/permission/Index', ['permissions' => $permissions]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response|RedirectResponse
    {
        $permission = $this->permissionService->get($id);
        if (!$permission) {
            return redirect()->route('admin.permission.index')->with('error', 'Permission not found.');
        }
        return Inertia::render('admin/permission/Show', ['permission' => $permission]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id): RedirectResponse
    {
        try {
            $permission = $this->permissionService->get($id);
            if (!$permission) {
                return redirect()->route('admin.permission.index')->with('error', 'Permission not found.');
            }
            $this->permissionService->delete($permission);
            return redirect()->route('admin.permission.index')->with('success', 'Permission deleted successfully.');
        } catch (\Throwable $exception) {
            return redirect()->route('admin.permission.index')->with('error', app()->environment('local') ? $exception->getMessage() : 'Permission could not be deleted.');
        }
    }
}
