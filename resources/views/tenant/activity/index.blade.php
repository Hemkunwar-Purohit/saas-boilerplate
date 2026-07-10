@extends('layouts.tenant')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <select name="user_id" onchange="this.form.submit()"
            class="text-sm border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-800">
        <option value="">All members</option>
        @foreach($users as $u)
            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
    </select>

    <input type="date" name="from" value="{{ request('from') }}" onchange="this.form.submit()"
           class="text-sm border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-800">

    <input type="date" name="to" value="{{ request('to') }}" onchange="this.form.submit()"
           class="text-sm border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-800">

    @if(request()->hasAny(['user_id', 'from', 'to']))
    <a href="/activity" class="text-sm text-primary-600 hover:underline self-center">Clear filters</a>
    @endif
</form>

{{-- Timeline --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl">
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($activities as $activity)
        <div class="px-5 py-4 flex items-start gap-3">
            <img src="{{ $activity->causer?->avatar_url ?? 'https://ui-avatars.com/api/?name=System' }}"
                 class="w-8 h-8 rounded-full flex-shrink-0 mt-0.5">
            <div class="flex-1 min-w-0">
                <p class="text-sm">
                    <span class="font-medium">{{ $activity->causer?->name ?? 'System' }}</span>
                    {{ $activity->description }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $activity->created_at->format('M d, Y \a\t h:i A') }}
                    · {{ $activity->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
        @empty
        <div class="px-5 py-12 text-center text-sm text-gray-500">No activity logs found.</div>
        @endforelse
    </div>
</div>

<div class="mt-4">
    {{ $activities->links() }}
</div>

@endsection
