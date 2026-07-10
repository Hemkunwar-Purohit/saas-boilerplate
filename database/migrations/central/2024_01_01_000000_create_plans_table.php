<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CENTRAL DATABASE MIGRATION — Plans table
 * Free, Basic, Pro plans yahan define honge.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');                // "Free", "Basic", "Pro"
            $table->string('slug')->unique();      // "free", "basic", "pro"
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);

            // Stripe price IDs
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();

            // Razorpay plan IDs
            $table->string('razorpay_monthly_plan_id')->nullable();
            $table->string('razorpay_yearly_plan_id')->nullable();

            /**
             * Features JSON structure:
             * {
             *   "users": 5,          // max users per tenant (-1 = unlimited)
             *   "storage_mb": 1000,  // max storage in MB
             *   "api_calls": 1000,   // max API calls per month
             *   "custom_domain": 0,  // 0 = nahi, 1 = haan
             *   "priority_support": 0,
             *   "white_label": 0
             * }
             */
            $table->json('features')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('trial_days')->default(14);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
