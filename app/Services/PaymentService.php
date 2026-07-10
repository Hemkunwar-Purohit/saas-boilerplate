<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    // ── Stripe ────────────────────────────────────────────────

    public function createStripeSubscription(Tenant $tenant, Plan $plan, string $paymentMethodId, string $interval = 'monthly'): array
    {
        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));

            if (!$tenant->stripe_id) {
                $customer = $stripe->customers->create([
                    'email'    => $tenant->email,
                    'name'     => $tenant->name,
                    'metadata' => ['tenant_id' => $tenant->id],
                ]);
                $tenant->update(['stripe_id' => $customer->id]);
            }

            $stripe->paymentMethods->attach($paymentMethodId, ['customer' => $tenant->stripe_id]);
            $stripe->customers->update($tenant->stripe_id, [
                'invoice_settings' => ['default_payment_method' => $paymentMethodId],
            ]);

            $priceId = $interval === 'yearly'
                ? $plan->stripe_yearly_price_id
                : $plan->stripe_monthly_price_id;

            $subscription = $stripe->subscriptions->create([
                'customer' => $tenant->stripe_id,
                'items'    => [['price' => $priceId]],
                'metadata' => ['tenant_id' => $tenant->id, 'plan_id' => $plan->id],
                'expand'   => ['latest_invoice.payment_intent'],
            ]);

            $tenant->update(['plan_id' => $plan->id, 'is_active' => true, 'trial_ends_at' => null]);
            activity()->log("Subscribed to {$plan->name} plan via Stripe");

            return ['success' => true, 'subscription_id' => $subscription->id];

        } catch (\Stripe\Exception\CardException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        } catch (\Exception $e) {
            Log::error('Stripe error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Payment failed. Please try again.'];
        }
    }

    public function cancelStripeSubscription(Tenant $tenant): array
    {
        try {
            $stripe       = new \Stripe\StripeClient(config('cashier.secret'));
            $subscriptions = $stripe->subscriptions->all(['customer' => $tenant->stripe_id, 'status' => 'active']);

            foreach ($subscriptions->data as $sub) {
                $stripe->subscriptions->cancel($sub->id);
            }

            $freePlan = Plan::where('is_free', true)->first();
            $tenant->update(['plan_id' => $freePlan?->id]);
            activity()->log('Cancelled Stripe subscription');

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ── Razorpay ──────────────────────────────────────────────

    public function createRazorpayOrder(Plan $plan, string $interval = 'monthly'): array
    {
        try {
            $api    = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $amount = $interval === 'yearly' ? $plan->price_yearly * 100 : $plan->price_monthly * 100;

            $order = $api->order->create([
                'amount'   => (int) $amount,
                'currency' => 'INR',
                'receipt'  => 'order_' . tenant()->id . '_' . time(),
                'notes'    => ['tenant_id' => tenant()->id, 'plan_id' => $plan->id, 'interval' => $interval],
            ]);

            return ['success' => true, 'order_id' => $order->id, 'amount' => $amount];

        } catch (\Exception $e) {
            Log::error('Razorpay error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyRazorpayPayment(string $orderId, string $paymentId, string $signature): bool
    {
        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, config('services.razorpay.secret'));
        return hash_equals($expected, $signature);
    }

    public function completeRazorpaySubscription(Tenant $tenant, Plan $plan, string $paymentId): void
    {
        $tenant->update(['plan_id' => $plan->id, 'razorpay_id' => $paymentId, 'is_active' => true, 'trial_ends_at' => null]);
        activity()->log("Subscribed to {$plan->name} plan via Razorpay");
    }
}
