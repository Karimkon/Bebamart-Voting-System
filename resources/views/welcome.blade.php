@extends('layouts.app')

@section('title', 'Buganda Tourism Board — Africa\'s Premier Voting Platform')

@section('content')

{{-- HERO --}}
<section class="relative min-h-screen-safe flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 50%, #1a1a4e 100%);">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="orb orb-1 w-80 h-80 sm:w-96 sm:h-96 opacity-[0.06]" style="background: radial-gradient(circle, #e6b030, transparent); top: 8%; left: 15%;"></div>
        <div class="orb orb-2 w-56 h-56 sm:w-72 sm:h-72 opacity-[0.05]" style="background: radial-gradient(circle, #d4941a, transparent); bottom: 18%; right: 12%;"></div>
        <div class="orb orb-3 w-40 h-40 opacity-[0.04]" style="background: radial-gradient(circle, #e6b030, transparent); top: 50%; left: 65%;"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-32 pb-20">
        <div class="reveal inline-flex items-center gap-2 px-4 py-2 mb-8 border border-white/10 text-xs tracking-widest uppercase text-gray-400" style="background: rgba(255,255,255,0.05);">
            <span style="color: #e6b030;">&#9733;</span> Presented by BebeMart <span style="color: #e6b030;">&#9733;</span>
        </div>
        <h1 class="reveal reveal-delay-1 text-5xl sm:text-6xl md:text-8xl font-light text-white leading-none tracking-tight mb-6" style="font-family: 'Cormorant Garamond', serif;">
            Vote for
            <span class="block" style="background: linear-gradient(90deg, #d4941a 0%, #f4dda0 50%, #d4941a 100%); background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: shimmer 3s linear infinite;">
                Africa's Best
            </span>
        </h1>
        <p class="reveal reveal-delay-2 text-gray-300 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed font-light">
            Africa's most transparent and secure voting platform for beauty pageants, tourism competitions, talent shows and awards.
        </p>
        <div class="reveal reveal-delay-3 flex flex-col sm:flex-row gap-4 justify-center mb-16">
            <a href="{{ route('competitions.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-sm font-semibold tracking-widest uppercase text-[#0d0d2b] hover:opacity-90 transition-all" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                &#10003; Vote Now
            </a>
            <a href="{{ route('leaderboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-sm font-semibold tracking-widest uppercase text-white border border-white/20 hover:bg-white/10 transition-all">
                Live Leaderboard
            </a>
        </div>
        @php
            $totalVotes = \App\Models\Vote::where('status','valid')->count();
            $totalVoters = \App\Models\User::where('role','user')->count();
            $activeComps = \App\Models\Competition::where('status','active')->count();
            $totalContestants = \App\Models\Contestant::where('status','active')->count();
        @endphp
        <div class="reveal reveal-delay-4 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 max-w-3xl mx-auto">
            @foreach([[$totalVotes,'Votes Cast'],[$totalVoters,'Total Voters'],[$activeComps,'Live Events'],[$totalContestants,'Contestants']] as $stat)
            <div class="px-4 py-4 border border-white/10 text-center card-touch" style="background: rgba(255,255,255,0.05);">
                <div class="text-3xl font-bold" style="font-family: 'Cormorant Garamond', serif; color: #e6b030;">{{ number_format($stat[0]) }}</div>
                <div class="text-xs text-gray-400 mt-1 tracking-widest uppercase">{{ $stat[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-gray-500">
        <span class="text-xs tracking-widest uppercase">Scroll</span>
        <div class="w-px h-12" style="background: linear-gradient(to bottom, #d4941a, transparent);"></div>
    </div>
</section>

{{-- ACTIVE COMPETITIONS --}}
@php
    $activeCompetitions = \App\Models\Competition::whereIn('status',['active','upcoming'])
        ->withCount(['contestants' => fn($q) => $q->where('status','active')])
        ->latest()->take(6)->get();
@endphp
@if($activeCompetitions->count())
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <div class="inline-flex items-center gap-3 text-xs tracking-widest uppercase mb-4 font-semibold" style="color: #d4941a;">
                <div class="w-8 h-px" style="background: #d4941a;"></div> Live Now <div class="w-8 h-px" style="background: #d4941a;"></div>
            </div>
            <h2 class="text-5xl font-light text-gray-900 mb-4" style="font-family: 'Cormorant Garamond', serif;">Active Competitions</h2>
            <p class="text-gray-500 max-w-xl mx-auto">Cast your vote and support your favourite contestant. Every vote counts.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($activeCompetitions as $competition)
            <a href="{{ route('competitions.show', $competition->slug) }}" class="group block bg-white border border-gray-100 overflow-hidden card-hover card-touch reveal">
                <div class="relative h-52 overflow-hidden" style="background: linear-gradient(135deg, #0d0d2b, #1a1a4e);">
                    @if($competition->banner_image)
                        <img src="{{ Storage::url($competition->banner_image) }}" class="w-full h-full object-cover opacity-60 group-hover:scale-110 transition-transform duration-700">
                    @endif
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-xs tracking-widest uppercase text-gray-400 mb-2">{{ ucfirst(str_replace('_',' ',$competition->type)) }}</div>
                        <h3 class="text-2xl font-light text-white" style="font-family: 'Cormorant Garamond', serif;">{{ $competition->name }}</h3>
                    </div>
                    <div class="absolute top-4 right-4">
                        @if($competition->status === 'active' && $competition->voting_enabled)
                            <span class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white" style="background: rgba(34,197,94,0.9);"><span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> VOTING LIVE</span>
                        @elseif($competition->status === 'upcoming')
                            <span class="px-3 py-1.5 text-xs font-semibold text-white" style="background: rgba(59,130,246,0.9);">UPCOMING</span>
                        @endif
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                        <span>{{ $competition->contestants_count }} contestants</span>
                        <span>{{ number_format($competition->total_votes) }} votes</span>
                    </div>
                    @if($competition->end_date)
                    <div class="text-xs text-gray-400 mb-3">Ends {{ \Carbon\Carbon::parse($competition->end_date)->format('D, d M Y') }}</div>
                    @endif
                    <div class="flex justify-end">
                        <span class="text-xs font-semibold tracking-widest uppercase group-hover:underline" style="color: #d4941a;">Vote Now &#8594;</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('competitions.index') }}" class="inline-flex items-center gap-2 px-8 py-4 text-sm font-semibold tracking-widest uppercase border-2 border-[#0d0d2b] text-[#0d0d2b] hover:bg-[#0d0d2b] hover:text-white transition-all duration-300">
                View All Competitions &#8594;
            </a>
        </div>
    </div>
</section>
@endif

{{-- HOW IT WORKS --}}
<section class="py-24" style="background: #faf9f7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <div class="inline-flex items-center gap-3 text-xs tracking-widest uppercase mb-4 font-semibold" style="color: #d4941a;">
                <div class="w-8 h-px" style="background: #d4941a;"></div> Simple Process <div class="w-8 h-px" style="background: #d4941a;"></div>
            </div>
            <h2 class="text-5xl font-light text-gray-900" style="font-family: 'Cormorant Garamond', serif;">How Voting Works</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach([['01','Browse Competitions','Explore active pageants, talent shows and awards from across Africa.'],['02','Sign In Securely','Login with Google, Facebook, X or Apple. No password needed.'],['03','Choose & Vote','Pick your favourite contestant and cast your vote. One vote per contestant per day.'],['04','Track Results','Watch live vote counts update in real time. Full transparency guaranteed.']] as $step)
            <div class="text-center reveal">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-6 text-white text-xl font-bold animate-pulse-ring" style="background: linear-gradient(135deg, #d4941a, #e6b030);">{{ $step[0] }}</div>
                <h3 class="text-xl font-medium text-gray-900 mb-3" style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem;">{{ $step[1] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TRUST SECTION --}}
<section class="py-20 text-white" style="background: linear-gradient(135deg, #0d0d2b, #1a1a4e);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-light mb-6" style="font-family: 'Cormorant Garamond', serif;">Built on Trust &amp; Transparency</h2>
        <p class="text-gray-300 max-w-2xl mx-auto mb-12 text-sm leading-relaxed">Every single vote is logged, audited, and publicly verifiable. Buganda Tourism Board is Africa's most transparent voting platform.</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 max-w-4xl mx-auto">
            @foreach([['Social Login Only','No anonymous votes'],['IP Monitoring','Fraud detection'],['Device Fingerprint','Multi-account detection'],['Full Audit Trail','Every vote logged'],['1 Vote/Day','Per contestant']] as $t)
            <div class="p-4 border border-white/10 text-center" style="background: rgba(255,255,255,0.05);">
                <div class="text-2xl mb-2" style="color: #e6b030;">&#10003;</div>
                <div class="text-sm font-semibold text-white">{{ $t[0] }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ $t[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
@keyframes shimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
</style>
@endsection
