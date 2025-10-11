<x-vendor-layout>
    <x-slot name="title">Add New Service</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Add New Service</h2>
            <p class="text-gray-600 mt-1">Create a new service listing for your business</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6">
                    <form method="POST" action="{{ route('vendor.services.store') }}" class="space-y-6">
                        @csrf

                        <!-- Service Title -->
                        <div>
                            <x-input-label for="title" :value="__('Service Title')" />
                            <x-text-input 
                                id="title" 
                                class="block mt-1 w-full" 
                                type="text" 
                                name="title" 
                                :value="old('title')" 
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
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            >{{ old('description') }}</textarea>
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
                                <option value="fixed" {{ old('pricing_type') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="hourly" {{ old('pricing_type') == 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                <option value="package" {{ old('pricing_type') == 'package' ? 'selected' : '' }}>Package Deal</option>
                                <option value="quote" {{ old('pricing_type') == 'quote' ? 'selected' : '' }}>Contact for Quote</option>
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
                                    :value="old('price_min')"
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
                                    :value="old('price_max')"
                                    placeholder="0.00"
                                />
                                <x-input-error :messages="$errors->get('price_max')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">Optional - leave blank if not applicable</p>
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
                            <a href="{{ route('vendor.services.index') }}">
                                <x-button variant="ghost" type="button">
                                    Cancel
                                </x-button>
                            </a>
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
    </div>
</x-vendor-layout>

