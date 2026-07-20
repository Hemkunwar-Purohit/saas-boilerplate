<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showForm()
    {
        $plans = Plan::active()->get();
        return view('auth.register', compact('plans'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255'],
            'password'     => ['required', 'confirmed', Password::min(8)],
            'company_name' => ['required', 'string', 'max:255'],
            'subdomain'    => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-z0-9\-]+$/'],
            'terms'        => ['required', 'accepted'],
        ]);

        try {
            $tenantId = Str::slug($request->subdomain);
            $domain   = $tenantId . '.localhost';

            // Step 1: Make Tenant + domain 
            $tenant = Tenant::create([
                'id'        => $tenantId,
                'name'      => $request->company_name,
                'email'     => $request->email,
                'is_active' => true,
            ]);
            $tenant->domains()->create(['domain' => $domain]);

            // Step 2: switch Tenant in DB
            tenancy()->initialize($tenant);

            // Step 3: Make User — simple, no roles yet
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Step 4:  insert Role directly from SQL
            $role = DB::table('roles')
                ->where('name', 'owner')
                ->where('guard_name', 'web')
                ->first();

            if ($role) {
                DB::table('model_has_roles')->insert([
                    'role_id'    => $role->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id'   => $user->id,
                ]);
            }

            // Step 5: Login
            auth()->login($user);

            // Step 6: Tenancy end
            tenancy()->end();

            // Step 7: Redirect
            $port = (request()->getPort() != 80 && request()->getPort() != 443)
                ? ':' . request()->getPort()
                : '';

            return redirect("http://{$domain}{$port}/")
                ->with('success', 'Welcome! Account created successfully.');

        } catch (\Throwable $e) {
            //  end Tenancy if it initialized
            try { tenancy()->end(); } catch (\Throwable $ignored) {}

            return back()
                ->withInput()
                ->with('error', $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . basename($e->getFile()));
        }
    }
}
