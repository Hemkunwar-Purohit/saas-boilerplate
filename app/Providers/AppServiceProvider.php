<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Central migrations
        $this->loadMigrationsFrom([
            database_path('migrations/central'),
        ]);

        // Session cookie alag karo — booting() nahi, seedha boot() mein
        try {
            $host         = request()->getHost();
            $centralHosts = ['127.0.0.1', 'localhost'];

            if (!in_array($host, $centralHosts) && str_contains($host, '.')) {
                $tenantId = explode('.', $host)[0];
                config(['session.cookie' => 'tenant_' . $tenantId . '_session']);
            } else {
                config(['session.cookie' => 'central_admin_session']);
            }
        } catch (\Exception $e) {
            // CLI commands mein request nahi hota — skip
        }
    }
}