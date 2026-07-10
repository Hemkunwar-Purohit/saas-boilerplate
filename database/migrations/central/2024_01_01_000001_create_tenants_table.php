<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CENTRAL MIGRATION — Tenants extra columns
 *
 * stancl/tenancy pehle se yeh tables banata hai:
 *   - tenants (id, data, created_at, updated_at)
 *   - domains (id, domain, tenant_id, created_at, updated_at)
 *
 * Hum sirf EXTRA columns add kar rahe hain tenants table mein.
 * isliye Schema::table use kar rahe hain, Schema::create NAHI.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── tenants table mein extra columns add karo ──────────
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('email')->nullable()->unique()->after('name');

            // Plan & billing
            $table->foreignId('plan_id')
                  ->nullable()
                  ->after('email')
                  ->constrained('plans')
                  ->nullOnDelete();

            $table->timestamp('trial_ends_at')->nullable()->after('plan_id');
            $table->boolean('is_active')->default(true)->after('trial_ends_at');

            // Stripe (Laravel Cashier compatible)
            $table->string('stripe_id')->nullable()->index()->after('is_active');
            $table->string('pm_type')->nullable()->after('stripe_id');
            $table->string('pm_last_four', 4)->nullable()->after('pm_type');

            // Razorpay
            $table->string('razorpay_id')->nullable()->index()->after('pm_last_four');
            $table->string('razorpay_subscription_id')->nullable()->after('razorpay_id');
        });

        // ── domains table mein kuch add nahi karna — stancl ka kaafi hai ──
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'name', 'email', 'plan_id', 'trial_ends_at', 'is_active',
                'stripe_id', 'pm_type', 'pm_last_four',
                'razorpay_id', 'razorpay_subscription_id',
            ]);
        });
    }
};
