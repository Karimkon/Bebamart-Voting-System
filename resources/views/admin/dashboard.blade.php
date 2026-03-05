@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Buganda Tourism Board overview')

@section('content')
@php
    use App\Models\Competition;
    use App\Models\Contestant;
    use App\Models\Vote;
    use App\Models\User;
    use App\Models\VoteLog;

    $stats = [
        'total_votes' => Vote::where('status', 'valid')->count(),
        'suspicious_votes' => Vote::where('status', 'suspicious')->count(),
        'total_voters' => User::where('role', 'user')->count(),
        'total_contestants' => Contestant::where('status', 'active')->count(),
        'active_competitions' => Competition::where('status', 'active')->count(),
        'total_competitions' => Competition::count(),
    ];

    $todayVotes = Vote::whereDate('created_at', today())->where('status', 'valid')->count();
    $yesterdayVotes = Vote::whereDate('created_at', now()->subDay()->toDateString())->where('status', 'valid')->count();
    $voteGrowth = $yesterdayVotes > 0 ? round((($todayVotes - $yesterdayVotes) / $yesterdayVotes) * 100) : 0;

    $recentVotes = Vote::with(['user', 'contestant'])->latest()->take(10)->get();
    $activeCompetitions = Competition::where('status', 'active')->with(['contestants' => fn($q) => $q->orderBy('total_votes', 'desc')->limit(5)])->get();

    $dailyVotesData = Vote::where('status', 'valid')
        ->where('created_at', '>=', now()->subDays(14))
        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->groupBy('date')->orderBy('date')->get();
@endphp

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @foreach([
        ['Total Votes', number_format($stats['total_votes']), 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', '#d4941a', 'Today: ' . number_format($todayVotes)],
        ['Active Events', $stats['active_competitions'] . ' / ' . $stats['total_competitions'], 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', '#3b82f6', 'Total competitions'],
        ['Total Voters', number_format($stats['total_voters']), 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', '#10b981', 'Registered users'],
        ['Flagged Votes', number_format($stats['suspicious_votes']), 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', '#ef4444', 'Needs review'],
    ] as $stat)
    <div class="bg-white rounded-xl border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: {{ $stat[3] }}20;">
                <svg class="w-5 h-5" style="color: {{ $stat[3] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat[1] === $stat[1] ? $stat[2] : '' }}"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1" style="font-family: 'Cormorant Garamond', serif;">{{ $stat[1] }}</div>
        <div class="text-sm font-medium text-gray-600">{{ $stat[0] }}</div>
        <div class="text-xs text-gray-400 mt-1">{{ $stat[4] }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Vote Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-gray-900">Vote Activity (Last 14 Days)</h3>
            <span class="text-xs px-2 py-1 rounded {{ $voteGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $voteGrowth >= 0 ? '+' : '' }}{{ $voteGrowth }}% vs yesterday
            </span>
        </div>
        <canvas id="voteChart" height="100"></canvas>
    </div>

    {{-- Active Competitions Status --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Active Competitions</h3>
        @if($activeCompetitions->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">No active competitions</p>
        @else
            <div class="space-y-4">
                @foreach($activeCompetitions as $comp)
                <div class="p-3 rounded-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-800 truncate">{{ $comp->name }}</span>
                        @if($comp->voting_enabled)
                            <span class="flex items-center gap-1 text-xs text-green-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
                            </span>
                        @else
                            <span class="text-xs text-gray-400">Paused</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mb-2">{{ number_format($comp->total_votes) }} votes</div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.competitions.show', $comp) }}" class="flex-1 text-center py-1.5 text-xs font-semibold border transition-colors hover:bg-gray-50" style="border-color: #d4941a; color: #d4941a;">Manage</a>
                        <form method="POST" action="{{ route('admin.competitions.toggle-voting', $comp) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-1.5 text-xs font-semibold border transition-colors hover:opacity-90" style="{{ $comp->voting_enabled ? 'background: #fee2e2; border-color: #fca5a5; color: #dc2626;' : 'background: linear-gradient(135deg, #d4941a, #e6b030); border-color: transparent; color: #0d0d2b;' }}">
                                {{ $comp->voting_enabled ? 'Pause' : 'Enable' }}
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
        <a href="{{ route('admin.competitions.create') }}" class="mt-4 flex items-center justify-center gap-2 w-full py-2.5 text-xs font-semibold tracking-widest uppercase text-[#0d0d2b] hover:opacity-90 transition-all" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
            + New Competition
        </a>
    </div>
</div>

{{-- Recent Votes Table --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Recent Votes</h3>
        <span class="text-xs text-gray-400">Last 10 votes</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Voter</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Contestant</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentVotes as $vote)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($vote->user?->avatar)
                                <img src="{{ $vote->user->avatar }}" class="w-7 h-7 rounded-full object-cover">
                            @else
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs text-white font-bold" style="background: #1a1a4e;">{{ substr($vote->user?->name ?? '?', 0, 1) }}</div>
                            @endif
                            <span class="text-sm text-gray-700">{{ $vote->user?->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $vote->contestant?->full_name ?? '—' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-400 hidden md:table-cell font-mono">{{ $vote->ip_address }}</td>
                    <td class="px-6 py-3">
                        @if($vote->status === 'valid')
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Valid</span>
                        @elseif($vote->status === 'suspicious')
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">&#9888; Suspicious</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">{{ ucfirst($vote->status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-400 hidden md:table-cell">{{ $vote->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No votes yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
const ctx = document.getElementById('voteChart');
const labels = @json($dailyVotesData->pluck('date'));
const data = @json($dailyVotesData->pluck('count'));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Votes',
            data: data,
            borderColor: '#d4941a',
            backgroundColor: 'rgba(212,148,26,0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#d4941a',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush

@endsection
