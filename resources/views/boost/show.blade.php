@extends('layouts.app')

@section('title', 'Boost Votes — ' . $contestant->full_name)

@section('content')
<div class="pt-16 sm:pt-20 min-h-screen" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%);">
    <div class="max-w-3xl mx-auto px-4 py-12">

        {{-- Back --}}
        <a href="{{ route('competitions.show', $competition->slug) }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-8 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to {{ $competition->name }}
        </a>

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-block text-xs px-3 py-1.5 font-semibold tracking-widest uppercase mb-4" style="background: rgba(230,176,48,0.2); color: #e6b030; border: 1px solid rgba(230,176,48,0.3);">
                Paid Vote Boost
            </div>
            <h1 class="text-4xl font-light text-white mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Boost Votes for {{ $contestant->full_name }}
            </h1>
            <p class="text-gray-400 text-sm">Choose a package or enter a custom amount. UGX {{ number_format($pricePerVote) }} per vote.</p>
        </div>

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-900/40 border border-red-500/40 text-red-300 text-sm rounded">{{ session('error') }}</div>
        @endif

        @guest
        <div class="p-6 text-center border border-amber-500/30 rounded-lg mb-8" style="background: rgba(230,176,48,0.07);">
            <p class="text-gray-300 mb-4">Sign in to purchase vote boosts</p>
            <a href="{{ route('social.redirect', 'google') }}?intended={{ urlencode(url()->current()) }}"
               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white border border-white/20 hover:bg-white/10 transition-all">
                Sign in with Google
            </a>
        </div>
        @endguest

        <div x-data="boostPage({{ $pricePerVote }})">

        {{-- Package Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            @foreach($packages as $pkg)
            <button @click="selectPackage({{ $pkg->id }}, {{ $pkg->votes_count }}, {{ $pkg->price }})"
                    :class="selectedPackageId === {{ $pkg->id }} ? 'ring-2 ring-amber-400 opacity-100' : 'opacity-80 hover:opacity-100'"
                    class="relative p-6 text-left border transition-all duration-200 rounded-lg"
                    style="background: rgba(255,255,255,0.04); border-color: rgba(230,176,48,0.25);"
                    :style="selectedPackageId === {{ $pkg->id }} ? 'border-color: #e6b030;' : ''">
                @if($pkg->is_popular)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 text-xs font-bold px-3 py-0.5 rounded-full" style="background: #e6b030; color: #0d0d2b;">POPULAR</div>
                @endif
                <div class="text-3xl font-bold text-white mb-1" style="font-family: 'Cormorant Garamond', serif;">{{ number_format($pkg->votes_count) }}</div>
                <div class="text-xs text-gray-400 uppercase tracking-widest mb-3">Votes</div>
                <div class="text-xl font-semibold" style="color: #e6b030;">UGX {{ number_format($pkg->price) }}</div>
                @if($pkg->description)
                <p class="text-xs text-gray-500 mt-2">{{ $pkg->description }}</p>
                @endif
            </button>
            @endforeach
        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-4 my-6">
            <div class="flex-1 h-px bg-white/10"></div>
            <span class="text-gray-500 text-xs uppercase tracking-widest">or custom amount</span>
            <div class="flex-1 h-px bg-white/10"></div>
        </div>

        {{-- Custom Input --}}
        <div class="p-6 border rounded-lg mb-8" style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.1);">
            <label class="block text-xs text-gray-400 uppercase tracking-widest mb-3">Number of votes</label>
            <div class="flex items-center gap-4">
                <input type="number" min="1" max="100000"
                       x-model.number="customVotes"
                       @input="selectCustom()"
                       placeholder="e.g. 250"
                       class="flex-1 px-4 py-3 text-white text-lg bg-white/5 border border-white/20 outline-none focus:border-amber-400 rounded">
                <div class="text-right">
                    <div class="text-sm text-gray-400">Total</div>
                    <div class="text-xl font-bold" style="color: #e6b030;" x-show="customVotes > 0" x-text="'UGX ' + formatNumber(customVotes * pricePerVote)"></div>
                <div class="text-lg text-gray-500" x-show="!customVotes || customVotes < 1">—</div>
                </div>
            </div>
        </div>

        {{-- Summary & Pay --}}
        <div x-show="totalVotes > 0" class="p-6 border rounded-lg mb-8" style="background: rgba(230,176,48,0.08); border-color: rgba(230,176,48,0.3);">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-300 text-sm">Contestant</span>
                <span class="text-white font-semibold">{{ $contestant->full_name }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-300 text-sm">Votes to add</span>
                <span class="text-white font-semibold" x-text="formatNumber(totalVotes) + ' votes'"></span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-amber-500/20">
                <span class="text-gray-300 text-sm font-semibold">Total</span>
                <span class="text-xl font-bold" style="color: #e6b030;" x-text="'UGX ' + formatNumber(totalAmount)"></span>
            </div>
        </div>

        @auth
        <form method="POST" action="{{ route('boost.initiate', $contestant) }}" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <input type="hidden" name="package_id" :value="selectedPackageId">
            <input type="hidden" name="custom_votes" :value="isCustom ? customVotes : null">

            <button type="submit"
                    :disabled="totalVotes < 1 || submitting"
                    class="w-full py-4 text-sm font-bold tracking-widest uppercase transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:opacity-90"
                    style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
                <span x-show="!submitting">Pay with Pesapal &rarr;</span>
                <span x-show="submitting">Redirecting to Pesapal...</span>
            </button>
        </form>
        @endauth

        @guest
        <div x-show="totalVotes > 0" class="p-5 border border-amber-500/30 text-center rounded-lg" style="background: rgba(230,176,48,0.07);">
            <p class="text-gray-200 text-sm font-semibold mb-1">Ready to boost {{ $contestant->full_name }}?</p>
            <p class="text-gray-400 text-xs mb-4">Sign in to complete your purchase.</p>
            <a href="{{ route('social.redirect', 'google') }}?intended={{ urlencode(url()->current()) }}"
               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold tracking-widest uppercase text-white hover:opacity-90 transition-all"
               style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
                Sign In &amp; Pay
            </a>
        </div>
        @endguest

        <p class="text-center text-xs text-gray-600 mt-4">Votes are applied instantly after payment confirmation. Powered by Pesapal.</p>

        </div>{{-- end x-data --}}
    </div>
</div>

@push('scripts')
<script>
function boostPage(pricePerVote) {
    return {
        pricePerVote,
        selectedPackageId: null,
        customVotes: '',
        isCustom: false,
        totalVotes: 0,
        totalAmount: 0,

        selectPackage(id, votes, price) {
            this.selectedPackageId = id;
            this.customVotes = '';
            this.isCustom = false;
            this.totalVotes = votes;
            this.totalAmount = price;
        },

        selectCustom() {
            this.selectedPackageId = null;
            this.isCustom = true;
            const v = parseInt(this.customVotes) || 0;
            this.totalVotes  = v;
            this.totalAmount = v * this.pricePerVote;
        },

        formatNumber(n) {
            return (n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
    };
}
</script>
@endpush
@endsection
