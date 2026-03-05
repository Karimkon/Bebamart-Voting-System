@extends('layouts.app')

@section('title', 'Sign In — Buganda Tourism Board')

@section('content')
<div class="min-h-screen flex items-center justify-center py-20 px-4" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-10">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 h-20 rounded-full object-cover">
                <div class="text-3xl font-light text-white" style="font-family: 'Cormorant Garamond', serif;">
                    Buganda <span style="color: #d4941a;">Tourism</span>
                </div>
            </a>
            <p class="text-gray-400 text-sm mt-2">Sign in to cast your votes</p>
        </div>

        {{-- Card --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 p-8">
            <h2 class="text-xl font-light text-white text-center mb-2" style="font-family: 'Cormorant Garamond', serif;">Welcome Back</h2>
            <p class="text-gray-400 text-xs text-center mb-8">Choose your preferred sign-in method</p>

            <div class="space-y-3">
                {{-- Google --}}
                <a href="{{ route('social.redirect', 'google') }}"
                   class="flex items-center gap-4 w-full px-5 py-3.5 bg-white hover:bg-gray-50 transition-colors text-gray-800 font-medium text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                </a>

                {{-- Facebook --}}
                <a href="{{ route('social.redirect', 'facebook') }}"
                   class="flex items-center gap-4 w-full px-5 py-3.5 transition-colors font-medium text-sm text-white"
                   style="background: #1877F2;" onmouseover="this.style.background='#166FE5'" onmouseout="this.style.background='#1877F2'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Continue with Facebook
                </a>

                {{-- Twitter / X --}}
                <a href="{{ route('social.redirect', 'twitter-oauth-2') }}"
                   class="flex items-center gap-4 w-full px-5 py-3.5 transition-colors font-medium text-sm text-white bg-black hover:bg-gray-900">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    Continue with X (Twitter)
                </a>

                {{-- Apple --}}
            </div>

            <div class="mt-8 pt-6 border-t border-white/10 text-center">
                <p class="text-xs text-gray-500">
                    By signing in, you agree to our
                    <a href="#" class="underline" style="color: #d4941a;">Terms of Service</a>
                    and confirm you are eligible to vote.
                </p>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-gray-500 text-sm hover:text-gray-300 transition-colors">&larr; Back to Home</a>
        </div>
    </div>
</div>
@endsection
