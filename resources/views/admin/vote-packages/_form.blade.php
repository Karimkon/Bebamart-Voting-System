<div class="grid grid-cols-2 md:grid-cols-3 gap-4">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Package Name</label>
        <input type="text" name="name" value="{{ old('name', $pkg->name ?? '') }}" required
               class="w-full px-3 py-2 border border-gray-200 text-sm outline-none focus:border-amber-400">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Votes Count</label>
        <input type="number" name="votes_count" value="{{ old('votes_count', $pkg->votes_count ?? '') }}" min="1" required
               class="w-full px-3 py-2 border border-gray-200 text-sm outline-none focus:border-amber-400">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Price (UGX)</label>
        <input type="number" name="price" value="{{ old('price', $pkg->price ?? '') }}" min="0" step="0.01" required
               class="w-full px-3 py-2 border border-gray-200 text-sm outline-none focus:border-amber-400">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Currency</label>
        <input type="text" name="currency" value="{{ old('currency', $pkg->currency ?? 'UGX') }}"
               class="w-full px-3 py-2 border border-gray-200 text-sm outline-none focus:border-amber-400">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $pkg->sort_order ?? 0) }}" min="0"
               class="w-full px-3 py-2 border border-gray-200 text-sm outline-none focus:border-amber-400">
    </div>
    <div class="flex items-end gap-4">
        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $pkg->is_active ?? true) ? 'checked' : '' }} class="rounded">
            Active
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $pkg->is_popular ?? false) ? 'checked' : '' }} class="rounded">
            Popular
        </label>
    </div>
    <div class="col-span-2 md:col-span-3">
        <label class="block text-xs text-gray-500 mb-1">Description (optional)</label>
        <input type="text" name="description" value="{{ old('description', $pkg->description ?? '') }}"
               class="w-full px-3 py-2 border border-gray-200 text-sm outline-none focus:border-amber-400">
    </div>
</div>
