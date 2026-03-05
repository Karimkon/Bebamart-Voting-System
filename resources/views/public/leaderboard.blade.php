@extends('layouts.app')

@section('title', 'Live Leaderboard — Buganda Tourism Board')

@section('content')
<div class="pt-20" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <div class="inline-flex items-center gap-3 text-xs tracking-widest uppercase mb-4 font-semibold" style="color: #d4941a;">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Live Updates
        </div>
        <h1 class="text-5xl md:text-6xl font-light text-white mb-4" style="font-family: 'Cormorant Garamond', serif;">Live Leaderboard</h1>
        <p class="text-gray-400 text-sm max-w-xl mx-auto">Real-time rankings for all active competitions. Updates every 30 seconds.</p>
    </div>
    <div class="h-0.5" style="background: linear-gradient(90deg, transparent, #d4941a, #e6b030, #d4941a, transparent);"></div>
</div>

<section class="py-16" style="background: #faf9f7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($competitions->isEmpty())
        <div class="text-center py-24">
            <div class="text-6xl mb-4">&#127942;</div>
            <h3 class="text-2xl font-light text-gray-700 mb-3" style="font-family: 'Cormorant Garamond', serif;">No Active Competitions</h3>
            <p class="text-gray-500">The leaderboard will appear when voting is open.</p>
        </div>
        @endif

        @foreach($competitions as $competition)
        <div class="mb-16">
            {{-- Competition Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-3xl font-light text-gray-900" style="font-family: 'Cormorant Garamond', serif;">{{ $competition->name }}</h2>
                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        {{ $competition->contestants->count() }} contestants • {{ number_format($competition->total_votes) }} total votes
                    </div>
                </div>
                <a href="{{ route('competitions.show', $competition->slug) }}"
                   class="hidden md:inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b] hover:opacity-90 transition-all"
                   style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                    Vote Now &#8594;
                </a>
            </div>

            {{-- Top 3 Podium --}}
            @if($competition->contestants->count() >= 3)
            <div class="grid grid-cols-3 gap-4 mb-8 max-w-2xl mx-auto">
                {{-- 2nd Place --}}
                @if($competition->contestants->count() > 1)
                <div class="text-center pt-4">
                    <div class="relative inline-block mb-3">
                        @if($competition->contestants[1]->photo)
                            <img src="{{ Storage::url($competition->contestants[1]->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto border-4 border-gray-300">
                        @else
                            <div class="w-20 h-20 rounded-full mx-auto border-4 border-gray-300 flex items-center justify-center text-xl font-bold text-gray-400" style="background: #f3f4f6;">{{ substr($competition->contestants[1]->full_name, 0, 1) }}</div>
                        @endif
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background: #9ca3af;">2</div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $competition->contestants[1]->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ number_format($competition->contestants[1]->total_votes) }} votes</div>
                    <div class="h-16 mt-3" style="background: #e5e7eb;"></div>
                </div>
                @endif

                {{-- 1st Place --}}
                <div class="text-center">
                    <div class="text-3xl mb-1">&#x1F451;</div>
                    <div class="relative inline-block mb-3">
                        @if($competition->contestants[0]->photo)
                            <img src="{{ Storage::url($competition->contestants[0]->photo) }}" class="w-24 h-24 rounded-full object-cover mx-auto border-4" style="border-color: #d4941a;">
                        @else
                            <div class="w-24 h-24 rounded-full mx-auto border-4 flex items-center justify-center text-2xl font-bold" style="border-color: #d4941a; background: #faf0d7; color: #d4941a;">{{ substr($competition->contestants[0]->full_name, 0, 1) }}</div>
                        @endif
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">1</div>
                    </div>
                    <div class="text-sm font-bold text-gray-900 truncate">{{ $competition->contestants[0]->full_name }}</div>
                    <div class="text-xs font-semibold" style="color: #d4941a;">{{ number_format($competition->contestants[0]->total_votes) }} votes</div>
                    <div class="h-24 mt-3" style="background: linear-gradient(to top, #d4941a, #e6b030);"></div>
                </div>

                {{-- 3rd Place --}}
                @if($competition->contestants->count() > 2)
                <div class="text-center pt-8">
                    <div class="relative inline-block mb-3">
                        @if($competition->contestants[2]->photo)
                            <img src="{{ Storage::url($competition->contestants[2]->photo) }}" class="w-18 h-18 rounded-full object-cover mx-auto border-4 border-amber-700">
                        @else
                            <div class="w-16 h-16 rounded-full mx-auto border-4 border-amber-700 flex items-center justify-center text-lg font-bold text-amber-700" style="background: #fef3c7;">{{ substr($competition->contestants[2]->full_name, 0, 1) }}</div>
                        @endif
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background: #b45309;">3</div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $competition->contestants[2]->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ number_format($competition->contestants[2]->total_votes) }} votes</div>
                    <div class="h-10 mt-3" style="background: #b45309; opacity: 0.6;"></div>
                </div>
                @endif
            </div>
            @endif

            {{-- Full Rankings Table --}}
            <div class="bg-white border border-gray-100 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr style="background: #0d0d2b;">
                            <th class="px-6 py-4 text-left text-xs font-semibold tracking-widest uppercase text-gray-300 w-16">Rank</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold tracking-widest uppercase text-gray-300">Contestant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold tracking-widest uppercase text-gray-300 hidden sm:table-cell">County</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold tracking-widest uppercase text-gray-300">Votes</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold tracking-widest uppercase text-gray-300 hidden md:table-cell">Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($competition->contestants->take(10) as $rank => $contestant)
                        <tr class="hover:bg-amber-50/30 transition-colors {{ $rank < 3 ? 'bg-amber-50/20' : '' }}">
                            <td class="px-6 py-4">
                                @if($rank === 0)
                                    <span class="text-xl">&#x1F451;</span>
                                @elseif($rank === 1)
                                    <span class="w-8 h-8 inline-flex items-center justify-center rounded-full text-sm font-bold text-white" style="background: #9ca3af;">2</span>
                                @elseif($rank === 2)
                                    <span class="w-8 h-8 inline-flex items-center justify-center rounded-full text-sm font-bold text-white" style="background: #b45309;">3</span>
                                @else
                                    <span class="text-gray-500 font-semibold">{{ $rank + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($contestant->photo)
                                        <img src="{{ Storage::url($contestant->photo) }}" class="w-10 h-10 rounded-full object-cover border-2" style="border-color: {{ $rank === 0 ? '#d4941a' : '#e5e7eb' }};">
                                    @else
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background: #1a1a4e;">{{ substr($contestant->full_name, 0, 1) }}</div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $contestant->full_name }}</div>
                                        @if($contestant->age)<div class="text-xs text-gray-400">Age {{ $contestant->age }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell text-sm text-gray-500">{{ $contestant->county?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="font-bold text-gray-900" style="{{ $rank === 0 ? 'color: #d4941a;' : '' }}">{{ number_format($contestant->total_votes) }}</div>
                                @if($competition->total_votes > 0)
                                <div class="text-xs text-gray-400">{{ round(($contestant->total_votes / $competition->total_votes) * 100, 1) }}%</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-right">
                                <a href="{{ route('competitions.show', $competition->slug) }}"
                                   class="text-xs font-semibold tracking-widest uppercase hover:underline" style="color: #d4941a;">
                                    Vote
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($competition->contestants->count() > 10)
            <div class="text-center mt-4">
                <a href="{{ route('competitions.show', $competition->slug) }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
                    View all {{ $competition->contestants->count() }} contestants &#8594;
                </a>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
// Auto-refresh leaderboard every 30 seconds
setTimeout(() => location.reload(), 30000);
</script>
@endpush

@endsection
