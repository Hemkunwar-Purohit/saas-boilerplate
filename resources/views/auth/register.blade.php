@extends('layouts.auth')

@section('title', 'Create your account')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Create your account</h1>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Start your 14-day free trial. No credit card required.</p>

{{-- Alerts --}}
@if(session('error'))
    <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('register') }}" x-data="registerForm()">
    @csrf

    {{-- Name --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your name</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('name') border-red-500 @enderror">
        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Work email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('email') border-red-500 @enderror">
        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Company + Subdomain --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company name</label>
        <input type="text" name="company_name" value="{{ old('company_name') }}" required
               x-on:input="generateSubdomain($event.target.value)"
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        @error('company_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your workspace URL</label>
        <div class="flex items-center gap-0 rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 @error('subdomain') border-red-500 @enderror">
            <input type="text" name="subdomain" x-model="subdomain" required
                   placeholder="mycompany"
                   class="flex-1 px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none">
            <span class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm border-l border-gray-300 dark:border-gray-700 whitespace-nowrap">
                .{{ config('app.domain', 'localhost') }}
            </span>
        </div>
        @error('subdomain') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Password --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
        <input type="password" name="password" required minlength="8"
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('password') border-red-500 @enderror">
        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm password</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Plan selection --}}
    @if($plans->count() > 0)
    <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Choose plan</label>
        <div class="grid grid-cols-3 gap-2">
            @foreach($plans as $plan)
            <label class="cursor-pointer">
                <input type="radio" name="plan_id" value="{{ $plan->id }}"
                       {{ $plan->is_free ? 'checked' : '' }} class="sr-only peer">
                <div class="border-2 border-gray-200 dark:border-gray-700 peer-checked:border-primary-500 rounded-lg p-2 text-center transition-colors">
                    <p class="text-xs font-medium text-gray-900 dark:text-white">{{ $plan->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->is_free ? 'Free' : '$'.$plan->price_monthly.'/mo' }}</p>
                </div>
            </label>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Terms --}}
    <div class="mb-5 flex items-start gap-2">
        <input type="checkbox" name="terms" id="terms" required
               class="mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        <label for="terms" class="text-xs text-gray-600 dark:text-gray-400">
            I agree to the <a href="#" class="text-primary-600 hover:underline">Terms of Service</a>
            and <a href="#" class="text-primary-600 hover:underline">Privacy Policy</a>
        </label>
    </div>
    @error('terms') <p class="text-xs text-red-500 -mt-4 mb-4">{{ $message }}</p> @enderror

    {{-- Submit --}}
    <button type="submit"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
        Create account →
    </button>
</form>

<script>
function registerForm() {
    return {
        subdomain: '{{ old('subdomain', '') }}',
        generateSubdomain(company) {
            this.subdomain = company
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .substring(0, 50);
        }
    }
}
</script>
@endsection

@section('footer')
    Already have an account?
    <a href="/login" class="text-primary-600 hover:underline font-medium">Sign in</a>
@endsection
