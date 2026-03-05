@extends('layouts.app')

@section('title', 'Transparency Dashboard — BebaMart Voting')

@section('content')
<div class="pt-20" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <div class="inline-flex items-center gap-3 text-xs tracking-widest uppercase mb-4 font-semibold" style="color: #d4941a;">
            <div class="w-8 h-px" style="background: #d4941a;"></div> Fully Transparent <div class="w-8 h-px" style="background: #d4941a;"></div>
        </div>
        <h1 class="text-5xl md:text-6xl font-light text-white mb-4" style="font-family: 'Cormorant Garamond', serif;">Transparency Dashboard</h1>
        <p class="text-gray-400 text-sm max-w-2xl mx-auto">Every vote is logged and publicly verifiable. BebaMart Voting System is committed to fair, transparent elections.</p>
    </div>
    <div class="h-0.5" style="background: linear-gradient(90deg, transparent, #d4941a, #e6b030, #d4941a, transparent);"></div>
</div>

<section class="py-16" style="background: #faf9f7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
            @foreach([
                ['Total Votes Cast', number_format($stats['total_votes']), 'All valid, verified votes'],
                ['Total Voters', number_format($stats['total_voters']), 'Registered via social login'],
                ['Active Competitions', $stats['active_competitions'], 'Currently running'],
                ['Flagged Votes', number_format($stats['suspicious_votes']), 'Under admin review'],
            ] as $s)
            <div class="bg-white border border-gray-100 p-6 text-center">
                <div class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif; color: #d4941a;">{{ $s[1] }}</div>
                <div class="font-semibold text-gray-800 text-sm mb-1">{{ $s[0] }}</div>
                <div class="text-xs text-gray-400">{{ $s[2] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Vote Activity Chart --}}
        <div class="bg-white border border-gray-100 p-8 mb-10">
            <h2 class="text-2xl font-light text-gray-900 mb-6" style="font-family: 'Cormorant Garamond', serif;">Daily Vote Activity (Last 30 Days)</h2>
            <canvas id="transparencyChart" height="80"></canvas>
        </div>

        {{-- How we protect integrity --}}
        <div class="bg-white border border-gray-100 p-8">
            <h2 class="text-2xl font-light text-gray-900 mb-8" style="font-family: 'Cormorant Garamond', serif;">How We Protect Voting Integrity</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach([
                    ['Social Login Required', 'Every voter must authenticate via Google, Facebook, Apple or X (Twitter). No anonymous votes are accepted.', '#d4941a'],
                    ['One Vote Per Day', 'Each voter can cast one vote per contestant per day. The system resets at midnight. This prevents bulk voting.', '#10b981'],
                    ['IP Address Monitoring', 'We track IP addresses and flag unusual patterns such as multiple votes from the same IP in short succession.', '#3b82f6'],
                    ['Device Fingerprinting', 'Browser and device characteristics are hashed and monitored to detect multiple accounts from the same device.', '#8b5cf6'],
                    ['Suspicious Vote Flagging', 'Votes that trigger our anomaly detection are flagged as suspicious and held for admin review before affecting results.', '#f59e0b'],
                    ['Full Audit Trail', 'Every vote is permanently logged with timestamp, voter ID, contestant ID, IP address, and device hash.', '#ef4444'],
                ] as $item)
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold" style="background: {{ $item[2] }};">&#10003;</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $item[0] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $item[1] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
const ctx = document.getElementById('transparencyChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($dailyVotes->pluck('date')),
        datasets: [{
            label: 'Votes',
            data: @json($dailyVotes->pluck('count')),
            backgroundColor: 'rgba(212,148,26,0.7)',
            borderColor: '#d4941a',
            borderWidth: 1,
            borderRadius: 4,
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
