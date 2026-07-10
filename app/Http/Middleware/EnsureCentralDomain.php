<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCentralDomain
{
    // Yeh paths tenant domain pe bhi allow honge
    // (tenant.php inhe handle karega)
    protected array $tenantPaths = [
        '/login',
        '/logout',
        '/dashboard',
        '/profile',
        '/team',
        '/activity',
        '/billing',
    ];

    public function handle(Request $request, Closure $next)
    {
        $host         = $request->getHost();
        $centralHosts = ['127.0.0.1', 'localhost'];

        // Central domain — sab allow
        if (in_array($host, $centralHosts)) {
            return $next($request);
        }

        // Tenant domain — sirf tenant paths allow karo
        $path = '/' . ltrim($request->path(), '/');

        foreach ($this->tenantPaths as $tenantPath) {
            if (str_starts_with($path, $tenantPath)) {
                return $next($request);
            }
        }

        // Baki sab block
        abort(404);
    }
}
