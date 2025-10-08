<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        {{-- Multi-Step Form --}}
        <div x-data="registrationForm()" x-init="currentStep = 1" x-cloak class="w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-200" style="min-height: 600px;">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-lg mr-3">
                            <i class="fas fa-store text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-white">Become a Vendor</h2>
                            <p class="text-sm text-indigo-100">Join Ghana's leading event services marketplace</p>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-4 sm:px-6 lg:px-8 py-4 border-b border-purple-100">
                    <div class="max-w-3xl mx-auto">
                        {{-- Progress Steps --}}
                        <div class="relative">
                            <!-- Step circles and connectors -->
                            <div class="flex items-center justify-center">
                                <!-- Step 1 -->
                                <div class="flex flex-col items-center">
                                    <div 
                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-md border-2 z-10"
                                        :class="currentStep >= 1 ? 'bg-gradient-to-br from-primary to-purple-700 text-white border-primary' : 'bg-white text-gray-400 border-gray-300'">
                                        <span>1</span>
                                    </div>
                                    <span class="mt-2 text-xs sm:text-sm font-medium whitespace-nowrap" :class="currentStep === 1 ? 'text-primary font-bold' : 'text-gray-500'">Account</span>
                                </div>

                                <!-- Connector 1-2 -->
                                <div class="flex-1 h-0.5 mx-3 sm:mx-6" :class="currentStep > 1 ? 'bg-gradient-to-r from-primary to-purple-700' : 'bg-gray-300'"></div>

                                <!-- Step 2 -->
                                <div class="flex flex-col items-center">
                                    <div 
                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-md border-2 z-10"
                                        :class="currentStep >= 2 ? 'bg-gradient-to-br from-primary to-purple-700 text-white border-primary' : 'bg-white text-gray-400 border-gray-300'">
                                        <span>2</span>
                                    </div>
                                    <span class="mt-2 text-xs sm:text-sm font-medium whitespace-nowrap" :class="currentStep === 2 ? 'text-primary font-bold' : 'text-gray-500'">Business</span>
                                </div>

                                <!-- Connector 2-3 -->
                                <div class="flex-1 h-0.5 mx-3 sm:mx-6" :class="currentStep > 2 ? 'bg-gradient-to-r from-primary to-purple-700' : 'bg-gray-300'"></div>

                                <!-- Step 3 -->
                                <div class="flex flex-col items-center">
                                    <div 
                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-md border-2 z-10"
                                        :class="currentStep >= 3 ? 'bg-gradient-to-br from-primary to-purple-700 text-white border-primary' : 'bg-white text-gray-400 border-gray-300'">
                                        <span>3</span>
                                    </div>
                                    <span class="mt-2 text-xs sm:text-sm font-medium whitespace-nowrap" :class="currentStep === 3 ? 'text-primary font-bold' : 'text-gray-500'">Services</span>
                                </div>

                                <!-- Connector 3-4 -->
                                <div class="flex-1 h-0.5 mx-3 sm:mx-6" :class="currentStep > 3 ? 'bg-gradient-to-r from-primary to-purple-700' : 'bg-gray-300'"></div>

                                <!-- Step 4 -->
                                <div class="flex flex-col items-center">
                                    <div 
                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-md border-2 z-10"
                                        :class="currentStep >= 4 ? 'bg-gradient-to-br from-primary to-purple-700 text-white border-primary' : 'bg-white text-gray-400 border-gray-300'">
                                        <span>4</span>
                                    </div>
                                    <span class="mt-2 text-xs sm:text-sm font-medium whitespace-nowrap" :class="currentStep === 4 ? 'text-primary font-bold' : 'text-gray-500'">Finish</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('vendor.public.store') }}" enctype="multipart/form-data" class="p-4 sm:p-6 lg:p-8 min-h-[400px]">
        @csrf

                    {{-- Step 1: Account Information --}}
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="mb-4">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-user-circle text-indigo-600 mr-2"></i>
                                Account Information
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">Create your login credentials</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name / Contact Person <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    required
                                    minlength="2"
                                    maxlength="255"
                                    value="{{ old('name') }}"
                                    :class="hasError('name') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 transition-colors"
                                    placeholder="John Doe">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p x-show="hasError('name')" x-text="getError('name')" class="mt-1 text-sm text-red-600 validation-error"></p>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    required
                                    maxlength="255"
                                    value="{{ old('email') }}"
                                    :class="hasError('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 transition-colors"
                                    placeholder="your@email.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p x-show="hasError('email')" x-text="getError('email')" class="mt-1 text-sm text-red-600 validation-error"></p>
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    id="phone" 
                                    required
                                    minlength="10"
                                    maxlength="20"
                                    value="{{ old('phone') }}"
                                    :class="hasError('phone') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 transition-colors"
                                    placeholder="+233 XX XXX XXXX or 0XX XXX XXXX">
                                <p class="mt-1 text-xs text-gray-500">Ghana phone format</p>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p x-show="hasError('phone')" x-text="getError('phone')" class="mt-1 text-sm text-red-600 validation-error"></p>
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    required
                                    minlength="8"
                                    maxlength="255"
                                    :class="hasError('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 transition-colors"
                                    placeholder="Minimum 8 characters">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p x-show="hasError('password')" x-text="getError('password')" class="mt-1 text-sm text-red-600 validation-error"></p>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="md:col-span-2">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    id="password_confirmation" 
                                    required
                                    minlength="8"
                                    maxlength="255"
                                    :class="hasError('password_confirmation') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 transition-colors"
                                    placeholder="Re-enter your password">
                                <p x-show="hasError('password_confirmation')" x-text="getError('password_confirmation')" class="mt-1 text-sm text-red-600 validation-error"></p>
                            </div>
            </div>
        </div>

                    {{-- Step 2: Business Information --}}
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="mb-4">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-briefcase text-indigo-600 mr-2"></i>
                                Business Information
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">Tell us about your business</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            {{-- Business Name --}}
            <div>
                                <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Business Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                    type="text" 
                    name="business_name" 
                                    id="business_name" 
                    required
                                    value="{{ old('business_name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="Your Business Name">
                                @error('business_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
            </div>

                            {{-- Category --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Business Category <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="category_id" 
                                    id="category_id" 
                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Business Description <span class="text-red-500">*</span>
                                </label>
                <textarea 
                                    name="description" 
                    id="description" 
                    rows="4"
                                    required
                                    maxlength="2000"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                    placeholder="Describe your business and the services you offer...">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maximum 2000 characters</p>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
            </div>

                            {{-- Address --}}
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Business Address <span class="text-red-500">*</span>
                                </label>
                                <input 
                    type="text" 
                    name="address" 
                                    id="address" 
                                    required
                                    value="{{ old('address') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="Street address, building name, etc.">
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Region --}}
                            <div>
                                <label for="region" class="block text-sm font-medium text-gray-700 mb-1">
                                    Region <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="region" 
                                    id="region" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <option value="">Select region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->name }}" {{ old('region') == $region->name ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- City --}}
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                    City / Town <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="city" 
                                    id="city" 
                                    required
                                    value="{{ old('city') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="e.g., Accra, Kumasi">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
            </div>

                            {{-- Website --}}
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                                    Website <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input 
                    type="url" 
                    name="website" 
                                    id="website"
                                    value="{{ old('website') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="https://www.yourbusiness.com">
                                @error('website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- WhatsApp --}}
                            <div>
                                <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                                    WhatsApp <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input 
                                    type="tel" 
                                    name="whatsapp" 
                                    id="whatsapp"
                                    value="{{ old('whatsapp') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="+233 XX XXX XXXX">
                                @error('whatsapp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Service Information --}}
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="mb-4">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-tags text-indigo-600 mr-2"></i>
                                Your First Service
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">Create your first service listing</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            {{-- Service Name --}}
                            <div class="md:col-span-2">
                                <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Service Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="service_name" 
                                    id="service_name" 
                                    required
                                    value="{{ old('service_name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="e.g., Wedding Photography Package">
                                @error('service_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Service Type --}}
                            <div>
                                <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pricing Type <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="service_type" 
                                    id="service_type" 
                                    required
                                    x-model="serviceType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <option value="">Select type</option>
                                    <option value="fixed">Fixed Price</option>
                                    <option value="hourly">Hourly Rate</option>
                                    <option value="package">Package Deal</option>
                                    <option value="quote">Custom Quote</option>
                                </select>
                                @error('service_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Service Price --}}
                            <div x-show="serviceType !== 'quote'">
                                <label for="service_price" class="block text-sm font-medium text-gray-700 mb-1">
                                    Price (GH₵) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="service_price" 
                                    id="service_price"
                                    :required="serviceType !== 'quote'"
                                    value="{{ old('service_price') }}"
                                    min="0"
                                    step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="0.00">
                                @error('service_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Service Description --}}
                            <div class="md:col-span-2">
                                <label for="service_description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Service Description <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="service_description" 
                                    id="service_description" 
                                    rows="4"
                                    required
                                    maxlength="1000"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                    placeholder="Describe what's included in this service...">{{ old('service_description') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters</p>
                                @error('service_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Portfolio Images --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Portfolio Images <span class="text-gray-400 text-xs">(Optional, max 3)</span>
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors">
                                    <input 
                                        type="file" 
                                        name="portfolio_images[]" 
                                        id="portfolio_images"
                                        accept="image/jpeg,image/jpg,image/png,image/webp"
                                        multiple
                                        max="3"
                                        class="hidden"
                                        @change="handleFileSelect">
                                    <label for="portfolio_images" class="cursor-pointer">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-600 mb-1">Click to upload images</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 5MB each</p>
                                        </div>
                                    </label>
                                </div>
                                <div x-show="selectedFiles.length > 0" class="mt-3">
                                    <p class="text-sm text-gray-700 font-medium mb-2">Selected files:</p>
                                    <template x-for="(file, index) in selectedFiles" :key="index">
                                        <div class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded mb-1">
                                            <span class="text-sm text-gray-600" x-text="file.name"></span>
                                            <span class="text-xs text-gray-500" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></span>
                                        </div>
                                    </template>
                                </div>
                                @error('portfolio_images.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Step 4: Terms and Finish --}}
                    <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="mb-4">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-check-circle text-indigo-600 mr-2"></i>
                                Almost Done!
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">Review and agree to our terms</p>
                        </div>

                        <div class="space-y-4">
                            {{-- Summary --}}
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <h4 class="font-semibold text-indigo-900 mb-3 flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Registration Summary
                                </h4>
                                <div class="space-y-2 text-sm text-indigo-800">
                                    <p>✓ Create your vendor account</p>
                                    <p>✓ Set up your business profile</p>
                                    <p>✓ List your first service</p>
                                    <p>✓ Start receiving inquiries</p>
                                </div>
                            </div>

                            {{-- Terms Checkbox --}}
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="flex items-start cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="terms" 
                                        id="terms"
                                        required
                                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-0.5">
                                    <span class="ml-3 text-sm text-gray-700">
                                        I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Terms & Conditions</a> and <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Privacy Policy</a>. I confirm that all information provided is accurate and I have the right to offer the services listed.
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Benefits --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Free to Start</p>
                                        <p class="text-xs text-gray-600">No upfront costs</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-users text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Reach Clients</p>
                                        <p class="text-xs text-gray-600">Across Ghana</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-shield-alt text-purple-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Verified Badge</p>
                                        <p class="text-xs text-gray-600">Build trust</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-chart-line text-yellow-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Grow Business</p>
                                        <p class="text-xs text-gray-600">Analytics included</p>
                                    </div>
                                </div>
                            </div>
            </div>
        </div>

                    {{-- Navigation Buttons --}}
                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                        {{-- Previous Button --}}
                        <button 
                            type="button"
                            x-show="currentStep > 1"
                            @click="previousStep()"
                            class="inline-flex items-center px-4 sm:px-5 py-2 sm:py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span class="hidden sm:inline">Previous</span>
                            <span class="sm:hidden">Back</span>
                        </button>

                        <div x-show="currentStep === 1"></div>

                        {{-- Next/Submit Button --}}
                        <button 
                            type="button"
                            x-show="currentStep < totalSteps"
                            @click="nextStep()"
                            class="inline-flex items-center px-4 sm:px-5 py-2 sm:py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm">
                            <span class="hidden sm:inline">Next Step</span>
                            <span class="sm:hidden">Next</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>

                        <button 
                            type="submit"
                            x-show="currentStep === totalSteps"
                            class="inline-flex items-center px-4 sm:px-5 py-2 sm:py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all shadow-lg">
                            <i class="fas fa-check-circle mr-2"></i>
                            Create Account
                        </button>
        </div>

                    {{-- Already have account --}}
                    <div class="text-center mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Log in here
                </a>
            </p>
        </div>
    </form>
        </div>
    </div>

    {{-- Alpine.js Multi-Step Form Logic --}}
    <script>
        function registrationForm() {
            return {
                currentStep: 1,
                totalSteps: 4,
                serviceType: '{{ old("service_type") }}',
                selectedFiles: [],
                errors: {},
                
                nextStep() {
                    // Validate current step before proceeding
                    if (this.validateStep(this.currentStep)) {
                        if (this.currentStep < this.totalSteps) {
                            this.currentStep++;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            this.errors = {}; // Clear errors when moving to next step
                        }
                    }
                },
                
                previousStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        this.errors = {}; // Clear errors when going back
                    }
                },
                
                validateStep(step) {
                    this.errors = {};
                    let isValid = true;
                    
                    if (step === 1) {
                        // Step 1: Account Information
                        const name = document.getElementById('name').value.trim();
                        const email = document.getElementById('email').value.trim();
                        const phone = document.getElementById('phone').value.trim();
                        const password = document.getElementById('password').value;
                        const passwordConfirm = document.getElementById('password_confirmation').value;
                        
                        if (!name || name.length < 2) {
                            this.errors.name = 'Name must be at least 2 characters';
                            isValid = false;
                        }
                        
                        if (!email || !this.isValidEmail(email)) {
                            this.errors.email = 'Please enter a valid email address';
                            isValid = false;
                        }
                        
                        if (!phone || phone.length < 10) {
                            this.errors.phone = 'Please enter a valid phone number';
                            isValid = false;
                        }
                        
                        if (!password || password.length < 8) {
                            this.errors.password = 'Password must be at least 8 characters';
                            isValid = false;
                        }
                        
                        if (password !== passwordConfirm) {
                            this.errors.password_confirmation = 'Passwords do not match';
                            isValid = false;
                        }
                    }
                    
                    if (step === 2) {
                        // Step 2: Business Information
                        const businessName = document.getElementById('business_name').value.trim();
                        const categoryId = document.getElementById('category_id').value;
                        const description = document.getElementById('description').value.trim();
                        const address = document.getElementById('address').value.trim();
                        const region = document.getElementById('region').value;
                        const city = document.getElementById('city').value.trim();
                        
                        if (!businessName || businessName.length < 3) {
                            this.errors.business_name = 'Business name must be at least 3 characters';
                            isValid = false;
                        }
                        
                        if (!categoryId) {
                            this.errors.category_id = 'Please select a business category';
                            isValid = false;
                        }
                        
                        if (!description || description.length < 20) {
                            this.errors.description = 'Description must be at least 20 characters';
                            isValid = false;
                        }
                        
                        if (description.length > 2000) {
                            this.errors.description = 'Description cannot exceed 2000 characters';
                            isValid = false;
                        }
                        
                        if (!address || address.length < 5) {
                            this.errors.address = 'Please enter a valid address';
                            isValid = false;
                        }
                        
                        if (!region) {
                            this.errors.region = 'Please select a region';
                            isValid = false;
                        }
                        
                        if (!city || city.length < 2) {
                            this.errors.city = 'Please enter a valid city/town';
                            isValid = false;
                        }
                    }
                    
                    if (step === 3) {
                        // Step 3: Service Information
                        const serviceName = document.getElementById('service_name').value.trim();
                        const serviceType = document.getElementById('service_type').value;
                        const servicePrice = document.getElementById('service_price').value;
                        const serviceDescription = document.getElementById('service_description').value.trim();
                        
                        if (!serviceName || serviceName.length < 3) {
                            this.errors.service_name = 'Service name must be at least 3 characters';
                            isValid = false;
                        }
                        
                        if (!serviceType) {
                            this.errors.service_type = 'Please select a pricing type';
                            isValid = false;
                        }
                        
                        if (serviceType !== 'quote' && (!servicePrice || parseFloat(servicePrice) < 0)) {
                            this.errors.service_price = 'Please enter a valid price';
                            isValid = false;
                        }
                        
                        if (!serviceDescription || serviceDescription.length < 20) {
                            this.errors.service_description = 'Service description must be at least 20 characters';
                            isValid = false;
                        }
                        
                        if (serviceDescription.length > 1000) {
                            this.errors.service_description = 'Service description cannot exceed 1000 characters';
                            isValid = false;
                        }
                    }
                    
                    if (!isValid) {
                        // Show error message
                        this.showValidationErrors();
                    }
                    
                    return isValid;
                },
                
                isValidEmail(email) {
                    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return re.test(email);
                },
                
                showValidationErrors() {
                    // Scroll to first error
                    setTimeout(() => {
                        const firstError = document.querySelector('.validation-error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 100);
                },
                
                handleFileSelect(event) {
                    const files = Array.from(event.target.files).slice(0, 3);
                    this.selectedFiles = files;
                },
                
                getError(field) {
                    return this.errors[field] || '';
                },
                
                hasError(field) {
                    return !!this.errors[field];
                }
            }
        }
    </script>
</x-guest-layout>
