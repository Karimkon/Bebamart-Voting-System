@extends('layouts.app')

@section('title', $competition->name . ' — BebaMart Voting')

@section('content')
<div x-data="votingPage()" x-init="init()">

{{-- COMPETITION HERO --}}
<div class="pt-16 sm:pt-20 pb-0 text-white" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 70%, #1a1a4e 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col lg:flex-row gap-10 items-start">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-4">
                    <a href="{{ route('competitions.index') }}" class="text-gray-400 hover:text-white text-xs tracking-widest uppercase transition-colors">Competitions</a>
                    <span class="text-gray-600">/</span>
                    <span class="text-gray-400 text-xs tracking-widest uppercase">{{ $competition->name }}</span>
                </div>
                <div class="inline-block text-xs px-3 py-1.5 font-semibold tracking-widest uppercase mb-4" style="background: rgba(230,176,48,0.2); color: #e6b030; border: 1px solid rgba(230,176,48,0.3);">
                    {{ ucfirst(str_replace('_', ' ', $competition->type)) }}
                </div>
                <h1 class="text-4xl md:text-6xl font-light mb-4" style="font-family: 'Cormorant Garamond', serif;">{{ $competition->name }}</h1>
                <p class="text-gray-400 max-w-xl leading-relaxed text-sm mb-6">{{ $competition->description }}</p>
                <div class="flex flex-wrap gap-6 text-sm">
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-4 h-4" style="color: #e6b030;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span id="total-contestants">{{ $competition->contestants()->where('status','active')->count() }}</span> Contestants
                    </div>
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-4 h-4" style="color: #e6b030;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="total-votes">{{ number_format($competition->total_votes) }}</span> Votes Cast
                    </div>
                    @if($competition->end_date)
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-4 h-4" style="color: #e6b030;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Ends {{ \Carbon\Carbon::parse($competition->end_date)->format('D, d M Y') }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Countdown Timer --}}
            @if($competition->end_date && \Carbon\Carbon::parse($competition->end_date)->isFuture())
            <div class="bg-white/5 border border-white/10 p-4 sm:p-6 text-center w-full sm:min-w-64"
                 x-data="countdown('{{ \Carbon\Carbon::parse($competition->end_date)->toISOString() }}')"
                 x-init="start()">
                <div class="text-xs tracking-widest uppercase text-gray-400 mb-4">Voting Ends In</div>
                <div class="grid grid-cols-4 gap-3">
                    <div class="text-center">
                        <div class="text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif; color: #e6b030;" x-text="days">00</div>
                        <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Days</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif; color: #e6b030;" x-text="hours">00</div>
                        <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Hours</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif; color: #e6b030;" x-text="minutes">00</div>
                        <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Mins</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif; color: #e6b030;" x-text="seconds">00</div>
                        <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Secs</div>
                    </div>
                </div>
                @if($competition->voting_enabled)
                <div class="mt-4 flex items-center justify-center gap-2 text-xs text-green-400">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    VOTING IS LIVE — YOU ONLY VOTE ONCE EVERY DAY
                </div>
                @endif
                {{-- Premium upsell --}}
                <a href="{{ route('premium.show', $competition->slug) }}"
                   class="mt-3 flex items-center justify-center gap-2 text-xs font-semibold px-4 py-2 hover:opacity-90 transition-opacity"
                   style="background: rgba(230,176,48,0.15); border: 1px solid rgba(230,176,48,0.4); color: #e6b030;">
                    ⭐ Go Premium — 10 votes/day
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Gold bottom border --}}
    <div class="h-0.5" style="background: linear-gradient(90deg, transparent, #d4941a, #e6b030, #d4941a, transparent);"></div>
</div>

