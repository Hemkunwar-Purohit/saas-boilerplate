<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function index()
    {
        $tenant = tenant();

        
        $plans = Plan::on('mysql')->where('is_active', true)->orderBy('sort_order')->get();

            $currentPlan = $tenant->plan_id
            ? Plan::on('mysql')->find($tenant->plan_id)
            : null;

        return view('tenant.billing.index', compact('tenant', 'plans', 'currentPlan'));
    }

    public function stripeCheckout(Request $request)
    {
        $request->validate([
            'plan_id'           => ['required'],
            'payment_method_id' => ['required', 'string'],
            'interval'          => ['required', 'in:monthly,yearly'],
        ]);

        $plan   = Plan::on('mysql')->findOrFail($request->plan_id);
        $tenant = tenant();

        tenancy()->end();
        $centralTenant = \App\Models\Tenant::find($tenant->id);
        $result = $this->paymentService->createStripeSubscription(
            $centralTenant, $plan, $request->payment_method_id, $request->interval
        );
        tenancy()->initialize($centralTenant);

        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', "Successfully subscribed to {$plan->name} plan!");
    }

    public function razorpayCreateOrder(Request $request)
    {
        $request->validate([
            'plan_id'  => ['required'],
            'interval' => ['required', 'in:monthly,yearly'],
        ]);

        $plan   = Plan::on('mysql')->findOrFail($request->plan_id);
        $result = $this->paymentService->createRazorpayOrder($plan, $request->interval);

        return response()->json($result);
    }

    public function razorpayVerify(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => ['required'],
            'razorpay_payment_id' => ['required'],
            'razorpay_signature'  => ['required'],
            'plan_id'             => ['required'],
        ]);

        $isValid = $this->paymentService->verifyRazorpayPayment(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (!$isValid) {
            return back()->with('error', 'Payment verification failed.');
        }

        $plan   = Plan::on('mysql')->findOrFail($request->plan_id);
        $tenant = tenant();

        tenancy()->end();
        $centralTenant = \App\Models\Tenant::find($tenant->id);
        $this->paymentService->completeRazorpaySubscription($centralTenant, $plan, $request->razorpay_payment_id);
        tenancy()->initialize($centralTenant);

        return redirect('/billing')->with('success', "Subscribed to {$plan->name}!");
    }

    public function cancel(Request $request)
    {
        $tenant = tenant();

        tenancy()->end();
        $centralTenant = \App\Models\Tenant::find($tenant->id);
        $result = $this->paymentService->cancelStripeSubscription($centralTenant);
        tenancy()->initialize($centralTenant);

        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', 'Subscription cancelled.');
    }
}