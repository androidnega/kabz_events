<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4">
        <div class="w-full max-w-xs mx-4">
            {{-- Header Section --}}
            <div class="text-center mb-6">
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

            {{-- Login Card --}}
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                {{-- Form Section --}}
                <div class="bg-gray-50 px-8 py-8 rounded-xl">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-3" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

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
                                autofocus 
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
                                placeholder="Enter your password"
                                required 
                                autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-6">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition-colors" href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3.5 rounded-lg font-semibold transition-colors duration-200 text-sm">
                                {{ __('Sign in') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
