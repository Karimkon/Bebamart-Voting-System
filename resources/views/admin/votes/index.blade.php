@extends('layouts.admin')

@section('title', 'Votes Management')
@section('page-title', 'Votes')
@section('page-subtitle', 'Monitor and manage all votes')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Votes', 'value' => number_format($stats['total']), 'color' => '#1a1a4e'],
        ['label' => 'Valid', 'value' => number_format($stats['valid']), 'color' => '#16a34a'],
        ['label' => 'Suspicious', 'value' => number_format($stats['suspicious']), 'color' => '#d97706'],
        ['label' => 'Flagged', 'value' => number_format($stats['flagged']), 'color' => '#dc2626'],
    ] as $s)
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <div class="text-2xl font-bold mb-1" style="font-family: 'Cormorant Garamond', serif; color: {{ $s['color'] }};">{{ $s['value'] }}</div>
        <div class="text-xs text-gray-400 tracking-wide uppercase">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-6 flex flex-wrap gap-3">
    <select name="competition_id" class="px-3 py-2 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] rounded">
        <option value="">All Competitions</option>
        @foreach($competitions as $c)
            <option value="{{ $c->id }}" {{ request('competition_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
    <select name="status" class="px-3 py-2 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] rounded">
        <option value="">All Statuses</option>
        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid</option>
        <option value="suspicious" {{ request('status') === 'suspicious' ? 'selected' : '' }}>Suspicious</option>
        <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Flagged</option>
        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search voter name/email…"
           class="px-3 py-2 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] rounded flex-1 min-w-48">
    <button type="submit" class="px-4 py-2 text-xs font-semibold tracking-wide text-[#0d0d2b] rounded" style="background: linear-gradient(135deg,#d4941a,#e6b030);">Filter</button>
    @if(request()->hasAny(['competition_id','status','search']))
        <a href="{{ route('admin.votes.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-500 border border-gray-200 rounded hover:bg-gray-50">Clear</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Voter</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">Contestant</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden lg:table-cell">Competition</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden lg:table-cell">IP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">Time</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($votes as $vote)
                <tr class="hover:bg-gray-50/50 {{ $vote->status !== 'valid' ? 'bg-red-50/30' : '' }}">
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-800">{{ $vote->user?->name ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-400">{{ $vote->user?->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 hidden md:table-cell">
                        <a href="{{ route('admin.contestants.show', $vote->contestant_id) }}" style="color:#d4941a" class="hover:underline">
                            {{ $vote->contestant?->full_name ?? '—' }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden lg:table-cell">{{ $vote->competition?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-400 hidden lg:table-cell">{{ $vote->ip_address }}</td>
                    <td class="px-4 py-3">
                        @php
                            $colors = ['valid'=>'bg-green-100 text-green-700','suspicious'=>'bg-amber-100 text-amber-700','flagged'=>'bg-red-100 text-red-700','rejected'=>'bg-gray-100 text-gray-500'];
                        @endphp
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $colors[$vote->status] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($vote->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400 hidden md:table-cell">{{ $vote->voted_at->diffForHumans() }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Quick status change --}}
                            <form method="POST" action="{{ route('admin.votes.status', $vote) }}" class="inline">
                                @csrf
                                @if($vote->status === 'valid')
                                    <input type="hidden" name="status" value="suspicious">
                                    <button type="submit" class="text-xs px-2 py-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200">Flag</button>
                                @else
                                    <input type="hidden" name="status" value="valid">
                                    <button type="submit" class="text-xs px-2 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200">Restore</button>
                                @endif
                            </form>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.votes.destroy', $vote) }}" class="inline"
                                  onsubmit="return confirm('Delete this vote? Contestant count will be recalculated.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs px-2 py-1 rounded bg-red-100 text-red-600 hover:bg-red-200">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No votes found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($votes->hasPages())
    <div class="px-4 py-4 border-t border-gray-100">
        {{ $votes->links() }}
    </div>
    @endif
</div>

@endsection
