<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Listeners;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Stancl\Tenancy\Contracts\TenantWithDatabase::class,
            \App\Models\Tenant::class
        );
    }

    public function boot(): void
    {
        Event::listen(
            Events\TenantCreated::class,
            function (Events\TenantCreated $event) {
                $tenant = $event->tenant;
                $prefix = config('tenancy.database.prefix', 'saas_tenant_');
                $dbName = $prefix . $tenant->getTenantKey();

                // Step 1: Database banao
                DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // Step 2: Tenant connection set karo manually
                config([
                    'database.connections.tenant' => array_merge(
                        config('database.connections.mysql'),
                        ['database' => $dbName]
                    )
                ]);

                DB::purge('tenant');
                DB::reconnect('tenant');

                // Step 3: Tenant migrations chalao
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--force'    => true,
                    '--path'     => database_path('migrations/tenant'),
                    '--realpath' => true,
                ]);

                echo Artisan::output();

                // Step 4: Seeder chalao
                try {
                    // Temporarily default connection change karo
                    DB::setDefaultConnection('tenant');

                    Artisan::call('db:seed', [
                        '--class' => 'Database\\Seeders\\TenantDatabaseSeeder',
                        '--force' => true,
                    ]);

                    echo Artisan::output();
                } catch (\Exception $e) {
                    echo "Seeder warning: " . $e->getMessage() . "\n";
                } finally {
                    // Central pe wapas jao
                    DB::setDefaultConnection(config('tenancy.database.central_connection', 'mysql'));
                }
            }
        );

        Event::listen(
            Events\TenancyInitialized::class,
            Listeners\BootstrapTenancy::class
        );

        Event::listen(
            Events\TenancyEnded::class,
            Listeners\RevertToCentralContext::class
        );

        Event::listen(
            Events\TenantDeleted::class,
            function (Events\TenantDeleted $event) {
                $prefix = config('tenancy.database.prefix', 'saas_tenant_');
                $dbName = $prefix . $event->tenant->getTenantKey();
                DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
            }
        );
    }
}
