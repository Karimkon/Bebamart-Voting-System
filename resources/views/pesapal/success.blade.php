@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="pt-16 sm:pt-20 min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-md w-full mx-auto px-4 text-center">
        <div class="text-6xl mb-6">🎉</div>
        <h1 class="text-3xl font-light text-white mb-3" style="font-family: 'Cormorant Garamond', serif;">Payment Successful!</h1>

        @if($order->isBoost())
        <p class="text-gray-300 mb-2">
            <strong class="text-white">{{ number_format($order->votes_count) }} votes</strong> have been added to
            <strong class="text-white">{{ $order->contestant?->full_name }}</strong>.
        </p>
        @else
        <p class="text-gray-300 mb-2">
            Your <strong class="text-white">Premium subscription</strong> for
            <strong class="text-white">{{ $order->competition?->name }}</strong> is now active.
            You can cast up to <strong class="text-white">10 votes per day</strong>.
        </p>
        @endif

        <p class="text-gray-500 text-sm mb-8">Ref: {{ $order->merchant_reference }}</p>

        <a href="{{ route('competitions.show', $order->competition?->slug) }}"
           class="inline-block px-8 py-4 text-sm font-bold tracking-widest uppercase hover:opacity-90 transition-opacity"
           style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
            Go to Competition
        </a>
    </div>
</div>
@endsection
