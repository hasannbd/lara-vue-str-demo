<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * @param UserService $userService
     * @param RoleService $roleService
     */
    public function __construct(private readonly UserService $userService, private readonly RoleService $roleService)
    {
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $filters = request()->all();
        $users = $this->userService->getPaginatedUsers($filters, 5);
        $roles = $this->roleService->getRoles();
        return Inertia::render('admin/user/Index', ['users' => $users, 'roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $roles = $this->roleService->getRoles();
        return Inertia::render('admin/user/Create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): Response|RedirectResponse
    {
        try {
            $this->userService->create($request);
            return redirect()->route('admin.user.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')->with('error', app()->environment('local') ? $e->getMessage() : 'User could not be created.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response|RedirectResponse
    {
        $user = $this->userService->get($id);
        if (!$user) {
            return redirect()->route('admin.user.index')->with('error', 'User not found.');
        }
        return Inertia::render('admin/user/Show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response|RedirectResponse
    {
        $user = $this->userService->get($id);
        if (!$user) {
            return redirect()->route('admin.user.index')->with('error', 'User not found.');
        }
        $roles = $this->roleService->getRoles();
        return Inertia::render('admin/user/Edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id): RedirectResponse
    {
        try {
            $user = $this->userService->get($id);
            if (!$user) {
                return redirect()->route('admin.user.index')->with('error', 'User not found.');
            }
            $this->userService->update($user, $request);
            return redirect()->route('admin.user.index')->with('success', 'User updated successfully.');
        } catch (\Throwable $exception) {
            return redirect()->route('admin.user.index')->with('error', app()->environment('local') ? $exception->getMessage() : 'User could not be updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id): RedirectResponse
    {
        try {
            $user = $this->userService->get($id);
            if (!$user) {
                return redirect()->route('admin.user.index')->with('error', 'User not found.');
            }
            $this->userService->delete($user);
            return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');
        } catch (\Throwable $exception) {
            return redirect()->route('admin.user.index')->with('error', app()->environment('local') ? $exception->getMessage() : 'User could not be deleted.');
        }
    }
}
