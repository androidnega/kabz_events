<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Register as a Vendor</h2>
        <p class="mt-2 text-sm text-gray-600">
            Create your account and start showcasing your services
        </p>
    </div>

    <form method="POST" action="{{ route('vendor.public.store') }}" class="space-y-6">
        @csrf

        <!-- Account Information Section -->
        <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
            
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input 
                    id="name" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name" 
                    placeholder="John Doe"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username"
                    placeholder="your@email.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input 
                    id="password" 
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password"
                    placeholder="Minimum 8 characters"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input 
                    id="password_confirmation" 
                    class="block mt-1 w-full"
                    type="password"
                    name="password_confirmation"
                    required 
                    autocomplete="new-password"
                    placeholder="Re-enter your password"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Business Information Section -->
        <div class="pt-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
            
            <!-- Business Name -->
            <div>
                <x-input-label for="business_name" :value="__('Business Name')" />
                <x-text-input 
                    id="business_name" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="business_name" 
                    :value="old('business_name')" 
                    required
                    placeholder="Your Business Name"
                />
                <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div class="mt-4">
                <x-input-label for="phone" :value="__('Phone Number')" />
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

            <!-- Description -->
            <div class="mt-4">
                <x-input-label for="description" :value="__('Business Description (Optional)')" />
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                    placeholder="Describe your business and the services you offer..."
                >{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                <p class="mt-1 text-xs text-gray-500">Maximum 2000 characters</p>
            </div>

            <!-- Address -->
            <div class="mt-4">
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

            <!-- Website -->
            <div class="mt-4">
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
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Back to Home') }}
            </a>
            
            <x-primary-button>
                {{ __('Create Vendor Account') }}
            </x-primary-button>
        </div>

        <!-- Already have an account -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Log in here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

