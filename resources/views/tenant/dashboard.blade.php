@extends('layouts.tenant')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-bold">Welcome back, {{ auth()->user()->name }} 👋</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Here's what's happening with {{ tenant()->name }} today.</p>
</div>

@if($stats['trial_days_left'] !== null)
<div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center justify-between">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-amber-800 dark:text-amber-300">
            <strong>{{ $stats['trial_days_left'] }} days</strong> left in your trial.
        </p>
    </div>
    <a href="/billing" class="text-sm font-medium text-amber-700 dark:text-amber-400 hover:underline">Upgrade now →</a>
</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Total Users', 'value' => $stats['total_users'], 'icon' => '👥'],
            ['label' => 'Active Users', 'value' => $stats['active_users'], 'icon' => '✅'],
            ['label' => 'Current Plan', 'value' => $stats['plan_name'], 'icon' => '💳'],
            ['label' => 'Tenant ID', 'value' => tenant()->id, 'icon' => '🏢'],
        ];
    @endphp

    @foreach($cards as $card)
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl">{{ $card['icon'] }}</span>
        </div>
        <p class="text-2xl font-bold">{{ $card['value'] }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-sm">Recent Activity</h3>
        <a href="/activity" class="text-xs text-primary-600 hover:underline">View all →</a>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($recentActivity as $activity)
        <div class="px-5 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm">{{ $activity->description }}</p>
                <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-sm text-gray-500">No activity yet.</div>
        @endforelse
    </div>
</div>

@endsection
