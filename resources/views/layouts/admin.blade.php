<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — BebaMart Voting Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="antialiased bg-gray-50" style="font-family: 'Montserrat', sans-serif;">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="flex-shrink-0 transition-all duration-300 flex flex-col"
           :class="sidebarOpen ? 'w-64' : 'w-16'"
           style="background: #0d0d2b;">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-9 h-9 rounded-full object-cover">
            </div>
            <div x-show="sidebarOpen" x-transition>
                <div class="text-white font-bold text-base" style="font-family: 'Cormorant Garamond', serif;">BebaMart Votes</div>
                <div class="text-xs" style="color: #e6b030;">Admin Panel</div>
            </div>
        </div>

        {{-- Nav Links --}}
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            @php
                $nav = [
                    ['route' => 'admin.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard'],
                    ['route' => 'admin.competitions.index', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'label' => 'Competitions'],
                    ['route' => 'admin.contestants.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Contestants'],
                    ['route' => 'admin.votes.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Votes'],
                    ['route' => 'admin.users.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Users'],
                ];
            @endphp
            @foreach($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs($item['route'] . '*') ? 'text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}"
               style="{{ request()->routeIs($item['route'] . '*') ? 'background: linear-gradient(135deg, rgba(212,148,26,0.2), rgba(230,176,48,0.1)); border-left: 3px solid #d4941a;' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                </svg>
                <span x-show="sidebarOpen" x-transition class="font-medium">{{ $item['label'] }}</span>
            </a>
            @endforeach

            <div class="pt-4 border-t border-white/10 mt-4">
                <a href="{{ route('home') }}" target="_blank"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-show="sidebarOpen" x-transition>View Site</span>
                </a>
            </div>
        </nav>

        {{-- User info --}}
        <div class="p-3 border-t border-white/10">
            <div class="flex items-center gap-3">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border-2" style="border-color: #d4941a;">
                @else
                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold" style="background: #d4941a;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                @endif
                <div x-show="sidebarOpen" x-transition class="flex-1 min-w-0">
                    <div class="text-white text-xs font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-gray-500 text-xs">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top Bar --}}
        <header class="bg-white border-b border-gray-200 flex items-center gap-4 px-6 py-4 flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex-1">
                <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                <div class="text-xs text-gray-400">@yield('page-subtitle', '')</div>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-semibold tracking-widest uppercase text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mx-6 mt-4 flex items-center gap-3 px-4 py-3 text-sm font-medium text-green-800 border border-green-200 rounded-lg" style="background: #f0fdf4;">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
            <button @click="show=false" class="ml-auto text-green-600">&#x2715;</button>
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
