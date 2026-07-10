<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SaasInstall extends Command
{
    protected $signature   = 'saas:install';
    protected $description = 'Interactive setup wizard for SaaS Boilerplate';

    public function handle(): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════╗');
        $this->line('║     SaaS Boilerplate — Install Wizard    ║');
        $this->line('╚══════════════════════════════════════════╝');
        $this->newLine();

        // Step 1: DB connection check
        $this->info('Step 1/5: Checking database connection...');
        try {
            DB::connection()->getPdo();
            $this->line('  ✅ Database connected!');
        } catch (\Exception $e) {
            $this->error('  ❌ Cannot connect to database. Check your .env file.');
            return;
        }

        // Step 2: Migrations
        $this->newLine();
        $this->info('Step 2/5: Running migrations...');
        Artisan::call('tenancy:install', [], $this->output);
        Artisan::call('migrate', ['--path' => 'database/migrations/central', '--force' => true], $this->output);
        $this->line('  ✅ Migrations complete!');

        // Step 3: Seeds
        $this->newLine();
        $this->info('Step 3/5: Seeding plans (Free, Basic, Pro)...');
        Artisan::call('db:seed', ['--class' => 'PlanSeeder', '--force' => true], $this->output);
        $this->line('  ✅ Plans seeded!');

        // Step 4: Super admin
        $this->newLine();
        $this->info('Step 4/5: Creating super admin...');
        $name     = $this->ask('Admin name', 'Super Admin');
        $email    = $this->ask('Admin email', 'admin@example.com');
        $password = $this->secret('Admin password (min 8 chars)');

        Admin::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt($password), 'is_super_admin' => true]
        );
        $this->line("  ✅ Admin created: {$email}");

        // Step 5: Storage link
        $this->newLine();
        $this->info('Step 5/5: Creating storage link...');
        Artisan::call('storage:link', [], $this->output);
        $this->line('  ✅ Storage linked!');

        // Done!
        $this->newLine();
        $this->line('╔══════════════════════════════════════════╗');
        $this->line('║        ✅ Installation Complete!         ║');
        $this->line('╠══════════════════════════════════════════╣');
        $this->line('║  Admin Panel : http://127.0.0.1:8000    ║');
        $this->line("║  Email       : {$email}");
        $this->line('║                                          ║');
        $this->line('║  Run: php artisan serve                  ║');
        $this->line('╚══════════════════════════════════════════╝');
        $this->newLine();
    }
}
