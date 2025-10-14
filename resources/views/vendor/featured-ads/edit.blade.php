<x-vendor-layout>
    <x-slot name="title">Edit Featured Ad</x-slot>

    <div class="mb-6">
        <a href="{{ route('vendor.featured-ads.index') }}" class="text-teal-600 hover:text-teal-700">← Back to Featured Ads</a>
    </div>

    <form action="{{ route('vendor.featured-ads.update', $featuredAd->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Featured Ad</h2>

            {{-- Headline --}}
            <div>
                <label for="headline" class="block text-sm font-semibold text-gray-700 mb-2">Headline* (max 100 characters)</label>
                <input type="text" name="headline" id="headline" maxlength="100" required
                       value="{{ old('headline', $featuredAd->headline) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                @error('headline')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description (optional)</label>
                <textarea name="description" id="description" rows="3" maxlength="500"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">{{ old('description', $featuredAd->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Custom Image --}}
            <div>
                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Update Banner Image (optional)</label>
                @if($featuredAd->image_path)
                    <img src="{{ asset('storage/' . $featuredAd->image_path) }}" alt="Current image" class="w-48 h-32 object-cover rounded mb-2">
                @endif
                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Formats: JPG, PNG</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Buttons --}}
            <div class="flex gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Update Featured Ad
                </button>
                <a href="{{ route('vendor.featured-ads.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</x-vendor-layout>

