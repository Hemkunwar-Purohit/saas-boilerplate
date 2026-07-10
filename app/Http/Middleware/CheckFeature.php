<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckFeature Middleware
 * Usage: Route::middleware('check.feature:team_management')
 *
 * Plan ke features check karta hai.
 * Agar feature allowed nahi hai toh billing page pe redirect.
 */
class CheckFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenant();

        if (!$tenant || !$tenant->plan) {
            return redirect()->route('tenant.billing.index')
                ->with('warning', 'Please choose a plan to access this feature.');
        }

        if (!$tenant->plan->hasFeature($feature)) {
            return redirect()->route('tenant.billing.index')
                ->with('upgrade_required', true)
                ->with('required_feature', $feature);
        }

        return $next($request);
    }
}
