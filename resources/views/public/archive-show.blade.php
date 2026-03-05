@extends('layouts.app')

@section('title', $competition->name . ' — Results — Buganda Tourism Board')

@section('content')
<div class="pt-20" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-4">
            <a href="{{ route('archive') }}" class="text-xs tracking-widest uppercase font-semibold hover:opacity-80 transition-opacity" style="color: #d4941a;">&larr; Back to Archive</a>
        </div>
        <div class="text-center">
            <div class="text-xs tracking-widest uppercase font-semibold mb-4" style="color: #d4941a;">
                {{ ucfirst(str_replace('_', ' ', $competition->type)) }}
            </div>
            <h1 class="text-5xl md:text-6xl font-light text-white mb-4" style="font-family: 'Cormorant Garamond', serif;">{{ $competition->name }}</h1>
            <div class="flex items-center justify-center gap-6 text-sm text-gray-400">
                <span>{{ $contestants->count() }} contestants</span>
                <span class="w-1 h-1 rounded-full bg-gray-600"></span>
                <span>{{ number_format($competition->total_votes) }} total votes</span>
                <span class="w-1 h-1 rounded-full bg-gray-600"></span>
                <span>Ended {{ $competition->end_date->format('d M Y') }}</span>
            </div>
        </div>
    </div>
    <div class="h-0.5" style="background: linear-gradient(90deg, transparent, #d4941a, #e6b030, #d4941a, transparent);"></div>
</div>

<section class="py-16" style="background: #faf9f7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Podium (top 3) --}}
        @if($contestants->count() >= 3)
        <div class="mb-16">
            <h2 class="text-2xl font-light text-center text-gray-900 mb-10" style="font-family: 'Cormorant Garamond', serif;">Final Results</h2>
            <div class="flex items-end justify-center gap-4 max-w-2xl mx-auto">

                {{-- 2nd Place --}}
                @php $second = $contestants->get(1); @endphp
                <div class="flex-1 text-center">
                    <div class="mx-auto w-20 h-20 rounded-full overflow-hidden border-4 mb-3" style="border-color: #9ca3af;">
                        @if($second->profile_photo)
                            <img src="{{ asset($second->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $second->full_name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white text-2xl font-bold" style="background: linear-gradient(135deg, #1a1a4e, #0d0d2b);">{{ substr($second->full_name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="text-2xl font-bold text-gray-400 mb-1">2nd</div>
                    <div class="font-semibold text-gray-800 text-sm mb-1">{{ $second->full_name }}</div>
                    <div class="text-xs text-gray-400 mb-3">{{ number_format($second->total_votes) }} votes</div>
                    <div class="h-24 flex items-end justify-center bg-gray-100 border border-gray-200">
                        <div class="text-xs font-bold text-gray-500 py-2">SILVER</div>
                    </div>
                </div>

                {{-- 1st Place --}}
                @php $first = $contestants->first(); @endphp
                <div class="flex-1 text-center">
                    <div class="text-2xl mb-1">&#127881;</div>
                    <div class="mx-auto w-24 h-24 rounded-full overflow-hidden border-4 mb-3" style="border-color: #d4941a;">
                        @if($first->profile_photo)
                            <img src="{{ asset($first->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $first->full_name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white text-2xl font-bold" style="background: linear-gradient(135deg, #d4941a, #e6b030);">{{ substr($first->full_name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="text-2xl font-bold mb-1" style="color: #d4941a;">1st</div>
                    <div class="font-semibold text-gray-900 text-sm mb-1">{{ $first->full_name }}</div>
                    <div class="text-xs text-gray-500 mb-3">{{ number_format($first->total_votes) }} votes</div>
                    <div class="h-36 flex items-end justify-center border" style="background: linear-gradient(135deg, rgba(212,148,26,0.1), rgba(230,176,48,0.05)); border-color: #d4941a40;">
                        <div class="text-xs font-bold py-2" style="color: #d4941a;">WINNER</div>
                    </div>
                </div>

                {{-- 3rd Place --}}
                @php $third = $contestants->get(2); @endphp
                <div class="flex-1 text-center">
                    <div class="mx-auto w-18 h-18 w-16 h-16 rounded-full overflow-hidden border-4 mb-3" style="border-color: #cd7f32;">
                        @if($third->profile_photo)
                            <img src="{{ asset($third->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $third->full_name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white text-xl font-bold" style="background: linear-gradient(135deg, #1a1a4e, #0d0d2b);">{{ substr($third->full_name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="text-2xl font-bold mb-1" style="color: #cd7f32;">3rd</div>
                    <div class="font-semibold text-gray-800 text-sm mb-1">{{ $third->full_name }}</div>
                    <div class="text-xs text-gray-400 mb-3">{{ number_format($third->total_votes) }} votes</div>
                    <div class="h-16 flex items-end justify-center bg-orange-50 border border-orange-200">
                        <div class="text-xs font-bold text-orange-600 py-2">BRONZE</div>
                    </div>
                </div>

            </div>
        </div>
        @endif

        {{-- Full Results Table --}}
        <div class="bg-white border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">All Results</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Contestant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">County/Region</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Votes</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500 hidden sm:table-cell">Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $totalVotes = $contestants->sum('total_votes'); @endphp
                        @foreach($contestants as $i => $contestant)
                        <tr class="{{ $i < 3 ? 'bg-amber-50/30' : 'hover:bg-gray-50/50' }}">
                            <td class="px-6 py-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                    {{ $i === 0 ? 'text-white' : ($i === 1 ? 'text-white' : ($i === 2 ? 'text-white' : 'text-gray-500 bg-gray-100')) }}"
                                    style="{{ $i === 0 ? 'background: linear-gradient(135deg, #d4941a, #e6b030);' : ($i === 1 ? 'background: #9ca3af;' : ($i === 2 ? 'background: #cd7f32;' : '')) }}">
                                    {{ $i + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                        @if($contestant->profile_photo)
                                            <img src="{{ asset($contestant->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $contestant->full_name }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white text-sm font-bold" style="background: linear-gradient(135deg, #1a1a4e, #0d0d2b);">{{ substr($contestant->full_name, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $contestant->full_name }}</div>
                                        @if($contestant->age)
                                            <div class="text-xs text-gray-400">Age {{ $contestant->age }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                                {{ $contestant->county?->name ?? '—' }}
                                @if($contestant->county?->region)
                                    <span class="text-gray-400">· {{ $contestant->county->region->name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-gray-900">{{ number_format($contestant->total_votes) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-400 hidden sm:table-cell">
                                {{ $totalVotes > 0 ? number_format(($contestant->total_votes / $totalVotes) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection
