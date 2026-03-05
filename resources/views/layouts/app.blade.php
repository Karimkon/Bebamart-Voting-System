<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ loginModal: false, mobileMenu: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BebaMart Voting') — Africa's Premier Voting Platform</title>
    <meta name="description" content="@yield('description', 'BebaMart Voting System — transparent, secure online voting for beauty pageants, tourism competitions, talent shows and awards across Africa.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="antialiased" style="font-family: 'Montserrat', sans-serif;">

{{-- NAVIGATION --}}
<nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
     x-data="{ scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 50)"
     :class="scrolled ? 'bg-[#0d0d2b] shadow-2xl' : 'bg-transparent'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full object-cover">
                </div>
                <div>
                    <span class="text-white font-bold text-xl tracking-wider" style="font-family: 'Cormorant Garamond', serif;">BebaMart Votes</span>
                    <div class="text-xs tracking-widest uppercase" style="color: #e6b030; font-size: 9px; letter-spacing: 0.2em;">Africa's Voting Platform</div>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-gray-300 hover:text-white text-sm tracking-wide transition-colors">Home</a>
                <a href="{{ route('competitions.index') }}" class="text-gray-300 hover:text-white text-sm tracking-wide transition-colors">Competitions</a>
                <a href="{{ route('leaderboard') }}" class="text-gray-300 hover:text-white text-sm tracking-wide transition-colors">Leaderboard</a>
                <a href="{{ route('transparency') }}" class="text-gray-300 hover:text-white text-sm tracking-wide transition-colors">Transparency</a>
                <a href="{{ route('archive') }}" class="text-gray-300 hover:text-white text-sm tracking-wide transition-colors">Archive</a>
            </div>

            {{-- Auth Buttons --}}
            <div class="hidden md:flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" class="w-9 h-9 rounded-full border-2 object-cover" style="border-color: #d4941a;">
                        @endif
                        <span class="text-gray-300 text-sm">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-white transition-colors tracking-widest uppercase">Logout</button>
                        </form>
                    </div>
                @else
                    <button @click="loginModal = true"
                            class="px-6 py-2.5 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b] transition-all duration-300"
                            style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                        Login to Vote
                    </button>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileMenu = !mobileMenu" class="md:hidden text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden pb-4 space-y-1 border-t border-white/10 mt-2 pt-3">
            <a href="{{ route('home') }}" class="block px-4 py-3 text-gray-300 hover:text-white text-sm rounded-lg hover:bg-white/5 transition-all">Home</a>
            <a href="{{ route('competitions.index') }}" class="block px-4 py-3 text-gray-300 hover:text-white text-sm rounded-lg hover:bg-white/5 transition-all">Competitions</a>
            <a href="{{ route('leaderboard') }}" class="block px-4 py-3 text-gray-300 hover:text-white text-sm rounded-lg hover:bg-white/5 transition-all">Leaderboard</a>
            <a href="{{ route('transparency') }}" class="block px-4 py-3 text-gray-300 hover:text-white text-sm rounded-lg hover:bg-white/5 transition-all">Transparency</a>
            @guest
                <button @click="loginModal = true" class="w-full mt-2 px-6 py-2.5 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b]" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                    Login to Vote
                </button>
            @endguest
        </div>
    </div>
</nav>

{{-- MAIN CONTENT --}}
@yield('content')

