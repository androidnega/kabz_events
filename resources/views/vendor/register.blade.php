<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Become a Vendor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Register Your Business</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Create your vendor profile to start offering services on KABZS EVENT platform.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('vendor.store') }}" class="space-y-6">
                        @csrf

                        <!-- Business Name -->
                        <div>
                            <x-input-label for="business_name" :value="__('Business Name')" class="required" />
                            <x-text-input 
                                id="business_name" 
                                class="block mt-1 w-full" 
                                type="text" 
                                name="business_name" 
                                :value="old('business_name')" 
                                required 
                                autofocus 
                                placeholder="Enter your business name"
                            />
                            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Business Description')" />
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="4"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                placeholder="Describe your business and services..."
                            >{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Maximum 2000 characters</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" class="required" />
                            <x-text-input 
                                id="phone" 
                                class="block mt-1 w-full" 
                                type="tel" 
                                name="phone" 
                                :value="old('phone')" 
                                required
                                placeholder="+233 XX XXX XXXX or 0XX XXX XXXX"
                            />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- WhatsApp -->
                        <div>
                            <x-input-label for="whatsapp" :value="__('WhatsApp Number (Optional)')" />
                            <x-text-input 
                                id="whatsapp" 
                                class="block mt-1 w-full" 
                                type="tel" 
                                name="whatsapp" 
                                :value="old('whatsapp')"
                                placeholder="+233 XX XXX XXXX or 0XX XXX XXXX"
                            />
                            <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                        </div>

                        <!-- Website -->
                        <div>
                            <x-input-label for="website" :value="__('Website (Optional)')" />
                            <x-text-input 
                                id="website" 
                                class="block mt-1 w-full" 
                                type="url" 
                                name="website" 
                                :value="old('website')"
                                placeholder="https://www.yourbusiness.com"
                            />
                            <x-input-error :messages="$errors->get('website')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('Business Address (Optional)')" />
                            <x-text-input 
                                id="address" 
                                class="block mt-1 w-full" 
                                type="text" 
                                name="address" 
                                :value="old('address')"
                                placeholder="Street, City, Province"
                            />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Location Coordinates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Latitude -->
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude (Optional)')" />
                                <x-text-input 
                                    id="latitude" 
                                    class="block mt-1 w-full" 
                                    type="number" 
                                    step="0.0000001"
                                    name="latitude" 
                                    :value="old('latitude')"
                                    placeholder="14.5995"
                                />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>

                            <!-- Longitude -->
                            <div>
                                <x-input-label for="longitude" :value="__('Longitude (Optional)')" />
                                <x-text-input 
                                    id="longitude" 
                                    class="block mt-1 w-full" 
                                    type="number" 
                                    step="0.0000001"
                                    name="longitude" 
                                    :value="old('longitude')"
                                    placeholder="120.9842"
                                />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Register as Vendor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

