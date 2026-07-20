<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Returns API token for tenant user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated.',
            ]);
        }

        // revoke old tokens
        $user->tokens()->where('name', $request->device_name ?? 'api')->delete();

        $token = $user->createToken($request->device_name ?? 'api');

        activity()->causedBy($user)->log('API login');

        return response()->json([
            'token'      => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user'       => [
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

    /**
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * GET /api/me
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'avatar_url' => $user->avatar_url,
            'roles'      => $user->getRoleNames(),
            'permissions'=> $user->getAllPermissions()->pluck('name'),
            'tenant'     => [
                'id'   => tenant()->id,
                'name' => tenant()->name,
                'plan' => tenant()->plan?->name,
            ],
            'created_at' => $user->created_at,
        ]);
    }
}
