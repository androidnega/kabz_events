<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-12 px-4">
        <div class="w-full max-w-md">
            {{-- Logo/Header --}}
            <div class="text-center mb-6">
                <a href="/" class="inline-block">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-primary to-purple-700 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">K</span>
                    </div>
                </a>
                <h2 class="mt-4 text-2xl sm:text-3xl font-bold text-gray-900">
                    Join KABZS EVENT
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-primary hover:text-purple-700 transition-colors">
                        Sign in
                    </a>
                </p>
            </div>

            {{-- Register Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-purple-100 p-6 sm:p-8 backdrop-blur-sm">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" class="text-gray-700 font-semibold" />
                        <x-text-input id="name" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-all" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-all" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold" />
                        <x-text-input id="password" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-all"
                                        type="password"
                                        name="password"
                                        required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 font-semibold" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-all"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <x-primary-button class="w-full justify-center bg-gradient-to-r from-primary to-purple-700 hover:from-purple-700 hover:to-primary transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                            <i class="fas fa-user-plus mr-2"></i>{{ __('Create Account') }}
                        </x-primary-button>
                    </div>
                </form>

                {{-- Vendor Registration Link --}}
                <div class="mt-5 pt-5 border-t border-purple-100">
                    <p class="text-center text-sm text-gray-600">
                        Want to offer services? 
                        <a href="{{ route('vendor.public.register') }}" class="font-medium text-primary hover:text-purple-700 transition-colors inline-flex items-center group">
                            <i class="fas fa-store mr-1 group-hover:scale-110 transition-transform"></i> 
                            <span class="border-b border-transparent group-hover:border-primary transition-all">Register as a Vendor</span>
                        </a>
                    </p>
                </div>
            </div>

            {{-- Back to Home Link --}}
            <div class="mt-4 text-center">
                <a href="/" class="text-sm text-gray-600 hover:text-primary transition-colors inline-flex items-center group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
