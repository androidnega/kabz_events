<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4">
        <div class="w-full max-w-xs mx-4">
            {{-- Header Section --}}
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">
                    Reset Password
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Enter your email to receive a password reset link
                </p>
            </div>

            {{-- Forgot Password Card --}}
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                {{-- Form Section --}}
                <div class="bg-gray-50 px-8 py-8 rounded-xl">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}">
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
                                placeholder="Enter your email address"
                                required 
                                autofocus />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3.5 rounded-lg font-semibold transition-colors duration-200 text-sm">
                                {{ __('Send Reset Link') }}
                            </button>
                        </div>
                    </form>

                    {{-- Login Link --}}
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
