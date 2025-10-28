<?php

namespace App\Services;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create a new user.
     *
     * @param UserRequest $request
     * @return User
     */
    public function create(UserRequest $request): User
    {
        $user = User::create($request->except('roles'));
        $roles = $request->only('roles');
        if (!empty($roles)) {
            $user->assignRole($roles);
        }
        return $user;
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Get a paginated list of users.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();
        $sort = request()->query('sort_by', 'created_at');
        $direction = request()->query('sort_order', 'asc');
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
        }
        if (isset($filters['role'])) {
            $role = $filters['role'];
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }
        $query->orderBy($sort, $direction);
        return $query->with('roles')->paginate($perPage);
    }

    /**
     * Get user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function get(int $id): ?User
    {
        return User::query()->with('roles')->find($id);
    }

    public function countUsers(): int
    {
        return User::query()->count();
    }

    /**
     * Update user information.
     *
     * @param User $user
     * @param UserRequest $request
     * @return User
     */
    public function update(User $user, UserRequest $request): User
    {
        $user->update($request->except('roles'));
        $roles = $request->only('roles');
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
        return $user;
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        $user->roles()->detach();
        return $user->delete();
    }
}
