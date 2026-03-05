@extends('layouts.admin')

@section('title', 'Edit ' . $competition->name)
@section('page-title', 'Edit Competition')
@section('page-subtitle', $competition->name)

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('admin.competitions.show', $competition) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
        &larr; Back to {{ $competition->name }}
    </a>

    <form action="{{ route('admin.competitions.update', $competition) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Competition Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $competition->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors resize-none @error('description') border-red-400 @enderror">{{ old('description', $competition->description) }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rules</label>
                    <textarea name="rules" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors resize-none">{{ old('rules', $competition->rules) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Schedule & Status</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $competition->start_date?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $competition->end_date?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors bg-white">
                    @foreach(['draft', 'upcoming', 'active', 'completed', 'archived'] as $s)
                        <option value="{{ $s }}" {{ old('status', $competition->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Settings --}}
        @if($competition->settings)
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-1">Competition Settings</h3>
            <p class="text-xs text-gray-400 mb-5">Changing these after contestants are added may cause inconsistencies.</p>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Number of Countyes</label>
                    <input type="number" name="number_of_counties" value="{{ old('number_of_counties', $competition->settings->number_of_counties) }}" min="1"
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contestants per County</label>
                    <input type="number" name="contestants_per_county" value="{{ old('contestants_per_county', $competition->settings->contestants_per_county) }}" min="1"
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Votes per User per Day</label>
                    <input type="number" name="votes_per_user_per_day" value="{{ old('votes_per_user_per_day', $competition->settings->votes_per_user_per_day ?? 1) }}" min="1"
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                </div>
            </div>
        </div>
        @endif

        {{-- Banner Image --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-1">Banner / Poster Image</h3>
            <p class="text-xs text-gray-400 mb-4">This image appears at the top of the public competition page. Recommended: 1200×500px or wider.</p>
            <div x-data="{ preview: '{{ $competition->banner_image ? asset($competition->banner_image) : '' }}' }">
                <label class="cursor-pointer block border-2 border-dashed border-gray-200 hover:border-[#d4941a] transition-colors rounded-lg overflow-hidden">
                    <input type="file" name="banner_image" accept="image/*" class="hidden"
                           @change="preview = URL.createObjectURL($event.target.files[0])">
                    <template x-if="preview">
                        <img :src="preview" class="w-full h-48 object-cover">
                    </template>
                    <template x-if="!preview">
                        <div class="h-32 flex flex-col items-center justify-center text-gray-400 gap-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-sm">Click to upload banner image</span>
                            <span class="text-xs">JPG, PNG up to 5MB</span>
                        </div>
                    </template>
                </label>
                @if($competition->banner_image)
                <p class="text-xs text-gray-400 mt-2">Current banner shown above. Upload a new file to replace it.</p>
                @endif
            </div>
            @error('banner_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold tracking-wide text-[#0d0d2b] hover:opacity-90 transition-all" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                Save Changes
            </button>
            <a href="{{ route('admin.competitions.show', $competition) }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
