@extends('layouts.tenant')
@section('title', 'Billing')
@section('page-title', 'Billing & Plans')

@section('content')
<div x-data="{ interval: 'monthly', paymentMethod: 'stripe', selectedPlan: null }">

{{-- Current Plan --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">Current Plan</h3>
            <p class="text-2xl font-bold mt-1">{{ $currentPlan?->name ?? 'No Plan' }}</p>
            @if($tenant->onTrial())
                <p class="text-sm text-amber-600 mt-1">Trial ends {{ $tenant->trial_ends_at->diffForHumans() }}</p>
            @endif
        </div>
        @if($currentPlan && !$currentPlan->is_free && $tenant->stripe_id)
        <form method="POST" action="/billing/cancel" onsubmit="return confirm('Cancel subscription?')">
            @csrf
            <button class="text-sm text-red-500 hover:text-red-700 border border-red-200 px-3 py-1.5 rounded-lg">
                Cancel subscription
            </button>
        </form>
        @endif
    </div>
</div>

{{-- Interval toggle --}}
<div class="flex items-center justify-center gap-4 mb-6">
    <span class="text-sm" :class="interval === 'monthly' ? 'font-semibold' : 'text-gray-500'">Monthly</span>
    <button @click="interval = interval === 'monthly' ? 'yearly' : 'monthly'"
            class="relative w-12 h-6 rounded-full transition-colors"
            :class="interval === 'yearly' ? 'bg-primary-600' : 'bg-gray-300'">
        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform"
             :class="interval === 'yearly' ? 'translate-x-6' : ''"></div>
    </button>
    <span class="text-sm" :class="interval === 'yearly' ? 'font-semibold' : 'text-gray-500'">
        Yearly <span class="text-green-600 text-xs font-medium">Save 2 months</span>
    </span>
</div>

{{-- Plans --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    @foreach($plans as $plan)
    <div class="bg-white dark:bg-gray-900 border-2 rounded-xl p-6 relative
        {{ $currentPlan?->id === $plan->id ? 'border-primary-500' : 'border-gray-200 dark:border-gray-800' }}">

        @if($currentPlan?->id === $plan->id)
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary-600 text-white text-xs font-medium px-3 py-0.5 rounded-full">
            Current
        </div>
        @endif

        <h3 class="font-bold text-lg">{{ $plan->name }}</h3>
        <p class="text-gray-500 text-sm mt-1">{{ $plan->description }}</p>

        <div class="my-4">
            @if($plan->is_free)
                <p class="text-3xl font-bold">Free</p>
            @else
                <p class="text-3xl font-bold">
                    $<span x-text="interval === 'yearly' ? '{{ $plan->price_yearly }}' : '{{ $plan->price_monthly }}'"></span>
                    <span class="text-base font-normal text-gray-500">/
                        <span x-text="interval === 'yearly' ? 'yr' : 'mo'"></span>
                    </span>
                </p>
            @endif
        </div>

        @php $features = $plan->features ?? []; @endphp
        <ul class="space-y-2 mb-6 text-sm">
            <li class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                {{ $features['users'] == -1 ? 'Unlimited' : $features['users'] }} users
            </li>
            <li class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                {{ $features['storage_mb'] == -1 ? 'Unlimited' : $features['storage_mb'].'MB' }} storage
            </li>
            <li class="flex items-center gap-2">
                <span class="{{ $features['api_calls'] > 0 ? 'text-green-500' : 'text-gray-400' }}">
                    {{ $features['api_calls'] > 0 ? '✓' : '✗' }}
                </span>
                {{ $features['api_calls'] == -1 ? 'Unlimited' : $features['api_calls'] }} API calls
            </li>
            <li class="flex items-center gap-2">
                <span class="{{ ($features['custom_domain'] ?? 0) ? 'text-green-500' : 'text-gray-400' }}">
                    {{ ($features['custom_domain'] ?? 0) ? '✓' : '✗' }}
                </span>
                Custom domain
            </li>
            <li class="flex items-center gap-2">
                <span class="{{ ($features['priority_support'] ?? 0) ? 'text-green-500' : 'text-gray-400' }}">
                    {{ ($features['priority_support'] ?? 0) ? '✓' : '✗' }}
                </span>
                Priority support
            </li>
        </ul>

        @if($currentPlan?->id !== $plan->id && !$plan->is_free)
        <button @click="selectedPlan = {{ $plan->id }}"
                x-data
                onclick="document.getElementById('checkout-modal').style.display='flex'"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 rounded-lg text-sm transition-colors">
            Upgrade to {{ $plan->name }}
        </button>
        @elseif($plan->is_free && $currentPlan?->id !== $plan->id)
        <button class="w-full border border-gray-300 text-gray-600 font-medium py-2 rounded-lg text-sm" disabled>
            Downgrade to Free
        </button>
        @else
        <button class="w-full bg-gray-100 dark:bg-gray-800 text-gray-500 font-medium py-2 rounded-lg text-sm" disabled>
            Current Plan
        </button>
        @endif
    </div>
    @endforeach
</div>

{{-- Checkout Modal --}}
<div id="checkout-modal" style="display:none"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 w-full max-w-md" x-data="checkoutForm()">

        <h3 class="font-semibold mb-4">Complete Payment</h3>

        {{-- Payment method tabs --}}
        <div class="flex gap-2 mb-4">
            <button @click="method = 'stripe'"
                    :class="method === 'stripe' ? 'bg-primary-600 text-white' : 'border border-gray-300 text-gray-600'"
                    class="flex-1 py-2 rounded-lg text-sm font-medium">
                💳 Stripe
            </button>
            <button @click="method = 'razorpay'"
                    :class="method === 'razorpay' ? 'bg-primary-600 text-white' : 'border border-gray-300 text-gray-600'"
                    class="flex-1 py-2 rounded-lg text-sm font-medium">
                🇮🇳 Razorpay
            </button>
        </div>

        {{-- Stripe form --}}
        <div x-show="method === 'stripe'">
            <form method="POST" action="/billing/stripe-checkout" id="stripe-form">
                @csrf
                <input type="hidden" name="plan_id" id="stripe-plan-id" value="">
                <input type="hidden" name="payment_method_id" id="payment-method-id">
                <input type="hidden" name="interval" x-bind:value="interval">

                <div id="card-element" class="p-3 border border-gray-300 dark:border-gray-700 rounded-lg mb-4 bg-white"></div>
                <div id="card-errors" class="text-red-500 text-xs mb-4"></div>

                <button type="submit" id="stripe-submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg text-sm">
                    Pay with Stripe
                </button>
            </form>
        </div>

        {{-- Razorpay --}}
        <div x-show="method === 'razorpay'" x-cloak>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Pay securely with UPI, Cards, Net Banking via Razorpay.
            </p>
            <button @click="initRazorpay()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg text-sm">
                Pay with Razorpay
            </button>
        </div>

        <button onclick="document.getElementById('checkout-modal').style.display='none'"
                class="w-full mt-3 text-sm text-gray-500 hover:text-gray-700">
            Cancel
        </button>
    </div>
</div>

</div>

{{-- Stripe JS --}}
<script src="https://js.stripe.com/v3/"></script>
{{-- Razorpay JS --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
const stripe = Stripe('{{ config('cashier.key') }}');
const elements = stripe.elements();
const cardElement = elements.create('card', {
    style: { base: { fontSize: '14px', color: '#374151' } }
});
cardElement.mount('#card-element');

function checkoutForm() {
    return {
        method: 'stripe',
        async initRazorpay() {
            const planId = document.getElementById('stripe-plan-id').value;
            const response = await fetch('/billing/razorpay-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ plan_id: planId, interval: 'monthly' })
            });
            const data = await response.json();
            if (!data.success) { alert(data.error); return; }

            const options = {
                key: '{{ config('services.razorpay.key') }}',
                amount: data.amount,
                currency: 'INR',
                order_id: data.order_id,
                name: '{{ config('app.name') }}',
                handler: function(response) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/billing/razorpay-verify';
                    const fields = {
                        '_token': '{{ csrf_token() }}',
                        'razorpay_order_id': response.razorpay_order_id,
                        'razorpay_payment_id': response.razorpay_payment_id,
                        'razorpay_signature': response.razorpay_signature,
                        'plan_id': planId
                    };
                    Object.entries(fields).forEach(([k, v]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = k;
                        input.value = v;
                        form.appendChild(input);
                    });
                    document.body.appendChild(form);
                    form.submit();
                }
            };
            new Razorpay(options).open();
        }
    }
}

// Stripe form submit
document.getElementById('stripe-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('stripe-submit');
    btn.textContent = 'Processing...';
    btn.disabled = true;

    const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
    });

    if (error) {
        document.getElementById('card-errors').textContent = error.message;
        btn.textContent = 'Pay with Stripe';
        btn.disabled = false;
        return;
    }

    document.getElementById('payment-method-id').value = paymentMethod.id;
    e.target.submit();
});
</script>
@endsection
