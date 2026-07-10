@extends('layouts.admin')
@section('title', 'Tenants')
@section('page-title', 'All Tenants')

@section('content')

<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-semibold text-sm">All Workspaces ({{ $tenants->total() }})</h3>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800/50 text-xs text-gray-500 uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Workspace</th>
                <th class="px-5 py-3 text-left">Plan</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-left">Created</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($tenants as $tenant)
            <tr>
                <td class="px-5 py-3">
                    <p class="font-medium">{{ $tenant->name }}</p>
                    <p class="text-xs text-gray-500">{{ $tenant->id }}.localhost</p>
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400">
                        {{ $tenant->plan?->name ?? 'No plan' }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    @if($tenant->is_active)
                        <span class="text-green-600 text-xs font-medium">● Active</span>
                    @else
                        <span class="text-red-500 text-xs font-medium">● Suspended</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-500 text-xs">
                    {{ $tenant->created_at->format('M d, Y') }}
                </td>
                <td class="px-5 py-3">
                    <form method="POST" action="/admin/tenants/{{ $tenant->id }}/toggle" class="inline">
                        @csrf
                        <button class="text-xs {{ $tenant->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }}">
                            {{ $tenant->is_active ? 'Suspend' : 'Activate' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
        {{ $tenants->links() }}
    </div>
</div>

@endsection
