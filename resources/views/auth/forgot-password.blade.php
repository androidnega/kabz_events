<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8 px-4">
        <div class="w-full max-w-xs mx-4">
            {{-- Forgot Password Card --}}
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                {{-- Header Section --}}
                <div class="bg-gradient-to-br from-indigo-50 to-white px-6 py-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-indigo-600 shadow-lg mb-4">
                            <i class="fas fa-key text-white text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 mb-2">
                            Reset Password
                        </h2>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Enter your email to receive a password reset link
                        </p>
                    </div>
                </div>

                {{-- Form Section --}}
                <div class="bg-gray-50 px-6 py-5 rounded-b-xl">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-3" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-3">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium text-sm" />
                            <x-text-input id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autofocus />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition shadow-md hover:shadow-lg text-sm transform hover:scale-105 duration-200">
                                <i class="fas fa-envelope mr-2"></i>{{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                    </form>

                    {{-- Divider --}}
                    <div class="my-4">
                        <div class="border-t border-gray-300"></div>
                    </div>

                    {{-- Login Link --}}
                    <div class="text-center mb-2">
                        <p class="text-xs text-gray-600">
                            Remember your password? 
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold underline decoration-2 hover:decoration-indigo-700">
                                <i class="fas fa-sign-in-alt mr-1"></i> 
                                Sign in
                            </a>
                        </p>
                    </div>

                    {{-- Back to Home Link --}}
                    <div class="text-center">
                        <a href="/" class="text-xs text-gray-500 hover:text-gray-700 inline-flex items-center font-medium hover:bg-gray-200 px-3 py-2 rounded-lg transition">
                            <i class="fas fa-arrow-left mr-1.5"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
