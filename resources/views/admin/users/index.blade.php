@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Manage registered voters and admins')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Users', 'value' => number_format($stats['total']), 'color' => '#1a1a4e'],
        ['label' => 'Active', 'value' => number_format($stats['active']), 'color' => '#16a34a'],
        ['label' => 'Blocked', 'value' => number_format($stats['blocked']), 'color' => '#dc2626'],
        ['label' => 'Admins', 'value' => number_format($stats['admins']), 'color' => '#d4941a'],
    ] as $s)
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <div class="text-2xl font-bold mb-1" style="font-family: 'Cormorant Garamond', serif; color: {{ $s['color'] }};">{{ $s['value'] }}</div>
        <div class="text-xs text-gray-400 tracking-wide uppercase">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…"
           class="px-3 py-2 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] rounded flex-1 min-w-48">
    <select name="role" class="px-3 py-2 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] rounded">
        <option value="">All Roles</option>
        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
    </select>
    <select name="status" class="px-3 py-2 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] rounded">
        <option value="">All Statuses</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
        <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
    </select>
    <button type="submit" class="px-4 py-2 text-xs font-semibold tracking-wide text-[#0d0d2b] rounded" style="background: linear-gradient(135deg,#d4941a,#e6b030);">Filter</button>
    @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-500 border border-gray-200 rounded hover:bg-gray-50">Clear</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">Provider</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden md:table-cell">Votes Cast</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500 hidden lg:table-cell">Last Login</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold tracking-widest uppercase text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold tracking-widest uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 {{ !$user->is_active ? 'opacity-60' : '' }}">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#1a1a4e;">{{ substr($user->name,0,1) }}</div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden md:table-cell">
                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600">{{ $user->provider ?? 'local' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @php $roleColors = ['user'=>'bg-blue-100 text-blue-700','admin'=>'bg-amber-100 text-amber-700','super_admin'=>'bg-purple-100 text-purple-700']; @endphp
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ str_replace('_',' ', ucfirst($user->role)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 hidden md:table-cell">{{ number_format($user->votes_count) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400 hidden lg:table-cell">
                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($user->is_active)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Blocked</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Change Role --}}
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline flex items-center gap-1">
                                @csrf
                                <select name="role" class="text-xs border border-gray-200 rounded px-1 py-1 focus:outline-none" onchange="this.form.submit()">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                            </form>
                            {{-- Block/Unblock --}}
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="inline"
                                  onsubmit="return confirm('{{ $user->is_active ? 'Block' : 'Unblock' }} this user?')">
                                @csrf
                                <button type="submit" class="text-xs px-2 py-1 rounded {{ $user->is_active ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }}">
                                    {{ $user->is_active ? 'Block' : 'Unblock' }}
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400 italic">You</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-4 py-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
