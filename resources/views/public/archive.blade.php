@extends('layouts.app')

@section('title', 'Competition Archive — Buganda Tourism Board')

@section('content')
<div class="pt-20" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h1 class="text-5xl md:text-6xl font-light text-white mb-4" style="font-family: 'Cormorant Garamond', serif;">Competition Archive</h1>
        <p class="text-gray-400 text-sm max-w-xl mx-auto">Browse all past competitions and their final results. History preserved permanently for transparency.</p>
    </div>
    <div class="h-0.5" style="background: linear-gradient(90deg, transparent, #d4941a, #e6b030, #d4941a, transparent);"></div>
</div>
<section class="py-16" style="background: #faf9f7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($competitions->isEmpty())
            <div class="text-center py-24">
                <div class="text-6xl mb-4">&#128190;</div>
                <h3 class="text-2xl font-light text-gray-700 mb-3" style="font-family: 'Cormorant Garamond', serif;">No Past Competitions Yet</h3>
                <p class="text-gray-500">Completed competitions will appear here.</p>
            </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($competitions as $competition)
            <a href="{{ route('archive.show', $competition->slug) }}" class="group block bg-white border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative h-48 overflow-hidden" style="background: linear-gradient(135deg, #1a1a4e, #0d0d2b);">
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                        <div class="text-xs text-gray-400 mb-2">{{ ucfirst(str_replace('_',' ',$competition->type)) }}</div>
                        <h3 class="text-xl font-light text-white" style="font-family: 'Cormorant Garamond', serif;">{{ $competition->name }}</h3>
                    </div>
                    <div class="absolute top-4 right-4 px-3 py-1.5 text-xs font-semibold text-white" style="background: rgba(139,92,246,0.9);">COMPLETED</div>
                </div>
                <div class="p-5">
                    <div class="flex justify-between text-sm text-gray-500 mb-2">
                        <span>{{ $competition->contestants()->count() }} contestants</span>
                        <span>{{ number_format($competition->total_votes) }} votes</span>
                    </div>
                    <div class="text-xs text-gray-400">Ended {{ \Carbon\Carbon::parse($competition->end_date)->format('d M Y') }}</div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-10">{{ $competitions->links() }}</div>
        @endif
    </div>
</section>
@endsection
