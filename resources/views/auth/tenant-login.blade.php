@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
    Welcome back{{ isset($tenant) ? ', ' . $tenant->name : '' }}
</h1>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Sign in to your workspace</p>

{{-- Session alerts --}}
@if(session('success'))
    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="/login">
    @csrf

    {{-- Email --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email address</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('email') border-red-500 @enderror">
        @error('email')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-4">
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
            <a href="/forgot-password" class="text-xs text-primary-600 hover:underline">
                Forgot password?
            </a>
        </div>
        <input type="password" name="password" required
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('password') border-red-500 @enderror">
        @error('password')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="mb-5 flex items-center gap-2">
        <input type="checkbox" name="remember" id="remember"
               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        <label for="remember" class="text-sm text-gray-600 dark:text-gray-400">Remember me</label>
    </div>

    {{-- Submit --}}
    <button type="submit"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
        Sign in →
    </button>
</form>
@endsection


