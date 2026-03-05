@extends('layouts.admin')

@section('title', 'Competitions')
@section('page-title', 'Competitions')
@section('page-subtitle', 'Manage all competitions')

@section('content')

{{-- Top Action Bar --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        @foreach(['all' => 'All', 'active' => 'Active', 'upcoming' => 'Upcoming', 'draft' => 'Draft', 'completed' => 'Completed'] as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
           class="px-3 py-1.5 text-xs font-semibold border transition-colors
               {{ request('status', 'all') === $val ? 'text-white border-transparent' : 'text-gray-600 border-gray-200 hover:border-gray-300' }}"
           style="{{ request('status', 'all') === $val ? 'background: linear-gradient(135deg, #d4941a, #e6b030); border-color: transparent;' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
    <a href="{{ route('admin.competitions.create') }}"
       class="flex items-center gap-2 px-4 py-2 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b] hover:opacity-90 transition-all"
       style="background: linear-gradient(135deg, #d4941a, #e6b030);">
        + New Competition
    </a>
</div>

{{-- Competitions Table --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Competition</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500 hidden sm:table-cell">Contestants</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500 hidden sm:table-cell">Votes</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden lg:table-cell">Dates</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($competitions as $competition)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900 text-sm">{{ $competition->name }}</div>
                        <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $competition->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                        {{ ucfirst(str_replace('_', ' ', $competition->type ?? 'N/A')) }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-700',
                                'draft' => 'bg-gray-100 text-gray-600',
                                'upcoming' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-purple-100 text-purple-700',
                                'archived' => 'bg-gray-100 text-gray-500',
                            ];
                        @endphp
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $statusColors[$competition->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($competition->status) }}
                            </span>
                            @if($competition->voting_enabled && $competition->status === 'active')
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-gray-600 hidden sm:table-cell">
                        {{ $competition->contestants()->count() }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-700 hidden sm:table-cell">
                        {{ number_format($competition->total_votes) }}
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <div class="text-xs text-gray-500">
                            <span>{{ $competition->start_date?->format('d M Y') ?? '—' }}</span>
                            <span class="text-gray-300 mx-1">→</span>
                            <span>{{ $competition->end_date?->format('d M Y') ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.competitions.show', $competition) }}" class="px-3 py-1.5 text-xs font-semibold border transition-colors hover:bg-gray-50" style="border-color: #d4941a; color: #d4941a;">Manage</a>
                            <a href="{{ route('admin.competitions.edit', $competition) }}" class="px-3 py-1.5 text-xs font-semibold border border-gray-200 text-gray-600 transition-colors hover:bg-gray-50">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="text-4xl mb-3">&#127942;</div>
                        <div class="text-gray-500 text-sm mb-4">No competitions yet. Create your first one!</div>
                        <a href="{{ route('admin.competitions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b]" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                            + New Competition
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($competitions->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $competitions->links() }}
    </div>
    @endif
</div>

@endsection
