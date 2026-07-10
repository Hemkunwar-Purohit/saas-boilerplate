<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // tenant.php PEHLE — /login tenant.login register hogi
            Route::middleware('web')
                ->group(base_path('routes/tenant.php'));

            // web.php BAAD MEIN — central.login register hogi (alag naam)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->redirectGuestsTo('/login');

        $middleware->prependToGroup('web',
            \App\Http\Middleware\InitializeTenancyByDomainEarly::class
        );

        $middleware->alias([
            'auth.admin'    => \App\Http\Middleware\AuthenticateAdmin::class,
            'role'          => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'    => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_perm'  => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tenant.active' => \App\Http\Middleware\EnsureTenantIsActive::class,
            'check.feature' => \App\Http\Middleware\CheckFeature::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'razorpay/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
