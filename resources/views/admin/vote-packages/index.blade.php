@extends('layouts.admin')

@section('title', 'Vote Packages')
@section('page-title', 'Vote Packages')
@section('page-subtitle', 'Manage preset vote boost packages')

@section('content')
<div x-data="{ showForm: false, editPackage: null }">

    {{-- Add Package Button --}}
    <div class="flex justify-end mb-6">
        <button @click="showForm = !showForm; editPackage = null"
                class="px-5 py-2.5 text-xs font-bold tracking-widest uppercase text-white hover:opacity-90 transition-opacity"
                style="background: linear-gradient(135deg, #d4941a, #e6b030); color: #0d0d2b;">
            + New Package
        </button>
    </div>

    {{-- Create Form --}}
    <div x-show="showForm && !editPackage" x-transition class="bg-white rounded-xl p-6 shadow mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Create Package</h3>
        <form method="POST" action="{{ route('admin.vote-packages.store') }}">
            @csrf
            @include('admin.vote-packages._form')
            <button type="submit" class="mt-4 px-6 py-2 text-xs font-bold tracking-widest uppercase text-white" style="background: #0d0d2b;">Save Package</button>
        </form>
    </div>

    {{-- Packages Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Votes</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Price</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Popular</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">Order</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($packages as $pkg)
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $pkg->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ number_format($pkg->votes_count) }}</td>
                    <td class="px-6 py-4 text-gray-900 font-semibold">UGX {{ number_format($pkg->price) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $pkg->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $pkg->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $pkg->is_popular ? '⭐' : '—' }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $pkg->sort_order }}</td>
                    <td class="px-6 py-4 text-right">
                        <button @click="editPackage = {{ $pkg->toJson() }}; showForm = true"
                                class="text-amber-600 hover:text-amber-800 text-xs font-semibold mr-3">Edit</button>
                        <form method="POST" action="{{ route('admin.vote-packages.destroy', $pkg) }}" class="inline"
                              onsubmit="return confirm('Delete this package?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>

                {{-- Inline Edit Row --}}
                <tr x-show="editPackage && editPackage.id === {{ $pkg->id }}" x-transition>
                    <td colspan="7" class="px-6 py-4 bg-amber-50">
                        <form method="POST" action="{{ route('admin.vote-packages.update', $pkg) }}">
                            @csrf @method('PUT')
                            @include('admin.vote-packages._form', ['pkg' => $pkg])
                            <div class="flex gap-3 mt-4">
                                <button type="submit" class="px-5 py-2 text-xs font-bold tracking-widest uppercase text-white" style="background: #0d0d2b;">Update</button>
                                <button type="button" @click="editPackage = null" class="px-5 py-2 text-xs font-bold tracking-widest uppercase text-gray-600 border border-gray-300">Cancel</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No packages yet. Create one above.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
