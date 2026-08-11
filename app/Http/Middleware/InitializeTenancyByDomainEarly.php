<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InitializeTenancyByDomainEarly
{
    public function handle(Request $request, Closure $next)
    {
        $host         = $request->getHost();
        $centralHosts = ['hemastudio.online', 'www.hemastudio.online'];

        if (in_array($host, $centralHosts)) {
            return $next($request);
        }

        // Tenant domain — initialize karo
        if (!tenancy()->initialized) {
            try {
                $domain = \Stancl\Tenancy\Database\Models\Domain
                    ::where('domain', $host)->first();

                if ($domain) {
                    tenancy()->initialize($domain->tenant);
                }
            } catch (\Exception $e) {
                // tenant nahi mila
            }
        }

        return $next($request);
    }
}
