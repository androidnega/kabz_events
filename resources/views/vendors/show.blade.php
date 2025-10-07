<x-layouts.base>
    <x-slot name="title">
        {{ $vendor->business_name }} - Verified Event Vendor in Ghana | {{ config('app.name') }}
    </x-slot>

    <!-- Vendor Banner -->
    <div class="bg-gradient-to-r from-primary to-purple-700 py-16">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Vendor Logo/Image -->
                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-5xl text-primary font-bold">
                        {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                    </span>
                </div>

                <!-- Vendor Info -->
                <div class="flex-1 text-center md:text-left text-white">
                    <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
                        <h1 class="text-3xl md:text-4xl font-bold">{{ $vendor->business_name }}</h1>
                        <x-badge type="verified" class="bg-white text-green-700">
                            <svg class="w-3 h-3 mr-1 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified Vendor
                        </x-badge>
                    </div>

                    <!-- Rating -->
                    <div class="flex items-center justify-center md:justify-start mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= round($averageRating) ? 'text-yellow-300' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="ml-2 text-lg">
                            {{ number_format($averageRating, 1) }} ({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})
                        </span>
                    </div>

                    <!-- Categories -->
                    @if($vendor->services->count() > 0)
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        @foreach($vendor->services->pluck('category')->unique('id') as $category)
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                            {{ $category->name }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-12">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- About Section -->
                    <x-card>
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Business</h2>
                            @if($vendor->description)
                                <p class="text-gray-700 leading-relaxed">{{ $vendor->description }}</p>
                            @else
                                <p class="text-gray-500 italic">No description provided yet.</p>
                            @endif
                        </div>
                    </x-card>

                    <!-- Services Section -->
                    <x-card>
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Services Offered</h2>
                            
                            @if($vendor->services->count() > 0)
                                <div class="space-y-4">
                                    @foreach($vendor->services as $service)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary transition">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $service->title }}</h3>
                                            <x-badge type="primary">{{ $service->category->name }}</x-badge>
                                        </div>

                                        @if($service->description)
                                        <p class="text-sm text-gray-600 mb-3">{{ $service->description }}</p>
                                        @endif

                                        <div class="flex items-center justify-between">
                                            <div class="text-sm">
                                                <span class="text-gray-500">Price:</span>
                                                <span class="font-semibold text-accent ml-1">
                                                    @if($service->price_min && $service->price_max)
                                                        GH₵ {{ number_format($service->price_min, 2) }} - GH₵ {{ number_format($service->price_max, 2) }}
                                                    @elseif($service->price_min)
                                                        GH₵ {{ number_format($service->price_min, 2) }}
                                                    @else
                                                        Contact for quote
                                                    @endif
                                                </span>
                                            </div>
                                            <x-badge type="default">{{ ucfirst($service->pricing_type) }}</x-badge>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-8">No active services at the moment.</p>
                            @endif
                        </div>
                    </x-card>

                    <!-- Review Submission Form -->
                    @auth
                    <x-card>
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Share Your Experience</h2>
                            <p class="text-sm text-gray-600 mb-6">Help other Ghanaians find trusted vendors by sharing your experience</p>
                            
                            @if(session('success'))
                                <x-alert type="success" class="mb-4">
                                    {{ session('success') }}
                                </x-alert>
                            @endif

                            @if(session('error'))
                                <x-alert type="error" class="mb-4">
                                    {{ session('error') }}
                                </x-alert>
                            @endif

                            <form action="{{ route('reviews.store', $vendor) }}" method="POST" class="space-y-4">
                                @csrf

                                <!-- Rating -->
                                <div>
                                    <x-input-label for="rating" value="Rating (Required)" />
                                    <select 
                                        name="rating" 
                                        id="rating"
                                        class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm block mt-1 w-full"
                                        required
                                    >
                                        <option value="">Select rating</option>
                                        <option value="5">5 Stars - Excellent</option>
                                        <option value="4">4 Stars - Very Good</option>
                                        <option value="3">3 Stars - Good</option>
                                        <option value="2">2 Stars - Fair</option>
                                        <option value="1">1 Star - Poor</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                                </div>

                                <!-- Review Title -->
                                <div>
                                    <x-input-label for="title" value="Review Title (Optional)" />
                                    <x-text-input 
                                        id="title" 
                                        name="title" 
                                        type="text" 
                                        class="mt-1 block w-full"
                                        :value="old('title')"
                                        placeholder="e.g., Great service for my wedding"
                                    />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <!-- Comment -->
                                <div>
                                    <x-input-label for="comment" value="Your Review (Required)" />
                                    <textarea 
                                        id="comment" 
                                        name="comment" 
                                        rows="4"
                                        class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm block mt-1 w-full"
                                        placeholder="Share details about your experience with this vendor..."
                                        required
                                    >{{ old('comment') }}</textarea>
                                    <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                                    <p class="mt-1 text-xs text-gray-500">Maximum 2000 characters</p>
                                </div>

                                <!-- Event Date -->
                                <div>
                                    <x-input-label for="event_date" value="Event Date (Optional)" />
                                    <x-text-input 
                                        id="event_date" 
                                        name="event_date" 
                                        type="date" 
                                        class="mt-1 block w-full"
                                        :value="old('event_date')"
                                    />
                                    <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <x-button type="submit" variant="primary" size="lg" class="w-full">
                                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Submit Review
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </x-card>
                    @else
                    <x-card>
                        <div class="p-6 text-center">
                            <p class="text-gray-600">
                                Please <a href="{{ route('login') }}" class="text-primary hover:underline font-semibold">log in</a> to leave a review for this vendor.
                            </p>
                        </div>
                    </x-card>
                    @endauth

                    <!-- Reviews Display Section -->
                    <x-card>
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                                Client Reviews (Ghana) - {{ $totalReviews }}
                            </h2>
                            
                            @if($totalReviews > 0)
                                <div class="space-y-6">
                                    @foreach($vendor->reviews as $review)
                                    <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $review->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</p>
                                                @if($review->event_date)
                                                <p class="text-xs text-gray-500">Event: {{ $review->event_date->format('d M Y') }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        @if($review->title)
                                        <h4 class="font-medium text-gray-900 mb-1">{{ $review->title }}</h4>
                                        @endif
                                        <p class="text-gray-700">{{ $review->comment }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-8">No reviews yet. Be the first to share your experience!</p>
                            @endif
                        </div>
                    </x-card>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Contact Card -->
                    <x-card>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Contact Vendor</h3>
                            
                            <!-- Call Button -->
                            @if($vendor->phone)
                            <a href="tel:{{ $vendor->phone }}" class="block w-full mb-3">
                                <x-button variant="primary" class="w-full">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Call Vendor
                                </x-button>
                            </a>
                            @endif

                            <!-- WhatsApp Button -->
                            @if($vendor->whatsapp)
                            @php
                                // Clean WhatsApp number (remove spaces, dashes, etc.)
                                $whatsappNumber = preg_replace('/[^0-9+]/', '', $vendor->whatsapp);
                                // If starts with 0, replace with 233
                                if (str_starts_with($whatsappNumber, '0')) {
                                    $whatsappNumber = '233' . substr($whatsappNumber, 1);
                                }
                                // Remove + if present for WhatsApp link
                                $whatsappNumber = ltrim($whatsappNumber, '+');
                            @endphp
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="block w-full mb-3">
                                <x-button variant="secondary" class="w-full">
                                    <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    WhatsApp Vendor
                                </x-button>
                            </a>
                            @endif

                            <!-- Website Button -->
                            @if($vendor->website)
                            <a href="{{ $vendor->website }}" target="_blank" class="block w-full">
                                <x-button variant="outline" class="w-full">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                    Visit Website
                                </x-button>
                            </a>
                            @endif
                        </div>
                    </x-card>

                    <!-- Business Info Card -->
                    <x-card>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Business Details</h3>
                            
                            @if($vendor->address)
                            <div class="flex items-start mb-3">
                                <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Location</p>
                                    <p class="text-sm text-gray-900">{{ $vendor->address }}</p>
                                </div>
                            </div>
                            @endif

                            @if($vendor->phone)
                            <div class="flex items-start mb-3">
                                <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Phone</p>
                                    <p class="text-sm text-gray-900">{{ $vendor->phone }}</p>
                                </div>
                            </div>
                            @endif

                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Member Since</p>
                                    <p class="text-sm text-gray-900">{{ $vendor->created_at->format('F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </x-card>

                    <!-- Stats Card -->
                    <x-card>
                        <div class="p-6">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-2xl font-bold text-primary">{{ $vendor->services->count() }}</p>
                                    <p class="text-xs text-gray-500">Services</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-accent">{{ number_format($averageRating, 1) }}</p>
                                    <p class="text-xs text-gray-500">Rating</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-secondary">{{ $totalReviews }}</p>
                                    <p class="text-xs text-gray-500">Reviews</p>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Sidebar Column (Mobile moves below main) -->
                <div class="lg:col-span-1">
                    <!-- Similar Vendors -->
                    @if($similarVendors->count() > 0)
                    <x-card>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Similar Vendors</h3>
                            <div class="space-y-4">
                                @foreach($similarVendors as $similar)
                                <a href="{{ route('vendors.show', $similar->slug) }}" class="block group">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-lg text-primary font-bold">
                                                {{ strtoupper(substr($similar->business_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 group-hover:text-primary">
                                                {{ Str::limit($similar->business_name, 30) }}
                                            </p>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= round($similar->rating_cached) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                                <span class="ml-1 text-xs text-gray-500">{{ number_format($similar->rating_cached, 1) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </x-card>
                    @endif

                    <!-- Safety Tips -->
                    <x-card>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Safety Tips</h3>
                            <ul class="text-sm text-gray-700 space-y-2">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Meet vendor in person before payment
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Check reviews and ratings
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verify vendor credentials
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Get written quotation first
                                </li>
                            </ul>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-layouts.base>

