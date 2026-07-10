<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Free',
                'slug'          => 'free',
                'description'   => 'Perfect for trying out the platform',
                'price_monthly' => 0,
                'price_yearly'  => 0,
                'is_free'       => true,
                'is_active'     => true,
                'trial_days'    => 0,
                'sort_order'    => 1,
                'features'      => [
                    'users'            => 1,      // max 1 user
                    'storage_mb'       => 100,    // 100 MB storage
                    'api_calls'        => 100,    // 100 API calls/month
                    'custom_domain'    => 0,      // nahi
                    'priority_support' => 0,
                    'white_label'      => 0,
                    'activity_log'     => 0,
                    'team_management'  => 0,
                ],
            ],
            [
                'name'                      => 'Basic',
                'slug'                      => 'basic',
                'description'               => 'For small teams getting started',
                'price_monthly'             => 19,
                'price_yearly'              => 190,    // 2 months free
                'stripe_monthly_price_id'   => 'price_basic_monthly',   // Stripe se replace karo
                'stripe_yearly_price_id'    => 'price_basic_yearly',
                'razorpay_monthly_plan_id'  => 'plan_basic_monthly',    // Razorpay se replace karo
                'is_free'                   => false,
                'is_active'                 => true,
                'trial_days'                => 14,
                'sort_order'                => 2,
                'features'                  => [
                    'users'            => 5,
                    'storage_mb'       => 2000,
                    'api_calls'        => 5000,
                    'custom_domain'    => 0,
                    'priority_support' => 0,
                    'white_label'      => 0,
                    'activity_log'     => 1,
                    'team_management'  => 1,
                ],
            ],
            [
                'name'                      => 'Pro',
                'slug'                      => 'pro',
                'description'               => 'For growing businesses — unlimited power',
                'price_monthly'             => 49,
                'price_yearly'              => 490,
                'stripe_monthly_price_id'   => 'price_pro_monthly',
                'stripe_yearly_price_id'    => 'price_pro_yearly',
                'razorpay_monthly_plan_id'  => 'plan_pro_monthly',
                'is_free'                   => false,
                'is_active'                 => true,
                'trial_days'                => 14,
                'sort_order'                => 3,
                'features'                  => [
                    'users'            => -1,     // -1 = unlimited
                    'storage_mb'       => -1,
                    'api_calls'        => -1,
                    'custom_domain'    => 1,
                    'priority_support' => 1,
                    'white_label'      => 1,
                    'activity_log'     => 1,
                    'team_management'  => 1,
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        $this->command->info('✅ Plans seeded: Free, Basic, Pro');
    }
}
