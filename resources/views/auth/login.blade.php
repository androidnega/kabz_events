<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4">
        <div class="w-full max-w-xs mx-4">
            {{-- Login Card --}}
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                {{-- Header Section --}}
                <div class="bg-gradient-to-br from-indigo-50 to-white px-8 py-8">
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-gray-900 mb-3">
                            Welcome Back
                        </h2>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Or 
                            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 underline decoration-2">
                                create a new account
                            </a>
                        </p>
                    </div>
                </div>

                {{-- Form Section --}}
                <div class="bg-gray-50 px-8 py-7 rounded-b-xl">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-3" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium text-sm mb-2" />
                            <x-text-input id="email" class="block mt-2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" 
                                          type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium text-sm mb-2" />
                            <x-text-input id="password" class="block mt-2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                          type="password"
                                          name="password"
                                          required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between text-sm pt-1">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                                <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-indigo-600 hover:text-indigo-700 font-medium" href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3.5 rounded-lg font-medium transition shadow-md hover:shadow-lg text-sm transform hover:scale-105 duration-200">
                                <i class="fas fa-sign-in-alt mr-2"></i>{{ __('Sign in') }}
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
