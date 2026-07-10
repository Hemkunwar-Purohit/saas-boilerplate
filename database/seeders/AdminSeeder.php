<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@saasapp.com'],
            [
                'name'           => 'Super Admin',
                'password'       => bcrypt('password123'),
                'is_super_admin' => true,
            ]
        );

        $this->command->info('✅ Super admin created: admin@saasapp.com / password123');
    }
}
