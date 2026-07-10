@extends('layouts.tenant')

@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
<div class="max-w-3xl" x-data="{ tab: 'general' }">

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 border-b border-gray-200 dark:border-gray-800">
        <button @click="tab = 'general'"
                :class="tab === 'general' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">General</button>
        <button @click="tab = 'password'"
                :class="tab === 'password' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">Password</button>
        <button @click="tab = 'avatar'"
                :class="tab === 'avatar' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">Avatar</button>
    </div>

    {{-- General tab --}}
    <div x-show="tab === 'general'" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <h3 class="font-semibold mb-4">Personal Information</h3>
        <form method="POST" action="/profile/update" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Timezone</label>
                    <select name="timezone" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option {{ $user->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option {{ $user->timezone === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
                        <option {{ $user->timezone === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Save changes
            </button>
        </form>
    </div>

    {{-- Password tab --}}
    <div x-show="tab === 'password'" x-cloak class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <h3 class="font-semibold mb-4">Change Password</h3>
        <form method="POST" action="/profile/password" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Current password</label>
                <input type="password" name="current_password"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">New password</label>
                <input type="password" name="password" minlength="8"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm new password</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Update password
            </button>
        </form>
    </div>

    {{-- Avatar tab --}}
    <div x-show="tab === 'avatar'" x-cloak class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <h3 class="font-semibold mb-4">Profile Picture</h3>
        <div class="flex items-center gap-4 mb-4">
            <img src="{{ $user->avatar_url }}" class="w-16 h-16 rounded-full">
        </div>
        <form method="POST" action="/profile/avatar" enctype="multipart/form-data">
            @csrf
            <input type="file" name="avatar" accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            @error('avatar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

            <button type="submit" class="mt-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Upload avatar
            </button>
        </form>
    </div>

</div>
@endsection
