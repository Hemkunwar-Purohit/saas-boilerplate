<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
      x-init="$watch('darkMode', v => localStorage.setItem('darkMode', v))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ tenant()->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: { primary: {
                50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca'
            }}}}
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors">

<div class="flex h-screen overflow-hidden">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-cloak></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-transform duration-200 flex flex-col">

        {{-- Logo --}}
        <div class="h-16 flex items-center gap-2 px-5 border-b border-gray-200 dark:border-gray-800">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="font-semibold text-sm truncate">{{ tenant()->name }}</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['icon' => 'home', 'label' => 'Dashboard', 'route' => 'tenant.dashboard', 'href' => '/dashboard'],
                    ['icon' => 'users', 'label' => 'Team', 'route' => 'tenant.team.index', 'href' => '/team'],
                    ['icon' => 'credit-card', 'label' => 'Billing', 'route' => 'tenant.billing.index', 'href' => '/billing'],
                    ['icon' => 'activity', 'label' => 'Activity Logs', 'route' => 'tenant.activity.index', 'href' => '/activity'],
                    ['icon' => 'user', 'label' => 'Profile', 'route' => 'tenant.profile.index', 'href' => '/profile'],
                ];
                $icons = [
                    'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 4v-2a4 4 0 00-3-3.87',
                    'credit-card' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    'activity' => 'M22 12h-4l-3 9L9 3l-3 9H2',
                    'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ $item['href'] }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
               {{ request()->is(trim($item['href'], '/')) || (trim($item['href'], '/') === '' && request()->is('/'))
                  ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400'
                  : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$item['icon']] }}"/>
                </svg>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>

        {{-- User card --}}
        <div class="p-3 border-t border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3 px-2 py-2">
                <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="/logout" class="mt-1">
                @csrf
                <button class="w-full text-left px-2 py-1.5 text-xs text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 lg:px-6 flex-shrink-0">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <h1 class="text-lg font-semibold hidden lg:block">@yield('page-title', 'Dashboard')</h1>

            <div class="flex items-center gap-2 ml-auto">
                {{-- Plan badge --}}
                @if(tenant()->plan)
                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400">
                    {{ tenant()->plan->name }} plan
                </span>
                @endif

                {{-- Dark mode toggle --}}
                <button @click="darkMode = !darkMode"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg x-show="!darkMode" class="w-[18px] h-[18px] text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" class="w-[18px] h-[18px] text-yellow-400" fill="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">

            {{-- Flash messages --}}
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

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
