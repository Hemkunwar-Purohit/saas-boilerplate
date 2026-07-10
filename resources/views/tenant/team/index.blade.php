@extends('layouts.tenant')

@section('title', 'Team')
@section('page-title', 'Team')

@section('content')
<div x-data="{ showInvite: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $currentCount }} of {{ $limit == -1 ? 'unlimited' : $limit }} team members
            </p>
        </div>
        <button @click="showInvite = true"
                class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Invite member
        </button>
    </div>

    {{-- Members table --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">
                <tr>
                    <th class="px-5 py-3 font-medium">Member</th>
                    <th class="px-5 py-3 font-medium">Role</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Joined</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($members as $member)
                <tr>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatar_url }}" class="w-8 h-8 rounded-full">
                            <div>
                                <p class="font-medium">{{ $member->name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($member->hasRole('owner'))
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400">Owner</span>
                        @else
                            <form method="POST" action="/team/{{ $member->id }}/role" class="inline">
                                @csrf @method('PUT')
                                <select name="role" onchange="this.form.submit()"
                                        class="text-xs border border-gray-200 dark:border-gray-700 rounded-md px-2 py-1 bg-white dark:bg-gray-800">
                                    @foreach($roles as $role)
                                        @if($role->name !== 'owner')
                                        <option value="{{ $role->name }}" {{ $member->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $member->is_active ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                            {{ $member->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $member->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        @if(!$member->hasRole('owner') && $member->id !== auth()->id())
                        <form method="POST" action="/team/{{ $member->id }}"
                              onsubmit="return confirm('Remove {{ $member->name }} from the team?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700">Remove</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Invite Modal --}}
    <div x-show="showInvite" x-cloak
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         @click.self="showInvite = false">
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 w-full max-w-md">
            <h3 class="font-semibold mb-4">Invite team member</h3>
            <form method="POST" action="/team/invite">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                        @foreach($roles as $role)
                            @if($role->name !== 'owner')
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="showInvite = false"
                            class="flex-1 border border-gray-300 dark:border-gray-700 text-sm font-medium py-2 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium py-2 rounded-lg">
                        Send invite
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
