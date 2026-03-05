@extends('layouts.admin')

@section('title', 'Revenue & Orders')
@section('page-title', 'Revenue & Orders')
@section('page-subtitle', 'All paid vote transactions')

@section('content')

{{-- Revenue Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl p-6 shadow">
        <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Total Revenue</div>
        <div class="text-2xl font-bold text-gray-900">UGX {{ number_format($totalRevenue) }}</div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow">
        <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Vote Boost Revenue</div>
        <div class="text-2xl font-bold text-gray-900">UGX {{ number_format($boostRevenue) }}</div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow">
        <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Premium Revenue</div>
        <div class="text-2xl font-bold text-gray-900">UGX {{ number_format($premiumRevenue) }}</div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl p-4 shadow mb-6 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Type</label>
        <select name="type" class="px-3 py-2 border border-gray-200 text-sm outline-none">
            <option value="">All Types</option>
            <option value="vote_boost" {{ request('type') === 'vote_boost' ? 'selected' : '' }}>Vote Boost</option>
            <option value="premium_subscription" {{ request('type') === 'premium_subscription' ? 'selected' : '' }}>Premium</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" class="px-3 py-2 border border-gray-200 text-sm outline-none">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Competition</label>
        <select name="competition_id" class="px-3 py-2 border border-gray-200 text-sm outline-none">
            <option value="">All Competitions</option>
            @foreach($competitions as $comp)
            <option value="{{ $comp->id }}" {{ request('competition_id') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="px-5 py-2 text-xs font-bold tracking-widest uppercase text-white" style="background: #0d0d2b;">Filter</button>
    <a href="{{ route('admin.vote-orders.index') }}" class="px-5 py-2 text-xs font-bold tracking-widest uppercase text-gray-600 border border-gray-200">Reset</a>
</form>

{{-- Orders Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Ref</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">User</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Type</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Contestant / Competition</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Votes</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Amount</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
            <tr>
                <td class="px-5 py-4 font-mono text-xs text-gray-500">{{ $order->merchant_reference }}</td>
                <td class="px-5 py-4 text-gray-900">{{ $order->user?->name ?? '—' }}</td>
                <td class="px-5 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->isBoost() ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $order->isBoost() ? 'Boost' : 'Premium' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-gray-600 text-xs">
                    @if($order->isBoost())
                        {{ $order->contestant?->full_name ?? '—' }}<br>
                        <span class="text-gray-400">{{ $order->competition?->name }}</span>
                    @else
                        {{ $order->competition?->name ?? '—' }}
                    @endif
                </td>
                <td class="px-5 py-4 text-gray-900 font-semibold">{{ number_format($order->votes_count) }}</td>
                <td class="px-5 py-4 font-semibold text-gray-900">UGX {{ number_format($order->amount) }}</td>
                <td class="px-5 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $order->payment_status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($order->payment_status === 'pending'   ? 'bg-yellow-100 text-yellow-700' :
                           ($order->payment_status === 'failed'    ? 'bg-red-100 text-red-700' :
                                                                     'bg-gray-100 text-gray-600')) }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td class="px-5 py-4 text-gray-500 text-xs">{{ $order->created_at->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($orders->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>

@endsection
