<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4" x-data="{ formWidth: 'max-w-md' }">
        <div class="w-full" :class="formWidth">
            {{-- Logo/Header --}}
            <div class="text-center mb-4">
                <a href="/" class="inline-block">
                    <div class="w-12 h-12 mx-auto bg-gradient-to-br from-primary to-purple-700 rounded-xl flex items-center justify-center shadow">
                        <span class="text-xl font-bold text-white">K</span>
                    </div>
                </a>
                <h2 class="mt-3 text-xl font-bold text-gray-900">
                    Welcome Back
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Or 
                    <a href="{{ route('register') }}" class="font-medium text-primary hover:text-purple-700">
                        create a new account
                    </a>
                </p>
            </div>

            {{-- Login Card --}}
            <div class="bg-white rounded-xl shadow-md border border-purple-100 p-5">
                <!-- Session Status -->
                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-3">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium text-sm" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" 
                                      type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium text-sm" />
                        <x-text-input id="password" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                      type="password"
                                      name="password"
                                      required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" name="remember">
                            <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-primary hover:text-purple-700" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <div>
                        <x-primary-button class="w-full justify-center bg-gradient-to-r from-primary to-purple-700 hover:from-purple-700 hover:to-primary shadow-md">
                            <i class="fas fa-sign-in-alt mr-2"></i>{{ __('Sign in') }}
                        </x-primary-button>
                    </div>
                </form>

                {{-- Vendor Registration Link --}}
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-center text-xs text-gray-600">
                        Want to offer services? 
                        <a href="{{ route('vendor.public.register') }}" class="font-medium text-primary hover:text-purple-700">
                            <i class="fas fa-store mr-1"></i> 
                            Register as a Vendor
                        </a>
                    </p>
                </div>
            </div>

            {{-- Back to Home Link --}}
            <div class="mt-4 text-center">
                <a href="/" class="text-xs text-gray-600 hover:text-primary inline-flex items-center">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
