@extends('layouts.auth')

@section('title', 'Forgot password')

@section('content')

{{-- Session alerts --}}
@if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Validation errors --}}
@if($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
        {{ $errors->first() }}
    </div>
@endif

<div class="mb-5">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
        Forgot your password?
    </h2>

    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        Enter your email address and we'll send you a password reset link.
    </p>
</div>

<form method="POST" action="{{ url('/forgot-password') }}">
    @csrf

    {{-- Email --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Email address
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('email') border-red-500 @enderror"
        >

        @error('email')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Submit --}}
    <button
        type="submit"
        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
    >
        Send reset link
    </button>
</form>

{{-- Back to login --}}
<div class="mt-4 text-center">
    <a href="{{ url('/login') }}"
       class="text-sm text-primary-600 hover:underline">
        ← Back to sign in
    </a>
</div>

@endsection