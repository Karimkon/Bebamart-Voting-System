@extends('layouts.admin')

@section('title', 'Edit ' . $contestant->full_name)
@section('page-title', 'Edit Contestant')
@section('page-subtitle', $contestant->full_name)

@section('content')

<div class="max-w-2xl">
    <a href="{{ route('admin.contestants.show', $contestant) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
        &larr; Back to {{ $contestant->full_name }}
    </a>

    <form action="{{ route('admin.contestants.update', $contestant) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Contestant Details</h3>
            <div class="space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $contestant->full_name) }}" required
                               class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] @error('full_name') border-red-400 @enderror">
                        @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                        <input type="number" name="age" value="{{ old('age', $contestant->age) }}" min="1" max="100"
                               class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contestant Number</label>
                        <input type="text" name="contestant_number" value="{{ old('contestant_number', $contestant->contestant_number) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] bg-white">
                            @foreach(['active', 'inactive', 'disqualified'] as $s)
                                <option value="{{ $s }}" {{ old('status', $contestant->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Parish / District</label>
                    <select name="parish_id" class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] bg-white">
                        <option value="">No parish assigned</option>
                        @foreach($regions as $region)
                            <optgroup label="{{ $region->name }}">
                                @foreach($region->parishes as $parish)
                                    <option value="{{ $parish->id }}" {{ old('parish_id', $contestant->parish_id) == $parish->id ? 'selected' : '' }}>
                                        {{ $parish->name }}{{ $parish->district ? ' (' . $parish->district . ')' : '' }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Photo & Biography</h3>
            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Profile Photo</label>
                    <div x-data="{ preview: '{{ $contestant->profile_photo ? asset($contestant->profile_photo) : '' }}' }">
                        <label class="cursor-pointer flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 hover:border-[#d4941a] transition-colors">
                            <input type="file" name="profile_photo" accept="image/*" class="hidden"
                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                            <img :src="preview" x-show="preview" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                            <div x-show="!preview" class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">&#128247;</div>
                            <div>
                                <div class="text-sm font-medium text-gray-700">{{ $contestant->profile_photo ? 'Change photo' : 'Upload photo' }}</div>
                                <div class="text-xs text-gray-400">JPG, PNG up to 4MB</div>
                            </div>
                        </label>
                    </div>
                    @error('profile_photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Biography</label>
                    <textarea name="biography" rows="4"
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] resize-none">{{ old('biography', $contestant->biography) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Talent / Achievement Description</label>
                    <textarea name="talent_description" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] resize-none">{{ old('talent_description', $contestant->talent_description) }}</textarea>
                </div>

            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="bg-white rounded-xl border border-red-100 p-6">
            <h3 class="font-semibold text-red-600 mb-3">Danger Zone</h3>
            <form method="POST" action="{{ route('admin.contestants.destroy', $contestant) }}"
                  onsubmit="return confirm('Remove {{ $contestant->full_name }}? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-red-600 border border-red-200 hover:bg-red-50 transition-colors">
                    Remove Contestant
                </button>
            </form>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold tracking-wide text-[#0d0d2b] hover:opacity-90 transition-all" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                Save Changes
            </button>
            <a href="{{ route('admin.contestants.show', $contestant) }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
