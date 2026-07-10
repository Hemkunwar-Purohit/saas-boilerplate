<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    // ── Auth ──────────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Account deactivated'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
            'tenant' => [
                'id'   => tenant()->id,
                'name' => tenant()->name,
                'plan' => tenant()->plan?->name,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'avatar_url' => $user->avatar_url,
            'roles'      => $user->getRoleNames(),
            'tenant'     => [
                'id'   => tenant()->id,
                'name' => tenant()->name,
                'plan' => tenant()->plan?->name,
            ],
        ]);
    }

    // ── Users ─────────────────────────────────────────────────

    public function users(Request $request): JsonResponse
    {
        $users = User::with('roles')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate(20);

        return response()->json($users);
    }

    // ── Tenant Info ───────────────────────────────────────────

    public function tenantInfo(): JsonResponse
    {
        $tenant = tenant();
        return response()->json([
            'id'       => $tenant->id,
            'name'     => $tenant->name,
            'plan'     => [
                'name'          => $tenant->plan?->name,
                'price_monthly' => $tenant->plan?->price_monthly,
                'features'      => $tenant->plan?->features,
            ],
            'is_active'      => $tenant->is_active,
            'trial_ends_at'  => $tenant->trial_ends_at,
            'on_trial'       => $tenant->onTrial(),
        ]);
    }
}
