{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.auth')
@section('title', 'Verify Email')
@section('content')
<div class="text-center">
    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>

    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Check your inbox</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
        We sent a verification link to <strong class="text-gray-700 dark:text-gray-300">{{ auth()->user()->email }}</strong>
    </p>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors mb-3">
            Resend verification email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            Sign out
        </button>
    </form>
</div>
@endsection
