@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Total Tenants', 'value' => $stats['total_tenants'], 'icon' => '🏢'],
            ['label' => 'Active Tenants', 'value' => $stats['active_tenants'], 'icon' => '✅'],
            ['label' => 'On Trial', 'value' => $stats['trial_tenants'], 'icon' => '⏳'],
            ['label' => 'MRR', 'value' => '$' . number_format($stats['mrr'], 0), 'icon' => '💰'],
        ];
    @endphp
    @foreach($cards as $card)
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <span class="text-2xl">{{ $card['icon'] }}</span>
        <p class="text-2xl font-bold mt-3">{{ $card['value'] }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent tenants --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
            <h3 class="font-semibold text-sm">Recent Tenants</h3>
            <a href="/admin/tenants" class="text-xs text-primary-600 hover:underline">View all →</a>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($recentTenants as $tenant)
                <tr>
                    <td class="px-5 py-3">
                        <p class="font-medium">{{ $tenant->name }}</p>
                        <p class="text-xs text-gray-500">{{ $tenant->id }}.localhost</p>
                    </td>
                    <td class="px-5 py-3 text-xs">
                        <span class="px-2 py-0.5 rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400">
                            {{ $tenant->plan?->name ?? 'No plan' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs">
                        @if($tenant->is_active)
                            <span class="text-green-600">● Active</span>
                        @else
                            <span class="text-red-500">● Suspended</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $tenant->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Plan distribution --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5">
        <h3 class="font-semibold text-sm mb-4">Plan Distribution</h3>
        <div class="space-y-3">
            @foreach($planDistribution as $plan)
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span>{{ $plan->name }}</span>
                    <span class="text-gray-500">{{ $plan->tenants_count }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full"
                         style="width: {{ $stats['total_tenants'] > 0 ? ($plan->tenants_count / $stats['total_tenants'] * 100) : 0 }}%">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
