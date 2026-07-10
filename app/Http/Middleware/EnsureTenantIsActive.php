<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureTenantIsActive Middleware
 * Agar super admin ne tenant suspend kiya ho toh access block karo.
 */
class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant && !$tenant->is_active) {
            auth()->logout();

            return redirect()->route('tenant.login')
                ->with('error', 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
