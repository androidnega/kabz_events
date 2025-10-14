<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4">
        <div class="w-full max-w-xs mx-4">
            {{-- Register Card --}}
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                {{-- Header Section --}}
                <div class="bg-gradient-to-br from-indigo-50 to-white px-8 py-8">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-indigo-600 shadow-lg mb-4">
                            <i class="fas fa-user-plus text-white text-lg"></i>
                        </div>
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
                </div>

                {{-- Form Section --}}
                <div class="bg-gray-50 px-8 py-7 rounded-b-xl">
                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="text-gray-700 font-medium text-sm mb-2" />
                            <x-text-input id="name" class="block mt-2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium text-sm mb-2" />
                            <x-text-input id="email" class="block mt-2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium text-sm mb-2" />
                            <x-text-input id="password" class="block mt-2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 font-medium text-sm mb-2" />
                            <x-text-input id="password_confirmation" class="block mt-2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                            type="password"
                                            name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3.5 rounded-lg font-medium transition shadow-md hover:shadow-lg text-sm transform hover:scale-105 duration-200">
                                <i class="fas fa-user-plus mr-2"></i>{{ __('Create Account') }}
                            </button>
                        </div>
                    </form>

                    {{-- Divider --}}
                    <div class="my-5">
                        <div class="border-t border-gray-300"></div>
                    </div>

                    {{-- Vendor Registration Link --}}
                    <div class="text-center mb-3">
                        <p class="text-sm text-gray-600">
                            Want to offer services? 
                            <a href="{{ route('vendor.public.register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold underline decoration-2 hover:decoration-indigo-700">
                                <i class="fas fa-store mr-1"></i> 
                                Register as a Vendor
                            </a>
                        </p>
                    </div>

                    {{-- Back to Home Link --}}
                    <div class="text-center">
                        <a href="/" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center font-medium hover:bg-gray-200 px-4 py-2.5 rounded-lg transition">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
