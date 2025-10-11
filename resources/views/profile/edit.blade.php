<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            {{-- Vendor Tutorial Section --}}
            @if(auth()->user()->hasRole('vendor'))
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Tutorial & Help') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Take a guided tour of your vendor dashboard to learn how to use all features.') }}
                        </p>
                    </header>

                    <form method="POST" action="{{ route('vendor.tour.restart') }}" class="mt-6">
                        @csrf
                        
                        <div class="flex items-start space-x-3 p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-graduation-cap text-purple-600 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-purple-900 mb-1">Interactive Dashboard Tour</h3>
                                <p class="text-xs text-purple-700">
                                    A step-by-step walkthrough showing you how to:
                                </p>
                                <ul class="text-xs text-purple-700 mt-2 space-y-1 ml-4 list-disc">
                                    <li>Manage your services and listings</li>
                                    <li>Get verified and build trust</li>
                                    <li>Respond to client messages</li>
                                    <li>Track your performance stats</li>
                                    <li>Upgrade your subscription</li>
                                </ul>
                            </div>
                        </div>

                        <x-primary-button>
                            <i class="fas fa-play-circle mr-2"></i>
                            {{ __('Start Tutorial') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
