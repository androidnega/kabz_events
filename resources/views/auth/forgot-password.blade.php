<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4">
        <div class="w-full max-w-xs mx-4">
            {{-- Forgot Password Card --}}
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                {{-- Header Section --}}
                <div class="bg-gradient-to-br from-indigo-50 to-white px-8 py-8">
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-gray-900 mb-3">
                            Reset Password
                        </h2>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Enter your email to receive a password reset link
                        </p>
                    </div>
                </div>

                {{-- Form Section --}}
                <div class="bg-gray-50 px-8 py-8 rounded-b-xl">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-6">
                            <x-input-label for="email" :value="__('Email')" class="block text-gray-700 font-semibold text-sm mb-3" />
                            <x-text-input 
                                id="email" 
                                class="w-full px-4 py-3.5 rounded-lg border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-gray-900 placeholder-gray-400" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                placeholder="Enter your email address"
                                required 
                                autofocus />
                            <x-input-error :messages="$errors->get('email')" class="mt-2.5" />
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 mb-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white px-6 py-4 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-sm tracking-wide">
                                <i class="fas fa-envelope mr-2.5"></i>
                                {{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                    </form>

                    {{-- Divider --}}
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-3 bg-gray-50 text-gray-500 uppercase tracking-wider">Or</span>
                        </div>
                    </div>

                    {{-- Login Link --}}
                    <div class="text-center mb-6">
                        <p class="text-sm text-gray-600 mb-4">
                            Remember your password?
                        </p>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i> 
                            Sign in to your account
                        </a>
                    </div>

                    {{-- Back to Home Link --}}
                    <div class="text-center pt-4 border-t border-gray-200">
                        <a href="/" class="inline-flex items-center justify-center text-sm text-gray-500 hover:text-gray-700 font-medium hover:bg-gray-200 px-5 py-3 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