{{-- LOGIN MODAL --}}
<div x-show="loginModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 modal-container-mobile"
     style="background: rgba(13,13,43,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"
     @click.self="loginModal = false">

    <div x-show="loginModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="modal-sheet bg-white w-full max-w-md relative overflow-hidden">

        {{-- Gold top bar --}}
        <div class="h-1" style="background: linear-gradient(90deg, #d4941a, #e6b030, #d4941a);"></div>

        {{-- Close button --}}
        <button @click="loginModal = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-light text-gray-900 mb-2" style="font-family: 'Cormorant Garamond', serif;">Sign In to Vote</h2>
                <p class="text-gray-500 text-sm">Use your social account. We only collect your name and email.</p>
            </div>

            {{-- Social Login Buttons --}}
            <div class="space-y-3">
                <a href="{{ route('social.redirect', 'google') }}"
                   class="flex items-center gap-4 w-full px-5 py-3.5 border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Continue with Google</span>
                    <svg class="w-4 h-4 ml-auto text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('social.redirect', 'facebook') }}"
                   class="flex items-center gap-4 w-full px-5 py-3.5 border border-gray-200 hover:bg-blue-50 hover:border-blue-200 transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0" fill="#1877F2" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">Continue with Facebook</span>
                    <svg class="w-4 h-4 ml-auto text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('social.redirect', 'twitter-oauth-2') }}"
                   class="flex items-center gap-4 w-full px-5 py-3.5 border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0" fill="#000000" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.738l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Continue with X (Twitter)</span>
                    <svg class="w-4 h-4 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                By signing in, you agree to our
                <a href="#" class="underline hover:text-gray-600">Terms of Service</a> and
                <a href="#" class="underline hover:text-gray-600">Privacy Policy</a>.
                We only collect your name and email.
            </p>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer class="text-white mt-20" style="background: #0d0d2b;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-10 border-b border-white/10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full object-cover">
                    </div>
                    <span class="text-white font-bold text-xl" style="font-family: 'Cormorant Garamond', serif;">BebaMart Votes</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed max-w-sm">Africa's most trusted voting platform for beauty pageants, tourism competitions, talent shows and awards. Transparent. Secure. Elegant.</p>
                <div class="mt-4 text-xs text-gray-500">Sponsored by <span style="color: #e6b030;">BebeMart</span> — Africa's E-Commerce Leader</div>
            </div>
            <div>
                <h4 class="text-sm font-semibold tracking-widest uppercase mb-4" style="color: #e6b030;">Platform</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('competitions.index') }}" class="hover:text-white transition-colors">Active Competitions</a></li>
                    <li><a href="{{ route('leaderboard') }}" class="hover:text-white transition-colors">Live Leaderboard</a></li>
                    <li><a href="{{ route('transparency') }}" class="hover:text-white transition-colors">Transparency Dashboard</a></li>
                    <li><a href="{{ route('archive') }}" class="hover:text-white transition-colors">Past Competitions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold tracking-widest uppercase mb-4" style="color: #e6b030;">Trust & Security</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>✓ One vote per day per contestant</li>
                    <li>✓ Social login required</li>
                    <li>✓ IP & device monitoring</li>
                    <li>✓ Full vote audit trail</li>
                    <li>✓ Anti-fraud detection</li>
                </ul>
            </div>
        </div>
        <div class="pt-6 flex flex-col md:flex-row items-center justify-between text-xs text-gray-500">
            <span>© {{ date('Y') }} BebaMart Voting™ — A Product of BebeMart | bugandavotes.com</span>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Use</a>
                <a href="#" class="hover:text-white transition-colors">Contact Us</a>
            </div>
        </div>
    </div>
</footer>

{{-- Flash Messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 bg-white px-5 py-4 shadow-2xl border-l-4 max-w-sm"
     style="border-color: #d4941a;">
    <svg class="w-5 h-5 flex-shrink-0" style="color: #d4941a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm font-medium text-gray-800">{{ session('success') }}</p>
    <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     x-transition class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 bg-white px-5 py-4 shadow-2xl border-l-4 max-w-sm"
     style="border-color: #ef4444;">
    <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm font-medium text-gray-800">{{ session('error') }}</p>
    <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif


<script>
// Scroll-reveal with IntersectionObserver
(function() {
    const els = document.querySelectorAll('.reveal');
    if (!els.length) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    els.forEach(el => io.observe(el));
})();

// Progress bar animation on scroll
(function() {
    const bars = document.querySelectorAll('.progress-bar[data-width]');
    if (!bars.length) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.style.width = e.target.dataset.width + '%'; io.unobserve(e.target); } });
    }, { threshold: 0.3 });
    bars.forEach(b => io.observe(b));
})();
</script>

@stack('scripts')
</body>
</html>
