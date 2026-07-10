<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Multi-Tenant SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center">
    <div class="text-center max-w-2xl px-6">
        <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>

        <h1 class="text-5xl font-bold mb-4">{{ config('app.name') }}</h1>
        <p class="text-xl text-gray-400 mb-10">Multi-Tenant SaaS Boilerplate — Launch faster, build smarter.</p>

        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="{{ route('register') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl text-lg transition-colors">
                Start free trial →
            </a>
            <a href="{{ route('login') }}"
               class="border border-gray-700 hover:border-gray-500 text-gray-300 hover:text-white font-semibold px-8 py-3 rounded-xl text-lg transition-colors">
                Sign in
            </a>
        </div>

        <div class="mt-16 grid grid-cols-3 gap-6 text-left">
            @foreach([
                ['icon' => '🏢', 'title' => 'Multi-tenancy', 'desc' => 'Isolated DB per tenant'],
                ['icon' => '💳', 'title' => 'Stripe + Razorpay', 'desc' => 'Global + Indian payments'],
                ['icon' => '🔐', 'title' => 'Roles & Permissions', 'desc' => 'Owner, Admin, Member'],
            ] as $f)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="text-2xl mb-2">{{ $f['icon'] }}</div>
                <p class="font-semibold text-white">{{ $f['title'] }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
