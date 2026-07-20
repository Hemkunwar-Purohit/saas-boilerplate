<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * GET /login — check Tenant or Central and show right view
     */
    public function showLoginForm()
    {
        // Tenant is in contex?
        if (tenancy()->initialized) {
            return view('auth.tenant-login', ['tenant' => tenant()]);
        }
        return view('auth.login');
    }

    /**
     * POST /login — handle Tenant or Central login 
     */
    public function handleLogin(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tenant login
        if (tenancy()->initialized) {
            if (!Auth::guard('web')->attempt(
                $request->only('email', 'password'),
                $request->boolean('remember')
            )) {
                throw ValidationException::withMessages([
                    'email' => 'These credentials do not match our records.',
                ]);
            }

            if (!auth()->user()->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated.',
                ]);
            }

            auth()->user()->updateLastLogin();
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        // Central admin login
        if (!Auth::guard('admin')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();
        return redirect('/admin/dashboard');
    }

    /**
     * POST /logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
