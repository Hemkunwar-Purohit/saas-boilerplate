<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TeamController extends Controller
{
    public function index()
    {
        $members      = User::with('roles')->latest()->get();
        $roles        = Role::all();
        $tenant       = tenant();
        $currentCount = $members->count();

        $plan  = $tenant->plan_id
            ? Plan::on('mysql')->find($tenant->plan_id)
            : Plan::on('mysql')->where('is_free', true)->first();

        $limit = $plan ? $plan->getFeatureLimit('users') : 10;

        return view('tenant.team.index', compact('members', 'roles', 'currentCount', 'limit'));
    }

    public function invite(Request $request)
    {
        $tenant = tenant();

        $plan = $tenant->plan_id
            ? Plan::on('mysql')->find($tenant->plan_id)
            : Plan::on('mysql')->where('is_free', true)->first();

        $currentCount = User::count();
        $limit        = $plan ? $plan->getFeatureLimit('users') : 10;

        if ($limit !== -1 && $currentCount >= $limit) {
            return back()->with('error', "User limit reached ({$limit}). Please upgrade your plan.");
        }

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role'  => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make(Str::random(12)),
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log("Invited {$user->name} as {$request->role}");

        return back()->with('success', "Invitation sent to {$user->email}!");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => ['required', 'exists:roles,name']]);

        if ($user->hasRole('owner') && auth()->id() !== $user->id) {
            return back()->with('error', "Cannot change the owner's role.");
        }

        $user->syncRoles([$request->role]);
        activity()->causedBy(auth()->user())->log("Changed {$user->name}'s role to {$request->role}");

        return back()->with('success', 'Role updated successfully.');
    }

    public function remove(User $user)
    {
        if ($user->hasRole('owner')) {
            return back()->with('error', 'Cannot remove the workspace owner.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        $name = $user->name;
        $user->delete();
        activity()->causedBy(auth()->user())->log("Removed {$name} from the team");

        return back()->with('success', 'Team member removed.');
    }
}