@if($competition->voting_enabled)
{{-- Premium upsell sticky banner — always visible below hero --}}
<div style="background: rgba(13,13,43,0.97); border-bottom: 1px solid rgba(230,176,48,0.18);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2 text-sm">
            <span>⭐</span>
            <span class="text-white font-semibold">Premium:</span>
            <span class="text-gray-400">Cast <strong class="text-amber-400">10 votes/day</strong> across any contestant — one-time UGX {{ number_format((int)env('PREMIUM_PRICE', 50000)) }}</span>
        </div>
        <a href="{{ route('premium.show', $competition->slug) }}"
           class="text-xs font-bold tracking-widest uppercase px-4 py-2 hover:opacity-90 transition-opacity flex-shrink-0"
           style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
            Unlock Premium
        </a>
    </div>
</div>
@endif

{{-- FILTERS & SEARCH --}}
<div class="bg-white border-b border-gray-100 sticky top-20 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative flex-1 max-w-xs">
                <input type="text" x-model="searchQuery" @input="filterContestants()"
                       placeholder="Search contestants..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select x-model="sortBy" @change="sortContestants()" class="px-4 py-2.5 text-sm border border-gray-200 focus:border-amber-400 outline-none">
                <option value="votes">Sort by Most Votes</option>
                <option value="name">Sort by Name</option>
                <option value="county">Sort by County</option>
            </select>
            <div class="text-sm text-gray-500">
                Showing <span x-text="filtered.length" class="font-semibold">{{ $contestants->count() }}</span> contestants
            </div>
        </div>
    </div>
</div>

