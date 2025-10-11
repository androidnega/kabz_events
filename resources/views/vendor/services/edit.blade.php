<x-vendor-layout>
    <x-slot name="title">Edit Service</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Service</h2>
            <p class="text-gray-600 mt-1">{{ $service->title }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6">
                    <form method="POST" action="{{ route('vendor.services.update', $service) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Service Title -->
                        <div>
                            <x-input-label for="title" :value="__('Service Title')" />
                            <x-text-input 
                                id="title" 
                                class="block mt-1 w-full" 
                                type="text" 
                                name="title" 
                                :value="old('title', $service->title)" 
                                required 
                                autofocus
                                placeholder="e.g., Professional Wedding Photography"
                            />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select 
                                id="category_id" 
                                name="category_id" 
                                class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm block mt-1 w-full"
                                required
                            >
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Service Description')" />
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="5"
                                class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm block mt-1 w-full"
                                placeholder="Describe your service, what's included, and what makes it special..."
                            >{{ old('description', $service->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Maximum 2000 characters</p>
                        </div>

                        <!-- Pricing Type -->
                        <div>
                            <x-input-label for="pricing_type" :value="__('Pricing Type')" />
                            <select 
                                id="pricing_type" 
                                name="pricing_type" 
                                class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm block mt-1 w-full"
                                required
                            >
                                <option value="">Select pricing type</option>
                                <option value="fixed" {{ old('pricing_type', $service->pricing_type) == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="hourly" {{ old('pricing_type', $service->pricing_type) == 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                <option value="package" {{ old('pricing_type', $service->pricing_type) == 'package' ? 'selected' : '' }}>Package Deal</option>
                                <option value="quote" {{ old('pricing_type', $service->pricing_type) == 'quote' ? 'selected' : '' }}>Contact for Quote</option>
                            </select>
                            <x-input-error :messages="$errors->get('pricing_type')" class="mt-2" />
                        </div>

                        <!-- Price Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Minimum Price -->
                            <div>
                                <x-input-label for="price_min" :value="__('Minimum Price (GH₵)')" />
                                <x-text-input 
                                    id="price_min" 
                                    class="block mt-1 w-full" 
                                    type="number" 
                                    step="0.01"
                                    min="0"
                                    name="price_min" 
                                    :value="old('price_min', $service->price_min)"
                                    placeholder="0.00"
                                />
                                <x-input-error :messages="$errors->get('price_min')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">Ghana Cedis (GHS)</p>
                            </div>

                            <!-- Maximum Price -->
                            <div>
                                <x-input-label for="price_max" :value="__('Maximum Price (GH₵)')" />
                                <x-text-input 
                                    id="price_max" 
                                    class="block mt-1 w-full" 
                                    type="number" 
                                    step="0.01"
                                    min="0"
                                    name="price_max" 
                                    :value="old('price_max', $service->price_max)"
                                    placeholder="0.00"
                                />
                                <x-input-error :messages="$errors->get('price_max')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">Must be greater than or equal to minimum price</p>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input 
                                id="is_active" 
                                name="is_active" 
                                type="checkbox" 
                                value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                            >
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Service is active and visible to clients
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-200">
                            <x-button variant="ghost" onclick="window.location='{{ route('vendor.services.index') }}'">
                                Cancel
                            </x-button>
                            <x-button type="submit" variant="primary">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Service
                            </x-button>
                        </div>
                </form>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Tips for creating great service listings:</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Use clear, descriptive titles that clients will search for</li>
                            <li>Include details about what's included in your service</li>
                            <li>Set competitive pricing based on your market research</li>
                            <li>Choose the pricing type that best fits your business model</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-vendor-layout>

