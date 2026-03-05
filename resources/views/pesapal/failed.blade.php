@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
<div class="pt-16 sm:pt-20 min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-md w-full mx-auto px-4 text-center">
        <div class="text-6xl mb-6">❌</div>
        <h1 class="text-3xl font-light text-white mb-3" style="font-family: 'Cormorant Garamond', serif;">Payment Not Completed</h1>
        <p class="text-gray-400 mb-8">{{ $message ?? 'Your payment was not completed. No charges were made.' }}</p>

        @if(isset($order))
        <a href="{{ $order->isBoost() ? route('boost.show', $order->contestant_id) : route('premium.show', $order->competition?->slug) }}"
           class="inline-block px-8 py-4 text-sm font-bold tracking-widest uppercase hover:opacity-90 transition-opacity border border-amber-500/50 text-amber-400 mr-3">
            Try Again
        </a>
        @endif

        <a href="{{ route('home') }}"
           class="inline-block px-8 py-4 text-sm font-bold tracking-widest uppercase hover:opacity-90 transition-opacity text-gray-400 hover:text-white">
            Back to Home
        </a>
    </div>
</div>
@endsection