{{-- CONTESTANTS GRID --}}
<section class="py-12" style="background: #faf9f7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(!$competition->voting_enabled)
        <div class="mb-8">
            {{-- Status banner --}}
            <div class="p-4 border-l-4 flex items-center gap-3 mb-6" style="background: #fff8e1; border-color: #e6b030;">
                <svg class="w-5 h-5 flex-shrink-0" style="color: #d4941a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-amber-900">
                    @if($competition->status === 'upcoming')
                        Voting has <strong>not opened yet</strong> for this competition. Browse the contestants below.
                    @elseif(in_array($competition->status, ['completed', 'archived']))
                        This competition has <strong>ended</strong>. Final results are shown below.
                    @else
                        Voting is currently <strong>closed</strong>. Browse contestants and current standings below.
                    @endif
                </p>
            </div>

            {{-- Results podium if there are contestants with votes --}}
            @if($contestants->count() > 0 && $contestants->first()->total_votes > 0)
            <div class="mb-8 p-6 rounded-none" style="background: linear-gradient(135deg, #0d0d2b, #1a1a4e);">
                <h3 class="text-white text-lg font-light text-center mb-6 tracking-widest uppercase" style="font-family: 'Cormorant Garamond', serif; color: #e6b030;">
                    {{ in_array($competition->status, ['completed', 'archived']) ? 'Final Results' : 'Current Standings' }}
                </h3>
                <div class="flex justify-center items-end gap-4">
                    @foreach($contestants->take(3) as $i => $c)
                    <div class="text-center {{ $i === 0 ? 'order-2' : ($i === 1 ? 'order-1' : 'order-3') }}">
                        <div class="text-2xl mb-1">{{ $i === 0 ? '🏆' : ($i === 1 ? '🥈' : '🥉') }}</div>
                        <div class="text-white text-sm font-semibold truncate max-w-24" style="font-family: 'Cormorant Garamond', serif;">{{ $c->full_name }}</div>
                        <div class="text-xs mt-1" style="color: #e6b030;">{{ number_format($c->total_votes) }} votes</div>
                        <div class="mt-1 text-xs text-gray-400">{{ $competition->total_votes > 0 ? round(($c->total_votes / $competition->total_votes) * 100, 1) : 0 }}%</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Contestants Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5" id="contestants-grid">
            @foreach($contestants as $index => $contestant)
            <div class="contestant-card-wrapper" data-name="{{ strtolower($contestant->full_name) }}" data-county="{{ strtolower($contestant->county->name ?? '') }}" data-votes="{{ $contestant->total_votes }}">
                <div class="contestant-card bg-white overflow-hidden cursor-pointer"
                     @click="openContestant({{ $contestant->id }})">

                    {{-- Rank Badge --}}
                    <div class="relative">
                        @if($index === 0)
                            <div class="absolute top-2 left-2 z-10 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">&#x1F451;</div>
                        @elseif($index < 3)
                            <div class="absolute top-2 left-2 z-10 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background: {{ $index === 1 ? '#9ca3af' : '#b45309' }};">#{{ $index + 1 }}</div>
                        @endif

                        {{-- Photo --}}
                        <div class="aspect-[3/4] overflow-hidden" style="background: linear-gradient(135deg, #0d0d2b, #1a1a4e);">
                            @if($contestant->photo)
                                <img src="{{ Storage::url($contestant->photo) }}" alt="{{ $contestant->full_name }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <div class="text-4xl font-bold text-white/30" style="font-family: 'Cormorant Garamond', serif;">
                                        {{ strtoupper(substr($contestant->full_name, 0, 1)) }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Vote count overlay --}}
                        <div class="absolute bottom-2 right-2 px-2 py-1 text-xs font-bold text-white" style="background: rgba(13,13,43,0.85);">
                            {{ number_format($contestant->total_votes) }} votes
                        </div>
                    </div>

                    {{-- Card Info --}}
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-900 text-sm truncate" style="font-family: 'Cormorant Garamond', serif; font-size: 1rem;">{{ $contestant->full_name }}</h3>
                        @if($contestant->county)
                            <p class="text-xs text-gray-500 truncate">{{ $contestant->county->name }}</p>
                        @endif
                        {{-- Progress bar --}}
                        @if($competition->total_votes > 0)
                        <div class="mt-2 h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000" style="background: linear-gradient(90deg, #d4941a, #e6b030); width: {{ min(100, round(($contestant->total_votes / $competition->total_votes) * 100)) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">{{ $competition->total_votes > 0 ? round(($contestant->total_votes / $competition->total_votes) * 100, 1) : 0 }}%</div>
                        @endif
                    </div>

                    {{-- Vote Button --}}
                    @if($competition->voting_enabled)
                    <button @click.stop="openContestant({{ $contestant->id }})"
                            class="w-full py-3.5 text-xs font-bold tracking-widest uppercase transition-all duration-200
                            {{ auth()->check() && auth()->user()->votes()->where('contestant_id', $contestant->id)->where('vote_date', now()->toDateString())->exists()
                                ? 'bg-green-50 text-green-600 cursor-default'
                                : 'text-white hover:opacity-90' }}"
                            style="{{ auth()->check() && auth()->user()->votes()->where('contestant_id', $contestant->id)->where('vote_date', now()->toDateString())->exists() ? '' : 'background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;' }}">
                        @if(auth()->check() && auth()->user()->votes()->where('contestant_id', $contestant->id)->where('vote_date', now()->toDateString())->exists())
                            &#10003; Voted Today
                        @else
                            Vote
                        @endif
                    </button>
                    {{-- Boost button --}}
                    <a href="{{ route('boost.show', $contestant->id) }}"
                       class="block w-full py-2 text-xs font-semibold tracking-widest uppercase text-center transition-all hover:opacity-80"
                       style="background: rgba(13,13,43,0.06); color: #d4941a; border-top: 1px solid rgba(212,148,26,0.2);">
                        &#128640; Boost
                    </a>
                    @else
                    <div class="w-full py-3 text-xs font-bold tracking-widest uppercase text-center text-gray-400 bg-gray-50">
                        Voting Closed
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CONTESTANT DETAIL MODAL --}}
<div x-show="modalOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4"
     style="background: rgba(13,13,43,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"
     @click.self="modalOpen = false">

    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         class="modal-sheet bg-white w-full sm:max-w-lg relative overflow-y-auto">

        <div class="sm:hidden flex justify-center pt-3 pb-1"><div class="w-10 h-1 rounded-full bg-gray-300"></div></div>
        <div class="h-0.5 hidden sm:block" style="background: linear-gradient(90deg, #d4941a, #e6b030, #d4941a);"></div>

        <button @click="modalOpen = false" class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300" style="min-width:44px;min-height:44px;">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <template x-if="selectedContestant">
            <div>
                {{-- Photo --}}
                <div class="relative h-64 overflow-hidden" style="background: linear-gradient(135deg, #0d0d2b, #1a1a4e);">
                    <img x-bind:src="selectedContestant.photo_url" x-bind:alt="selectedContestant.full_name"
                         class="w-full h-full object-cover opacity-80" x-show="selectedContestant.photo_url">
                    <div class="absolute inset-0 flex items-end p-6" style="background: linear-gradient(to top, rgba(13,13,43,0.8), transparent);">
                        <div>
                            <h2 class="text-3xl font-light text-white" style="font-family: 'Cormorant Garamond', serif;" x-text="selectedContestant.full_name"></h2>
                            <p class="text-gray-300 text-sm" x-text="selectedContestant.county_name"></p>
                        </div>
                    </div>
                    {{-- Rank --}}
                    <div class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold text-white" style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
                        RANK #<span x-text="selectedContestant.rank"></span>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Vote Count --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="text-center">
                            <div class="text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif; color: #d4941a;" x-text="formatNumber(selectedContestant.total_votes)"></div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">Total Votes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-800" style="font-family: 'Cormorant Garamond', serif;" x-text="selectedContestant.age + ' yrs'"></div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">Age</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm font-semibold text-gray-800" x-text="selectedContestant.region_name"></div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">Region</div>
                        </div>
                    </div>

                    {{-- Bio --}}
                    <div x-show="selectedContestant.bio" class="mb-6">
                        <h4 class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-2">About</h4>
                        <p class="text-sm text-gray-600 leading-relaxed" x-text="selectedContestant.bio"></p>
                    </div>

                    {{-- Vote / Login Buttons --}}
                    <div x-show="!isLoggedIn" class="space-y-2.5">
                        <p class="text-sm text-center text-gray-500 mb-3">Sign in to vote for <span class="font-semibold" x-text="selectedContestant.full_name"></span></p>

                        {{-- Google --}}
                        <a :href="googleLoginUrl" class="flex items-center justify-center gap-3 w-full px-5 py-3 border border-gray-200 hover:bg-gray-50 transition-all text-sm font-medium text-gray-700">
                            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                            Continue with Google
                        </a>

                        {{-- Facebook --}}
                        <a :href="facebookLoginUrl" class="flex items-center justify-center gap-3 w-full px-5 py-3 border border-gray-200 hover:bg-blue-50 transition-all text-sm font-medium text-gray-700">
                            <svg class="w-5 h-5 flex-shrink-0" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Continue with Facebook
                        </a>

                        {{-- Twitter / X --}}
                        <a :href="twitterLoginUrl" class="flex items-center justify-center gap-3 w-full px-5 py-3 bg-black hover:bg-gray-900 transition-all text-sm font-medium text-white">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            Continue with X (Twitter)
                        </a>

                    </div>

                    {{-- Already voted notification --}}
                    <div x-show="isLoggedIn && hasVotedToday" class="p-4 text-center border" style="background: #f0fdf4; border-color: #86efac;">
                        <div class="text-3xl mb-2" x-text="isPremiumUser ? '⭐' : '🎉'"></div>
                        <p class="font-semibold text-green-800 text-sm" x-text="isPremiumUser ? 'Daily Limit Reached' : 'You Already Voted Today!'"></p>
                        <p class="text-green-600 text-xs mt-1" x-text="isPremiumUser ? 'All 10 premium votes used today. Come back tomorrow!' : 'You can vote again tomorrow. Come back at midnight.'"></p>
                    </div>

                    {{-- Vote button --}}
                    <div x-show="isLoggedIn && !hasVotedToday && votingEnabled">
                        <button @click="castVote()"
                                :disabled="voting"
                                class="w-full py-4 text-sm font-bold tracking-widest uppercase transition-all duration-200 hover:opacity-90 disabled:opacity-50 active:scale-95"
                                style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
                            <span x-show="!voting">
                                <span x-show="isPremiumUser">⭐ Premium Vote — <span x-text="selectedContestant.full_name.split(' ')[0]"></span></span>
                                <span x-show="!isPremiumUser">👍 Vote for <span x-text="selectedContestant.full_name.split(' ')[0]"></span></span>
                            </span>
                            <span x-show="voting">Casting vote...</span>
                        </button>
                        <p class="text-xs text-center text-gray-400 mt-2">
                            <span x-show="isPremiumUser" x-text="'⭐ Premium: ' + dailyVotesUsed + '/10 votes used today'"></span>
                            <span x-show="!isPremiumUser">One free vote per contestant per day</span>
                        </p>
                    </div>

                    {{-- Boost CTA in modal --}}
                    <div x-show="votingEnabled" class="mt-3">
                        <a :href="boostBaseUrl + selectedContestant.id"
                           @click.stop
                           class="flex items-center justify-center gap-2 w-full py-3 text-xs font-semibold tracking-widest uppercase transition-all hover:opacity-90 border"
                           style="border-color: rgba(212,148,26,0.4); color: #d4941a; background: rgba(212,148,26,0.06);">
                            🚀 Boost — add votes instantly (UGX 1,100/vote)
                        </a>
                    </div>

                    {{-- Share --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-xs text-center text-gray-400 mb-3 tracking-widest uppercase">Share this contestant</p>
                        <div class="flex justify-center gap-3">
                            <a :href="'https://wa.me/?text=Vote for ' + selectedContestant.full_name + ' on BebaMart Voting! ' + window.location.href" target="_blank"
                               class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm hover:opacity-90 transition-opacity" style="background: #25D366;">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.531 3.487"/></svg>
                            </a>
                            <a :href="'https://twitter.com/intent/tweet?text=Vote for ' + selectedContestant.full_name + ' on BebaMart Voting!&url=' + window.location.href" target="_blank"
                               class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm hover:opacity-90 transition-opacity" style="background: #000;">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.738l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + window.location.href" target="_blank"
                               class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm hover:opacity-90 transition-opacity" style="background: #1877F2;">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- Success/Already voted notification --}}
<div x-show="voteNotification.show"
     x-transition
     class="fixed bottom-6 left-4 right-4 sm:left-auto sm:right-6 sm:w-72 z-[200] px-6 py-4 shadow-2xl text-center rounded-xl"
     :style="voteNotification.type === 'success' ? 'background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;' : 'background: #fff; border-left: 4px solid #ef4444;'">
    <p class="font-semibold text-sm" x-text="voteNotification.message"></p>
    <p class="text-xs mt-1 opacity-75" x-text="voteNotification.sub"></p>
</div>

</div>

@push('scripts')
<script>
@php
$contestantsJson = $contestants->values()->map(function($c, $i) {
    return [
        'id'          => $c->id,
        'full_name'   => $c->full_name,
        'age'         => $c->age,
        'bio'         => $c->biography,
        'total_votes' => $c->total_votes,
        'photo_url'   => $c->profile_photo ? asset($c->profile_photo) : null,
        'county_name' => $c->county?->name ?? '',
        'region_name' => $c->county?->region?->name ?? '',
        'rank'        => $i + 1,
    ];
});
@endphp
const contestants = @json($contestantsJson);

const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
const votedToday = @json(auth()->check() ? auth()->user()->votes()->where('competition_id', $competition->id)->where('vote_date', now()->toDateString())->pluck('contestant_id') : []);
const votingEnabled = {{ $competition->voting_enabled ? 'true' : 'false' }};
@php
$isPremiumUser = auth()->check() ? \App\Models\VoteOrder::where('user_id', auth()->id())
    ->where('competition_id', $competition->id)
    ->where('order_type', 'premium_subscription')
    ->where('payment_status', 'completed')
    ->where('subscription_starts_at', '<=', now())
    ->where('subscription_expires_at', '>=', now())
    ->exists() : false;
$dailyVotesUsedCount = auth()->check() ? \App\Models\Vote::where('user_id', auth()->id())
    ->where('competition_id', $competition->id)
    ->whereDate('vote_date', now()->toDateString())
    ->count() : 0;
@endphp
const isPremiumUser = {{ $isPremiumUser ? 'true' : 'false' }};
let dailyVotesUsed = {{ $dailyVotesUsedCount }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const voteBaseUrl = '{{ url('/vote') }}/';
const boostBaseUrl = '{{ url('/boost') }}/';
const currentPageUrl = encodeURIComponent(window.location.href);
const googleLoginUrl = '{{ route('social.redirect', 'google') }}?intended=' + currentPageUrl;
const facebookLoginUrl = '{{ route('social.redirect', 'facebook') }}?intended=' + currentPageUrl;
const twitterLoginUrl = '{{ route('social.redirect', 'twitter-oauth-2') }}?intended=' + currentPageUrl;

function votingPage() {
    return {
        modalOpen: false,
        selectedContestant: null,
        hasVotedToday: false,
        voting: false,
        isLoggedIn: isLoggedIn,
        votingEnabled: votingEnabled,
        isPremiumUser: isPremiumUser,
        searchQuery: '',
        sortBy: 'votes',
        filtered: contestants,
        voteNotification: { show: false, type: 'success', message: '', sub: '' },
        googleUrl: googleLoginUrl,
        facebookUrl: facebookLoginUrl,

        init() {
            this.filtered = [...contestants];
        },

        openContestant(id) {
            const c = contestants.find(c => c.id === id);
            if (!c) return;
            this.selectedContestant = c;
            if (isPremiumUser) {
                this.hasVotedToday = dailyVotesUsed >= 10;
            } else {
                this.hasVotedToday = votedToday.includes(id);
            }
            this.modalOpen = true;
        },

        async castVote() {
            if (!this.selectedContestant || this.voting) return;
            this.voting = true;
            try {
                const res = await fetch(voteBaseUrl + this.selectedContestant.id, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const data = await res.json();
                if (data.success) {
                    this.selectedContestant.total_votes = data.vote_count;
                    if (isPremiumUser) {
                        dailyVotesUsed++;
                        this.hasVotedToday = dailyVotesUsed >= 10;
                        const remaining = 10 - dailyVotesUsed;
                        const sub = remaining > 0 ? remaining + ' premium votes left today' : 'Daily limit reached. Back tomorrow!';
                        this.showNotification('success', '⭐ Premium Vote Cast!', sub);
                    } else {
                        this.hasVotedToday = true;
                        votedToday.push(this.selectedContestant.id);
                        this.showNotification('success', '🎉 Vote Cast Successfully!', 'You can vote again tomorrow.');
                    }
                    if (this.hasVotedToday) setTimeout(() => this.modalOpen = false, 2000);
                } else {
                    this.showNotification('error', data.message, '');
                }
            } catch(e) {
                this.showNotification('error', 'Something went wrong. Please try again.', '');
            }
            this.voting = false;
        },

        showNotification(type, message, sub) {
            this.voteNotification = { show: true, type, message, sub };
            setTimeout(() => this.voteNotification.show = false, 4000);
        },

        filterContestants() {
            const q = this.searchQuery.toLowerCase();
            this.filtered = contestants.filter(c =>
                c.full_name.toLowerCase().includes(q) ||
                c.county_name.toLowerCase().includes(q)
            );
        },

        sortContestants() {
            this.filtered.sort((a, b) => {
                if (this.sortBy === 'votes') return b.total_votes - a.total_votes;
                if (this.sortBy === 'name') return a.full_name.localeCompare(b.full_name);
                if (this.sortBy === 'county') return a.county_name.localeCompare(b.county_name);
                return 0;
            });
        },

        formatNumber(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); },
    };
}

function countdown(endDate) {
    return {
        days: '00', hours: '00', minutes: '00', seconds: '00',
        start() {
            const update = () => {
                const now = new Date().getTime();
                const end = new Date(endDate).getTime();
                const diff = end - now;
                if (diff <= 0) { this.days = this.hours = this.minutes = this.seconds = '00'; return; }
                this.days = String(Math.floor(diff / 86400000)).padStart(2, '0');
                this.hours = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
                this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            };
            update();
            setInterval(update, 1000);
        }
    };
}
</script>
@endpush

@endsection
