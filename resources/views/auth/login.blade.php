@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Admin Sign In</h1>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Central admin panel access</p>

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

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
        <input type="password" name="password" required
               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
        Sign in →
    </button>
</form>
@endsection

@section('footer')
    Not an admin?
    <a href="/register" class="text-indigo-600 hover:underline font-medium">Create workspace</a>
@endsection