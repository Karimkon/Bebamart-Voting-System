@extends('layouts.admin')

@section('title', 'Contestants')
@section('page-title', 'Contestants')
@section('page-subtitle', 'All contestants across competitions')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <select onchange="this.value ? window.location.href='?competition_id='+this.value : window.location.href=''"
                class="px-3 py-1.5 border border-gray-200 text-xs bg-white focus:outline-none focus:border-[#d4941a]">
            <option value="">All Competitions</option>
            @foreach($competitions as $comp)
                <option value="{{ $comp->id }}" {{ request('competition_id') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
            @endforeach
        </select>
    </div>
    <a href="{{ route('admin.contestants.create') }}"
       class="flex items-center gap-2 px-4 py-2 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b] hover:opacity-90 transition-all"
       style="background: linear-gradient(135deg, #d4941a, #e6b030);">
        + Add Contestant
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Contestant</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">Competition</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden lg:table-cell">County / Region</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Votes</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($contestants as $contestant)
                <tr class="hover:bg-gray-50/50">
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
                                <div class="text-xs text-gray-400">{{ $contestant->age ? 'Age '.$contestant->age : '' }}{{ $contestant->contestant_number ? ' · #'.$contestant->contestant_number : '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 hidden md:table-cell">
                        <a href="{{ route('admin.competitions.show', $contestant->competition) }}" class="hover:underline" style="color: #d4941a;">
                            {{ $contestant->competition?->name ?? '—' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                        {{ $contestant->county?->name ?? '—' }}
                        @if($contestant->county?->region)
                            <span class="text-gray-400">· {{ $contestant->county->region->name }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm">{{ number_format($contestant->total_votes) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                            {{ $contestant->status === 'active' ? 'bg-green-100 text-green-700' : ($contestant->status === 'disqualified' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($contestant->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.contestants.show', $contestant) }}" class="text-xs text-[#d4941a] hover:underline">View</a>
                            <a href="{{ route('admin.contestants.edit', $contestant) }}" class="text-xs text-gray-500 hover:underline">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="text-4xl mb-3">&#127775;</div>
                        <div class="text-gray-500 text-sm mb-4">No contestants yet.</div>
                        <a href="{{ route('admin.contestants.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b]" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                            + Add Contestant
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contestants->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $contestants->links() }}</div>
    @endif
</div>

@endsection
