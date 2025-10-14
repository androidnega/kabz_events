<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4">
        <div class="w-full max-w-xs mx-4">
            {{-- Header Section --}}
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">
                    Join KABZS EVENT
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 underline decoration-2">
                        Sign in
                    </a>
                </p>
            </div>

            {{-- Register Card --}}
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                {{-- Form Section --}}
                <div class="bg-gray-50 px-8 py-8 rounded-xl">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Name')" class="block text-gray-700 font-semibold text-sm mb-3" />
                            <x-text-input 
                                id="name" 
                                class="w-full px-4 py-3.5 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors text-gray-900 placeholder-gray-400" 
                                type="text" 
                                name="name" 
                                :value="old('name')" 
                                placeholder="Enter your full name"
                                required 
                                autofocus 
                                autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mb-6">
                            <x-input-label for="email" :value="__('Email')" class="block text-gray-700 font-semibold text-sm mb-3" />
                            <x-text-input 
                                id="email" 
                                class="w-full px-4 py-3.5 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors text-gray-900 placeholder-gray-400" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                placeholder="Enter your email"
                                required 
                                autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <x-input-label for="password" :value="__('Password')" class="block text-gray-700 font-semibold text-sm mb-3" />
                            <x-text-input 
                                id="password" 
                                class="w-full px-4 py-3.5 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors text-gray-900 placeholder-gray-400"
                                type="password"
                                name="password"
                                placeholder="Create a password"
                                required 
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="block text-gray-700 font-semibold text-sm mb-3" />
                            <x-text-input 
                                id="password_confirmation" 
                                class="w-full px-4 py-3.5 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors text-gray-900 placeholder-gray-400"
                                type="password"
                                name="password_confirmation" 
                                placeholder="Confirm your password"
                                required 
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3.5 rounded-lg font-semibold transition-colors duration-200 text-sm">
                                {{ __('Create Account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
