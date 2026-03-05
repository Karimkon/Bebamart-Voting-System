@extends('layouts.admin')

@section('title', 'Add Contestant')
@section('page-title', 'Add Contestant')
@section('page-subtitle', 'Register a new contestant')

@section('content')

<div class="max-w-2xl">
    <a href="{{ route('admin.contestants.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
        &larr; Back to Contestants
    </a>

    <form action="{{ route('admin.contestants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Competition & Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Contestant Details</h3>
            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Competition <span class="text-red-500">*</span></label>
                    <select name="competition_id" required class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] bg-white @error('competition_id') border-red-400 @enderror">
                        <option value="">Select competition</option>
                        @foreach($competitions as $comp)
                            <option value="{{ $comp->id }}" {{ (old('competition_id', $selectedCompetitionId) == $comp->id) ? 'selected' : '' }}>{{ $comp->name }}</option>
                        @endforeach
                    </select>
                    @error('competition_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                               placeholder="e.g. Amara Nalubega"
                               class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] @error('full_name') border-red-400 @enderror">
                        @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                        <input type="number" name="age" value="{{ old('age') }}" min="1" max="100"
                               placeholder="e.g. 22"
                               class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Contestant Number
                            <span class="text-xs font-normal text-gray-400 ml-1">(auto-assigned if blank)</span>
                        </label>
                        <input type="text" name="contestant_number" value="{{ old('contestant_number') }}"
                               placeholder="Leave blank to auto-generate"
                               class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] bg-white">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">County / District</label>
                    <select name="county_id" id="county_select" class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] bg-white">
                        <option value="">No county assigned</option>
                        @foreach($regions as $region)
                            <optgroup label="{{ $region->name }}">
                                @foreach($region->counties as $county)
                                    <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>
                                        {{ $county->name }}{{ $county->district ? ' (' . $county->district . ')' : '' }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @if($regions->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">No regions/counties yet. <a href="#" class="underline">Create them first</a> or leave blank.</p>
                    @endif
                </div>

            </div>
        </div>

        {{-- Photo & Bio --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Photo & Biography</h3>
            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Profile Photo</label>
                    <div x-data="{ preview: null }">
                        <label class="cursor-pointer flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 hover:border-[#d4941a] transition-colors">
                            <input type="file" name="profile_photo" accept="image/*"
                                   class="hidden"
                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex-shrink-0" x-show="!preview">
                                <div class="w-full h-full flex items-center justify-center text-gray-400">&#128247;</div>
                            </div>
                            <img :src="preview" x-show="preview" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                            <div>
                                <div class="text-sm font-medium text-gray-700">Click to upload photo</div>
                                <div class="text-xs text-gray-400">JPG, PNG up to 4MB. Recommended: 400×400px square</div>
                            </div>
                        </label>
                    </div>
                    @error('profile_photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Biography</label>
                    <textarea name="biography" rows="4" placeholder="Brief introduction about the contestant..."
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] resize-none">{{ old('biography') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Talent / Achievement Description</label>
                    <textarea name="talent_description" rows="3" placeholder="Describe their talent or achievement..."
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] resize-none">{{ old('talent_description') }}</textarea>
                </div>

            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold tracking-wide text-[#0d0d2b] hover:opacity-90 transition-all" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                Add Contestant
            </button>
            <a href="{{ route('admin.contestants.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
