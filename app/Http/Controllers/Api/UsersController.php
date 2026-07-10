<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * GET /api/users
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->role($request->role))
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => $users->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'avatar_url' => $u->avatar_url,
                'roles'      => $u->getRoleNames(),
                'is_active'  => $u->is_active,
                'last_login' => $u->last_login_at?->toISOString(),
                'created_at' => $u->created_at->toISOString(),
            ]),
            'meta' => [
                'total'        => $users->total(),
                'per_page'     => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/users/{id}
     */
    public function show(User $user)
    {
        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'avatar_url'  => $user->avatar_url,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'is_active'   => $user->is_active,
            'last_login'  => $user->last_login_at?->toISOString(),
            'created_at'  => $user->created_at->toISOString(),
        ]);
    }

    /**
     * PUT /api/users/{id}
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit users');

        $request->validate([
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
        ]);

        $user->update($request->only('name', 'email'));

        activity()->causedBy(auth()->user())->performedOn($user)->log('Updated user via API');

        return response()->json(['message' => 'User updated.', 'user' => $user->fresh()]);
    }

    /**
     * DELETE /api/users/{id}
     */
    public function destroy(User $user)
    {
        $this->authorize('delete users');

        if ($user->hasRole('owner')) {
            return response()->json(['error' => 'Cannot delete workspace owner.'], 403);
        }

        $user->delete();
        activity()->causedBy(auth()->user())->log("Deleted user {$user->email} via API");

        return response()->json(['message' => 'User deleted.']);
    }
}
