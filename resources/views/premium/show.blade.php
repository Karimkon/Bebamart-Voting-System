@extends('layouts.app')

@section('title', 'Premium — ' . $competition->name)

@section('content')
<div class="pt-16 sm:pt-20 min-h-screen" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-xl mx-auto px-4 py-12">

        {{-- Back --}}
        <a href="{{ route('competitions.show', $competition->slug) }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-8 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to {{ $competition->name }}
        </a>

        {{-- Already subscribed --}}
        @if($hasPremium)
        <div class="p-8 text-center border rounded-lg" style="background: rgba(230,176,48,0.08); border-color: rgba(230,176,48,0.4);">
            <div class="text-5xl mb-4">⭐</div>
            <h2 class="text-2xl font-light text-white mb-2" style="font-family: 'Cormorant Garamond', serif;">You're Premium!</h2>
            <p class="text-gray-400 text-sm mb-6">You have an active Premium subscription for <strong class="text-white">{{ $competition->name }}</strong>. Enjoy 10 votes per day across all contestants.</p>
            <a href="{{ route('competitions.show', $competition->slug) }}"
               class="inline-block px-8 py-3 text-sm font-bold tracking-widest uppercase hover:opacity-90 transition-opacity"
               style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
                Vote Now
            </a>
        </div>
        @else

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-900/40 border border-red-500/40 text-red-300 text-sm rounded">{{ session('error') }}</div>
        @endif

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="text-5xl mb-4">⭐</div>
            <div class="inline-block text-xs px-3 py-1.5 font-semibold tracking-widest uppercase mb-4" style="background: rgba(230,176,48,0.2); color: #e6b030; border: 1px solid rgba(230,176,48,0.3);">
                Premium Subscription
            </div>
            <h1 class="text-4xl font-light text-white mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Go Premium
            </h1>
            <p class="text-gray-400 text-sm">For <strong class="text-white">{{ $competition->name }}</strong></p>
        </div>

        {{-- Pricing Card --}}
        <div class="p-8 border rounded-lg mb-8" style="background: rgba(255,255,255,0.04); border-color: rgba(230,176,48,0.3);">
            <div class="text-center mb-6">
                <div class="text-5xl font-bold text-white mb-1" style="font-family: 'Cormorant Garamond', serif;">UGX {{ number_format($price) }}</div>
                <div class="text-gray-400 text-sm">one-time per competition</div>
            </div>

            <ul class="space-y-3 mb-8">
                <li class="flex items-center gap-3 text-sm text-gray-300">
                    <svg class="w-5 h-5 flex-shrink-0" style="color: #e6b030;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <strong class="text-white">10 votes per day</strong> for the full competition duration
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-300">
                    <svg class="w-5 h-5 flex-shrink-0" style="color: #e6b030;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Vote for <strong class="text-white">any contestant</strong> — no per-contestant limit
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-300">
                    <svg class="w-5 h-5 flex-shrink-0" style="color: #e6b030;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Active until:
                    @if($competition->end_date)
                        <strong class="text-white">{{ \Carbon\Carbon::parse($competition->end_date)->format('d M Y') }}</strong>
                    @else
                        <strong class="text-white">competition ends</strong>
                    @endif
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-300">
                    <svg class="w-5 h-5 flex-shrink-0" style="color: #e6b030;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Premium badge on your votes
                </li>
            </ul>

            @guest
            <p class="text-center text-gray-400 text-sm mb-4">Sign in to subscribe</p>
            <a href="{{ route('social.redirect', 'google') }}?intended={{ urlencode(url()->current()) }}"
               class="block w-full text-center py-4 text-sm font-bold tracking-widest uppercase hover:opacity-90 transition-opacity"
               style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
                Sign in to Continue
            </a>
            @endguest

            @auth
            <form method="POST" action="{{ route('premium.initiate', $competition->slug) }}">
                @csrf
                <button type="submit"
                        class="w-full py-4 text-sm font-bold tracking-widest uppercase hover:opacity-90 transition-opacity"
                        style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
                    Subscribe — UGX {{ number_format($price) }}
                </button>
            </form>
            @endauth
        </div>

        <p class="text-center text-xs text-gray-600">Secure payment via Pesapal. No recurring charges.</p>
        @endif

    </div>
</div>
@endsection
