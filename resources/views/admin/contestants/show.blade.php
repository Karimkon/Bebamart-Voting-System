@extends('layouts.admin')

@section('title', $contestant->full_name)
@section('page-title', $contestant->full_name)
@section('page-subtitle', 'Contestant profile')

@section('content')

<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.competitions.show', $contestant->competition) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
        &larr; {{ $contestant->competition?->name ?? 'Back' }}
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.contestants.edit', $contestant) }}"
           class="px-4 py-2 text-xs font-semibold tracking-wide hover:opacity-90 transition-all text-[#0d0d2b]"
           style="background: linear-gradient(135deg, #d4941a, #e6b030);">
            Edit Contestant
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profile Card --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
            <div class="w-28 h-28 rounded-full overflow-hidden mx-auto mb-4 border-4" style="border-color: #d4941a40;">
                @if($contestant->profile_photo)
                    <img src="{{ asset($contestant->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $contestant->full_name }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-4xl font-bold" style="background: linear-gradient(135deg, #1a1a4e, #0d0d2b);">{{ substr($contestant->full_name, 0, 1) }}</div>
                @endif
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-1">{{ $contestant->full_name }}</h2>
            @if($contestant->contestant_number)
                <div class="text-sm font-mono text-gray-400 mb-2">#{{ $contestant->contestant_number }}</div>
            @endif
            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                {{ $contestant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ ucfirst($contestant->status) }}
            </span>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="text-3xl font-bold mb-1" style="font-family: 'Cormorant Garamond', serif; color: #d4941a;">
                    {{ number_format($contestant->total_votes) }}
                </div>
                <div class="text-xs text-gray-400">Total Votes</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Age</dt>
                    <dd class="font-medium">{{ $contestant->age ?? '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">County</dt>
                    <dd class="font-medium text-right">{{ $contestant->county?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Region</dt>
                    <dd class="font-medium">{{ $contestant->county?->region?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Competition</dt>
                    <dd class="font-medium text-right">
                        <a href="{{ route('admin.competitions.show', $contestant->competition) }}" style="color: #d4941a;" class="hover:underline">
                            {{ $contestant->competition?->name ?? '—' }}
                        </a>
                    </dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Added</dt>
                    <dd class="text-gray-400 text-xs">{{ $contestant->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        @if($contestant->biography)
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Biography</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $contestant->biography }}</p>
        </div>
        @endif
    </div>

    {{-- Vote History --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Recent Votes (Last 20)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Voter</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">IP</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($contestant->votes as $vote)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $vote->user?->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-3 text-xs text-gray-400 font-mono hidden md:table-cell">{{ $vote->ip_address }}</td>
                            <td class="px-6 py-3">
                                @if($vote->status === 'valid')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Valid</span>
                                @elseif($vote->status === 'suspicious')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Suspicious</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">{{ ucfirst($vote->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-400">{{ $vote->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No votes yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
