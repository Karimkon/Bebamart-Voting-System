@extends('layouts.admin')

@section('title', $competition->name)
@section('page-title', $competition->name)
@section('page-subtitle', 'Competition management')

@section('content')

{{-- Top Bar --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.competitions.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
        &larr; All Competitions
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('competitions.show', $competition->slug) }}" target="_blank"
           class="px-4 py-2 text-xs font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
            View Public Page &#8599;
        </a>
        <a href="{{ route('admin.competitions.edit', $competition) }}"
           class="px-4 py-2 text-xs font-semibold border transition-colors hover:opacity-90"
           style="background: linear-gradient(135deg, #d4941a, #e6b030); border-color: transparent; color: #0d0d2b;">
            Edit Competition
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left Column: Info + Settings --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Status Card --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Status & Control</h3>

            @php
                $statusColors = [
                    'active' => 'bg-green-100 text-green-700',
                    'draft' => 'bg-gray-100 text-gray-600',
                    'upcoming' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-purple-100 text-purple-700',
                    'archived' => 'bg-gray-100 text-gray-500',
                ];
            @endphp

            <div class="flex items-center gap-2 mb-4">
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$competition->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($competition->status) }}
                </span>
                @if($competition->voting_enabled && $competition->status === 'active')
                    <span class="flex items-center gap-1 text-xs text-green-600 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Voting Live
                    </span>
                @endif
            </div>

            {{-- Toggle Voting --}}
            @if($competition->status === 'active')
            <form method="POST" action="{{ route('admin.competitions.toggle-voting', $competition) }}">
                @csrf
                <button type="submit" class="w-full py-2.5 text-xs font-semibold border transition-colors hover:opacity-90 mb-3"
                    style="{{ $competition->voting_enabled ? 'background: #fee2e2; border-color: #fca5a5; color: #dc2626;' : 'background: linear-gradient(135deg, #d4941a, #e6b030); border-color: transparent; color: #0d0d2b;' }}">
                    {{ $competition->voting_enabled ? '⏸ Pause Voting' : '▶ Enable Voting' }}
                </button>
            </form>
            @endif

            {{-- Change Status --}}
            <form method="POST" action="{{ route('admin.competitions.update', $competition) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $competition->name }}">
                <input type="hidden" name="description" value="{{ $competition->description }}">
                <input type="hidden" name="start_date" value="{{ $competition->start_date?->format('Y-m-d') }}">
                <input type="hidden" name="end_date" value="{{ $competition->end_date?->format('Y-m-d') }}">
                <div class="flex gap-2">
                    <select name="status" class="flex-1 px-3 py-2 border border-gray-200 text-xs focus:outline-none focus:border-[#d4941a] bg-white">
                        @foreach(['draft', 'upcoming', 'active', 'completed', 'archived'] as $s)
                            <option value="{{ $s }}" {{ $competition->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 text-xs font-semibold border border-gray-200 hover:bg-gray-50 transition-colors">Update</button>
                </div>
            </form>
        </div>

        {{-- Competition Details --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Type</dt>
                    <dd class="font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $competition->type ?? 'N/A')) }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Start Date</dt>
                    <dd class="font-medium text-gray-800">{{ $competition->start_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">End Date</dt>
                    <dd class="font-medium text-gray-800">{{ $competition->end_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Total Votes</dt>
                    <dd class="font-bold text-gray-900">{{ number_format($competition->total_votes) }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Contestants</dt>
                    <dd class="font-medium text-gray-800">{{ $competition->contestants->count() }}</dd>
                </div>
            </dl>
        </div>

        {{-- Settings --}}
        @if($competition->settings)
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Competition Settings</h3>
            <dl class="space-y-3">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Countyes</dt>
                    <dd class="font-medium">{{ $competition->settings->number_of_counties }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Per County</dt>
                    <dd class="font-medium">{{ $competition->settings->contestants_per_county }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Total Expected</dt>
                    <dd class="font-bold">{{ $competition->settings->number_of_counties * $competition->settings->contestants_per_county }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Votes/Day</dt>
                    <dd class="font-medium">{{ $competition->settings->votes_per_user_per_day ?? 1 }} per user</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Rounds</dt>
                    <dd class="font-medium">{{ $competition->settings->number_of_rounds }}</dd>
                </div>
            </dl>
        </div>
        @endif

        {{-- Danger Zone --}}
        <div class="bg-white rounded-xl border border-red-100 p-6">
            <h3 class="font-semibold text-red-600 mb-3">Danger Zone</h3>
            <form method="POST" action="{{ route('admin.competitions.destroy', $competition) }}"
                  onsubmit="return confirm('Delete this competition? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2 text-xs font-semibold text-red-600 border border-red-200 hover:bg-red-50 transition-colors">
                    Delete Competition
                </button>
            </form>
        </div>

    </div>

    {{-- Right Column: Contestants --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Add Contestant Button --}}
        <div class="flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Contestants ({{ $competition->contestants->count() }})</h3>
            <a href="{{ route('admin.contestants.create') }}?competition_id={{ $competition->id }}"
               class="flex items-center gap-2 px-4 py-2 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b] hover:opacity-90 transition-all"
               style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                + Add Contestant
            </a>
        </div>

        {{-- Contestants Table --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Contestant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">County</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Votes</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($competition->contestants->sortByDesc('total_votes') as $i => $contestant)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0">
                                        @if($contestant->profile_photo)
                                            <img src="{{ asset($contestant->profile_photo) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white text-xs font-bold" style="background: linear-gradient(135deg, #1a1a4e, #0d0d2b);">{{ substr($contestant->full_name, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $contestant->full_name }}</div>
                                        @if($contestant->age)
                                            <div class="text-xs text-gray-400">Age {{ $contestant->age }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $contestant->county?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 text-sm">{{ number_format($contestant->total_votes) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                    {{ $contestant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($contestant->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.contestants.edit', $contestant) }}" class="text-xs text-[#d4941a] hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.contestants.destroy', $contestant) }}"
                                          onsubmit="return confirm('Remove {{ $contestant->full_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">Remove</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">
                                No contestants yet.
                                <a href="{{ route('admin.contestants.create') }}?competition_id={{ $competition->id }}" style="color: #d4941a;" class="hover:underline ml-1">Add the first one</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
