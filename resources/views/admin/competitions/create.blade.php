@extends('layouts.admin')

@section('title', 'Create Competition')
@section('page-title', 'Create Competition')
@section('page-subtitle', 'Set up a new voting competition')

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('admin.competitions.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
        &larr; Back to Competitions
    </a>

    <form action="{{ route('admin.competitions.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Basic Information</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Competition Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Miss Uganda 2025"
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors bg-white @error('type') border-red-400 @enderror">
                        <option value="">Select competition type</option>
                        <option value="beauty_pageant" {{ old('type') === 'beauty_pageant' ? 'selected' : '' }}>Beauty Pageant</option>
                        <option value="talent_show" {{ old('type') === 'talent_show' ? 'selected' : '' }}>Talent Show</option>
                        <option value="awards" {{ old('type') === 'awards' ? 'selected' : '' }}>Awards Ceremony</option>
                        <option value="tourism" {{ old('type') === 'tourism' ? 'selected' : '' }}>Tourism/Ambassador</option>
                        <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                              placeholder="Describe this competition..."
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rules (optional)</label>
                    <textarea name="rules" rows="3"
                              placeholder="Competition rules and eligibility..."
                              class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors resize-none">{{ old('rules') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5">Schedule</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors @error('start_date') border-red-400 @enderror">
                    @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors @error('end_date') border-red-400 @enderror">
                    @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Competition Config --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-1">Competition Configuration</h3>
            <p class="text-xs text-gray-400 mb-5">These settings define the structure. You can change them later.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Number of Parishes/Districts <span class="text-red-500">*</span></label>
                    <input type="number" name="number_of_parishes" value="{{ old('number_of_parishes', 53) }}" required min="1"
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                    <p class="text-xs text-gray-400 mt-1">Uganda has 146 districts, or customize for your competition</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contestants per Parish <span class="text-red-500">*</span></label>
                    <input type="number" name="contestants_per_parish" value="{{ old('contestants_per_parish', 3) }}" required min="1"
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Number of Rounds <span class="text-red-500">*</span></label>
                    <input type="number" name="number_of_rounds" value="{{ old('number_of_rounds', 3) }}" required min="1"
                           class="w-full px-4 py-2.5 border border-gray-200 text-sm focus:outline-none focus:border-[#d4941a] transition-colors">
                    <p class="text-xs text-gray-400 mt-1">e.g. 3 rounds: Parish → Regional → National</p>
                </div>
            </div>

            <div class="mt-4 p-3 bg-gray-50 border border-gray-100 text-xs text-gray-600">
                <strong>Auto-calculated:</strong>
                Total contestants = parishes × contestants per parish.
                With the values above: <strong id="totalCalc">{{ old('number_of_parishes', 53) * old('contestants_per_parish', 3) }}</strong> contestants.
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold tracking-wide text-[#0d0d2b] hover:opacity-90 transition-all" style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                Create Competition
            </button>
            <a href="{{ route('admin.competitions.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@push('scripts')
<script>
    const parishes = document.querySelector('[name="number_of_parishes"]');
    const perParish = document.querySelector('[name="contestants_per_parish"]');
    const calc = document.getElementById('totalCalc');
    function update() { calc.textContent = (parseInt(parishes.value)||0) * (parseInt(perParish.value)||0); }
    parishes.addEventListener('input', update);
    perParish.addEventListener('input', update);
</script>
@endpush

@endsection
