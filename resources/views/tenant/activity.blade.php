@extends('layouts.app')
@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('content')

<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
    <div class="p-5 border-b border-gray-100 dark:border-gray-800">
        <p class="text-sm text-gray-500">All actions performed in your workspace</p>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($activities as $activity)
        <div class="flex items-start gap-4 p-4">
            <img src="{{ $activity->causer?->avatar_url ?? 'https://ui-avatars.com/api/?name=S&background=6366f1&color=fff' }}"
                 class="w-8 h-8 rounded-full flex-shrink-0" alt="">
            <div class="flex-1">
                <p class="text-sm text-gray-900 dark:text-white">
                    <span class="font-medium">{{ $activity->causer?->name ?? 'System' }}</span>
                    {{ $activity->description }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $activity->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <p class="text-gray-400 text-sm">No activity logs yet</p>
        </div>
        @endforelse
    </div>
    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
        {{ $activities->links() }}
    </div>
</div>
@endsection
