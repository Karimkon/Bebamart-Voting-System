@extends('layouts.app')

@section('title', 'All Competitions — Buganda Tourism Board')

@section('content')
<div class="pt-20" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <div class="inline-flex items-center gap-3 text-xs tracking-widest uppercase mb-4 font-semibold" style="color: #d4941a;">
            <div class="w-8 h-px" style="background: #d4941a;"></div>
            All Events
            <div class="w-8 h-px" style="background: #d4941a;"></div>
        </div>
        <h1 class="text-5xl md:text-6xl font-light text-white mb-4" style="font-family: 'Cormorant Garamond', serif;">Competitions</h1>
        <p class="text-gray-400 text-sm max-w-xl mx-auto">Browse all active and upcoming voting competitions. Click to view contestants and cast your vote.</p>
    </div>
    <div class="h-0.5" style="background: linear-gradient(90deg, transparent, #d4941a, #e6b030, #d4941a, transparent);"></div>
</div>

<section class="py-16" style="background: #faf9f7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($competitions->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($competitions as $competition)
            <a href="{{ route('competitions.show', $competition->slug) }}"
               class="group block bg-white border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                <div class="relative h-56 overflow-hidden" style="background: linear-gradient(135deg, #0d0d2b, #1a1a4e);">
                    @if($competition->banner_image)
                        <img src="{{ Storage::url($competition->banner_image) }}" class="w-full h-full object-cover opacity-60 group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-6xl opacity-10" style="font-family: 'Cormorant Garamond', serif; color: #e6b030;">B</div>
                        </div>
                    @endif
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-xs tracking-widest uppercase text-gray-300 mb-3">{{ ucfirst(str_replace('_', ' ', $competition->type)) }}</div>
                        <h3 class="text-2xl font-light text-white" style="font-family: 'Cormorant Garamond', serif;">{{ $competition->name }}</h3>
                    </div>
                    <div class="absolute top-4 right-4">
                        @if($competition->status === 'active' && $competition->voting_enabled)
                            <span class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white" style="background: rgba(34,197,94,0.9);">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> VOTING LIVE
                            </span>
                        @elseif($competition->status === 'active')
                            <span class="px-3 py-1.5 text-xs font-semibold text-white" style="background: rgba(107,114,128,0.9);">ACTIVE</span>
                        @elseif($competition->status === 'upcoming')
                            <span class="px-3 py-1.5 text-xs font-semibold text-white" style="background: rgba(59,130,246,0.9);">UPCOMING</span>
                        @elseif($competition->status === 'completed')
                            <span class="px-3 py-1.5 text-xs font-semibold text-white" style="background: rgba(139,92,246,0.9);">COMPLETED</span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-2">{{ $competition->description }}</p>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-4 text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $competition->contestants_count }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ number_format($competition->total_votes) }}
                            </span>
                        </div>
                        <span class="text-xs font-semibold tracking-widest uppercase group-hover:underline" style="color: #d4941a;">View &#8594;</span>
                    </div>
                    @if($competition->end_date)
                    <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-400">
                        &#128197; Ends {{ \Carbon\Carbon::parse($competition->end_date)->format('D, d M Y') }}
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $competitions->links() }}
        </div>

        @else
        <div class="text-center py-24">
            <div class="text-6xl mb-4">&#127942;</div>
            <h3 class="text-2xl font-light text-gray-700 mb-3" style="font-family: 'Cormorant Garamond', serif;">No Competitions Yet</h3>
            <p class="text-gray-500">Check back soon. New competitions are coming soon!</p>
        </div>
        @endif
    </div>
</section>
@endsection
