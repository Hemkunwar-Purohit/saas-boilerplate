<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * TenantDatabaseSeeder — Har naye tenant ke database mein chalti hai.
 * Default roles aur permissions set karta hai.
 */
class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cache clear karo (important for multi-tenant)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions define karo ────────────────────────────
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'invite users',

            // Billing
            'view billing',
            'manage billing',

            // Settings
            'view settings',
            'manage settings',

            // Reports
            'view reports',

            // API
            'manage api tokens',

            // Activity logs
            'view activity logs',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ── Roles create karo ──────────────────────────────────

        // Owner — sab kuch kar sakta hai
        $ownerRole = Role::findOrCreate('owner', 'web');
        $ownerRole->syncPermissions(Permission::all());

        // Admin — almost everything, except delete owner
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions([
            'view users', 'create users', 'edit users', 'invite users',
            'view billing',
            'view settings', 'manage settings',
            'view reports',
            'manage api tokens',
            'view activity logs',
        ]);

        // Member — basic access
        $memberRole = Role::findOrCreate('member', 'web');
        $memberRole->syncPermissions([
            'view users',
            'view reports',
        ]);

        $this->command->info('✅ Tenant roles & permissions seeded: owner, admin, member');
    }
}
