<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
      x-init="$watch('darkMode', v => localStorage.setItem('darkMode', v))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { primary: {
            50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca'
        }}}}}
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">

<div class="flex h-screen overflow-hidden">
    <aside class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0">
        <div class="h-16 flex items-center gap-2 px-5 border-b border-gray-800">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="font-semibold text-sm">Super Admin</span>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->is('admin/dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                📊 Dashboard
            </a>
            <a href="/admin/tenants" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->is('admin/tenants*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                🏢 Tenants
            </a>
        </nav>

        <div class="p-3 border-t border-gray-800">
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full text-left px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-6">
            <h1 class="text-lg font-semibold">@yield('page-title', 'Dashboard')</h1>
            <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg x-show="!darkMode" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="darkMode" x-cloak class="w-[18px] h-[18px] text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z"/>
                </svg>
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
