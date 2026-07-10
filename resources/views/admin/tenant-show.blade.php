@extends('layouts.admin')
@section('title', $tenant->name)
@section('page-title', $tenant->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <h3 class="font-semibold mb-4">Tenant Details</h3>
        <dl class="space-y-3 text-sm">
            <div class="flex gap-4">
                <dt class="text-gray-500 w-32">ID</dt>
                <dd class="font-medium">{{ $tenant->id }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="text-gray-500 w-32">Name</dt>
                <dd class="font-medium">{{ $tenant->name }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="text-gray-500 w-32">Email</dt>
                <dd class="font-medium">{{ $tenant->email }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="text-gray-500 w-32">Plan</dt>
                <dd class="font-medium">{{ $tenant->plan?->name ?? 'No plan' }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="text-gray-500 w-32">Status</dt>
                <dd>
                    @if($tenant->is_active)
                        <span class="text-green-600 font-medium">Active</span>
                    @else
                        <span class="text-red-500 font-medium">Suspended</span>
                    @endif
                </dd>
            </div>
            <div class="flex gap-4">
                <dt class="text-gray-500 w-32">Created</dt>
                <dd>{{ $tenant->created_at->format('M d, Y h:i A') }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="text-gray-500 w-32">Domain</dt>
                <dd>{{ $tenant->id }}.localhost</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <h3 class="font-semibold mb-4">Actions</h3>
        <form method="POST" action="/admin/tenants/{{ $tenant->id }}/toggle">
            @csrf
            <button class="w-full py-2 px-4 rounded-lg text-sm font-medium {{ $tenant->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                {{ $tenant->is_active ? '🔒 Suspend Tenant' : '✅ Activate Tenant' }}
            </button>
        </form>

        <a href="http://{{ $tenant->id }}.localhost:8000/dashboard"
           target="_blank"
           class="mt-2 w-full py-2 px-4 rounded-lg text-sm font-medium bg-primary-50 text-primary-600 hover:bg-primary-100 flex items-center justify-center">
            🔗 Open Workspace
        </a>
    </div>

</div>

<div class="mt-4">
    <a href="/admin/tenants" class="text-sm text-primary-600 hover:underline">← Back to tenants</a>
</div>
@endsection
