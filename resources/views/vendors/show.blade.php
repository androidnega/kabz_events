<x-layouts.base>
    <x-slot name="title">
        {{ $vendor->business_name }} - Verified Event Vendor in Ghana | {{ config('app.name') }}
    </x-slot>

    <!-- Breadcrumb Section -->
    <div class="bg-gray-50 border-b border-gray-200 py-3">
        <div class="container mx-auto">
            <!-- Breadcrumb Only -->
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-primary transition">
                    <i class="fas fa-home"></i> Home
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <a href="{{ route('vendors.index') }}" class="text-gray-500 hover:text-primary transition">
                    Vendors
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-500">Vendor Details</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-4 md:py-12 px-2 md:px-4" x-data="{ 
        showLoginModal: false, 
        showContactSheet: false,
        showCallbackSheet: false,
        callbackForm: {
            name: '',
            phone: ''
        },
        callbackSubmitting: false
    }">
        <div class="container mx-auto">
            <!-- Vendor Name Header -->
            <div class="mb-4 md:mb-8 px-2 md:px-0">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900">{{ $vendor->business_name }}</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
                <!-- Main Column -->
                <div class="lg:col-span-2 space-y-4 md:space-y-8">
                    <!-- Sample Work Section -->
                    @if($vendor->sample_work_images && count($vendor->sample_work_images) > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <!-- Section Header -->
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ $vendor->sample_work_title ?? 'Sample Work' }}
                            </h2>
                        </div>
                        
                        <!-- Image Gallery -->
                        <div class="p-3 md:p-6">
                            <!-- Main Image Display -->
                            <div class="relative mb-4">
                                <div class="w-full h-64 md:h-96 bg-gray-100 rounded-lg overflow-hidden group relative">
                                    <img id="mainSampleImage" src="{{ asset('storage/' . $vendor->sample_work_images[0]) }}" 
                                         alt="Sample work from {{ $vendor->business_name }}" 
                                         class="w-full h-full object-contain md:object-cover">
                                    
                                    <!-- Navigation Arrows -->
                                    @if(count($vendor->sample_work_images) > 1)
                                    <button onclick="previousImage()" class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 p-2 rounded-full transition-all md:opacity-0 md:group-hover:opacity-100 shadow-lg">
                                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <button onclick="nextImage()" class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 p-2 rounded-full transition-all md:opacity-0 md:group-hover:opacity-100 shadow-lg">
                                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    @endif
                                    
                                    <!-- Image Counter -->
                                    <div class="absolute bottom-4 left-4 bg-white bg-opacity-90 text-gray-800 px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                        <span id="imageCounter">1</span>/{{ count($vendor->sample_work_images) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Thumbnail Gallery - Hidden on Mobile, Desktop Only -->
                            @if(count($vendor->sample_work_images) > 1)
                            <div class="hidden md:block overflow-x-auto">
                                <div class="flex gap-3">
                                    @foreach($vendor->sample_work_images as $index => $image)
                                        @if($index < 5)
                                        <div class="flex-shrink-0 relative cursor-pointer group thumbnail-item" data-image="{{ asset('storage/' . $image) }}" data-index="{{ $index + 1 }}">
                                            <div class="w-24 h-16 overflow-hidden rounded-lg border-2 border-gray-300 group-hover:border-primary transition-all duration-200">
                                                <img src="{{ asset('storage/' . $image) }}" 
                                                     alt="Sample work {{ $index + 1 }}" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                    
                                    @if(count($vendor->sample_work_images) > 5)
                                    <div class="flex-shrink-0 relative">
                                        <div class="w-24 h-16 bg-blue-600 rounded-lg border-2 border-gray-300 flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">+{{ count($vendor->sample_work_images) - 5 }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- About Section (moved after pictures) -->
                    <x-card>
                        <div class="p-4 md:p-6" x-data="{ expanded: false }">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 md:mb-4">About This Business</h2>
                            @if($vendor->description)
                                @php
                                    $descriptionLength = strlen($vendor->description);
                                    $shortDescription = $descriptionLength > 200 ? substr($vendor->description, 0, 200) . '...' : $vendor->description;
                                @endphp
                                <div class="text-gray-700 leading-relaxed text-sm md:text-base">
                                    <p x-show="!expanded">{{ $shortDescription }}</p>
                                    <p x-show="expanded" x-cloak>{{ $vendor->description }}</p>
                                </div>
                                @if($descriptionLength > 200)
                                    <button @click="expanded = !expanded" class="mt-3 text-primary hover:text-purple-700 font-medium text-sm flex items-center">
                                        <span x-text="expanded ? 'Read less' : 'Read more'"></span>
                                        <svg x-show="!expanded" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <svg x-show="expanded" x-cloak class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                @endif
                            @else
                                <p class="text-gray-500 italic text-sm">No description provided yet.</p>
                            @endif
                        </div>
                    </x-card>

                    <!-- Mobile Only: Action Buttons (After About Section) -->
                    <div class="lg:hidden">
                        <x-card>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Call Button -->
                                    <button @click="showContactSheet = true" class="flex flex-col items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-4 rounded-xl transition-all active:scale-95">
                                        <i class="fas fa-phone text-xl"></i>
                                        <span class="text-sm font-medium">Call</span>
                                    </button>

                                    <!-- Request Callback Button -->
                                    <button @click="showCallbackSheet = true" class="flex flex-col items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-4 rounded-xl transition-all active:scale-95">
                                        <i class="fas fa-phone-volume text-xl"></i>
                                        <span class="text-sm font-medium">Request Callback</span>
                                    </button>

                                    <!-- Message Button -->
                                    @auth
                                    <button onclick="alert('Message feature coming soon')" class="flex flex-col items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-4 rounded-xl transition-all active:scale-95">
                                        <i class="fas fa-comment-dots text-xl"></i>
                                        <span class="text-sm font-medium">Message</span>
                                    </button>
                                    @else
                                    <button @click="showLoginModal = true" class="flex flex-col items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-4 rounded-xl transition-all active:scale-95">
                                        <i class="fas fa-comment-dots text-xl"></i>
                                        <span class="text-sm font-medium">Message</span>
                                    </button>
                                    @endauth

                                    <!-- WhatsApp Button -->
                                    @if($vendor->whatsapp)
                                    @php
                                        $whatsappNumber = preg_replace('/[^0-9+]/', '', $vendor->whatsapp);
                                        if (str_starts_with($whatsappNumber, '0')) {
                                            $whatsappNumber = '233' . substr($whatsappNumber, 1);
                                        }
                                        $whatsappNumber = ltrim($whatsappNumber, '+');
                                    @endphp
                                    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="flex flex-col items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-4 rounded-xl transition-all active:scale-95">
                                        <i class="fab fa-whatsapp text-xl"></i>
                                        <span class="text-sm font-medium">WhatsApp</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </x-card>
                    </div>

                    <!-- Services Section -->
                    <x-card>
                        <div class="p-4 md:p-6">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 md:mb-6">Services Offered</h2>
                            
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
                        <div class="p-4 md:p-6">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 md:mb-4">Share Your Experience</h2>
                            <p class="text-sm text-gray-600 mb-4 md:mb-6">Help other Ghanaians find trusted vendors by sharing your experience</p>
                            
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
                                Please <button @click="showLoginModal = true" class="text-primary hover:underline font-semibold">log in</button> to leave a review for this vendor.
                            </p>
                        </div>
                    </x-card>
                    @endauth

                    <!-- Reviews Display Section -->
                    <x-card>
                        <div class="p-4 md:p-6">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 md:mb-6">
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

                <!-- Sticky Sidebar - Hidden on Mobile -->
                <div class="hidden lg:block lg:col-span-1">
                    <div class="lg:sticky lg:top-20 space-y-4">
                        <!-- Vendor Sidebar Component with all sidebar content -->
                        <x-vendor-sidebar 
                            :vendor="$vendor" 
                            :averageRating="$averageRating" 
                            :totalReviews="$totalReviews"
                            :averageResponseTime="$averageResponseTime"
                        />

                        <!-- Similar Vendors -->
                        @if($similarVendors->count() > 0)
                        <div class="bg-white p-3 rounded-2xl shadow">
                            <h3 class="text-[13px] font-bold text-gray-900 mb-3">Similar Vendors</h3>
                            <div class="space-y-3">
                                @foreach($similarVendors as $similar)
                                <a href="{{ route('vendors.show', $similar->slug) }}" class="block group">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm text-primary font-bold">
                                                {{ strtoupper(substr($similar->business_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-[13px] font-semibold text-gray-900 group-hover:text-primary">
                                                {{ strlen($similar->business_name) > 25 ? substr($similar->business_name, 0, 25) . '...' : $similar->business_name }}
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
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Login Required Modal --}}
        <div x-show="showLoginModal" 
             x-cloak
             @click.away="showLoginModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" 
             style="display: none;">
          
          <!-- Modal panel - Centered and Compact -->
          <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-xs mx-4 overflow-hidden" 
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               @click.stop>
            
            <!-- Close Button -->
            <button 
              @click="showLoginModal = false"
              class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition z-10">
              <i class="fas fa-times text-lg"></i>
            </button>

            <!-- Header Section -->
            <div class="bg-gradient-to-br from-indigo-50 to-white px-6 py-6">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-600 mb-4 shadow-lg">
                  <i class="fas fa-lock text-white text-base"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">
                  Login Required
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                  Please sign in to leave a review for this vendor
                </p>
              </div>
            </div>
            
            <!-- Actions Section -->
            <div class="bg-gray-50 px-6 py-5 rounded-b-xl">
              <div class="space-y-3">
                <a href="{{ route('login') }}" class="block w-full">
                  <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-200 text-sm">
                    Sign In
                  </button>
                </a>
                
                <a href="{{ route('register') }}" class="block w-full">
                  <button class="w-full bg-white hover:bg-gray-50 text-indigo-600 border-2 border-indigo-600 px-4 py-3 rounded-lg font-medium transition-colors duration-200 text-sm">
                    Create Account
                  </button>
                </a>
              </div>

              <!-- Divider -->
              <div class="my-4">
                <div class="border-t border-gray-300"></div>
              </div>

              <!-- Vendor Registration Link -->
              <div class="text-center">
                <p class="text-xs text-gray-600">
                  Are you a vendor? 
                  <a href="{{ route('vendor.public.register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold underline decoration-2 hover:decoration-indigo-700">
                    Register here
                  </a>
                </p>
              </div>
            </div>
          </div>
        </div>

        {{-- Contact Bottom Sheet (Mobile Only) --}}
        <div x-show="showContactSheet" 
             x-cloak
             @click="showContactSheet = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 z-50 bg-black bg-opacity-50" 
             style="display: none;">
          
          <!-- Bottom Sheet Content -->
          <div @click.stop
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="translate-y-full"
               x-transition:enter-end="translate-y-0"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-y-0"
               x-transition:leave-end="translate-y-full"
               class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl max-h-[80vh] overflow-y-auto">
            
            <!-- Handle Bar -->
            <div class="flex justify-center pt-3 pb-2">
              <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Contact Vendor</h3>
                <button @click="showContactSheet = false" class="text-gray-400 hover:text-gray-600">
                  <i class="fas fa-times text-xl"></i>
                </button>
              </div>
            </div>

            <!-- Contact Options -->
            <div class="p-6 space-y-4">
              @if($vendor->phone)
              <a href="tel:{{ $vendor->phone }}" class="flex items-center gap-4 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition active:scale-95">
                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-phone text-white"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-gray-500">Phone</p>
                  <p class="text-lg font-semibold text-gray-900">{{ $vendor->phone }}</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
              </a>
              @endif

              @if($vendor->whatsapp)
              @php
                $whatsappNumber = preg_replace('/[^0-9+]/', '', $vendor->whatsapp);
                if (str_starts_with($whatsappNumber, '0')) {
                    $whatsappNumber = '233' . substr($whatsappNumber, 1);
                }
                $whatsappNumber = ltrim($whatsappNumber, '+');
              @endphp
              <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="flex items-center gap-4 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition active:scale-95">
                <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="fab fa-whatsapp text-white text-xl"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-gray-500">WhatsApp</p>
                  <p class="text-lg font-semibold text-gray-900">{{ $vendor->whatsapp }}</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
              </a>
              @endif

              @if($vendor->email)
              <a href="mailto:{{ $vendor->email }}" class="flex items-center gap-4 p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition active:scale-95">
                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-envelope text-white"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-gray-500">Email</p>
                  <p class="text-base font-semibold text-gray-900 break-all">{{ $vendor->email }}</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
              </a>
              @endif

              @if($vendor->address)
              <div class="p-4 bg-gray-50 rounded-xl">
                <div class="flex items-start gap-4">
                  <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-white"></i>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">Location</p>
                    <p class="text-base font-medium text-gray-900">{{ $vendor->address }}</p>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Callback Request Bottom Sheet (Mobile Only) --}}
        <div x-show="showCallbackSheet" 
             x-cloak
             @click="showCallbackSheet = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 z-50 bg-black bg-opacity-50" 
             style="display: none;">
          
          <!-- Bottom Sheet Content -->
          <div @click.stop
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="translate-y-full"
               x-transition:enter-end="translate-y-0"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-y-0"
               x-transition:leave-end="translate-y-full"
               class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl">
            
            <!-- Handle Bar -->
            <div class="flex justify-center pt-3 pb-2">
              <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Request Callback</h3>
                  <p class="text-sm text-gray-500 mt-1">{{ $vendor->business_name }} will call you back</p>
                </div>
                <button @click="showCallbackSheet = false" class="text-gray-400 hover:text-gray-600">
                  <i class="fas fa-times text-xl"></i>
                </button>
              </div>
            </div>

            <!-- Callback Form -->
            <form @submit.prevent="
              callbackSubmitting = true;
              fetch('{{ route('vendor.callback.request', $vendor->id) }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(callbackForm)
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  alert('Callback request sent successfully!');
                  showCallbackSheet = false;
                  callbackForm.name = '';
                  callbackForm.phone = '';
                } else {
                  alert(data.message || 'Something went wrong');
                }
                callbackSubmitting = false;
              })
              .catch(error => {
                alert('Error sending request. Please try again.');
                callbackSubmitting = false;
              });
            " class="p-6 space-y-4">
              
              <!-- Name Field -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                <input 
                  type="text" 
                  x-model="callbackForm.name"
                  required
                  placeholder="Enter your full name"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-base">
              </div>

              <!-- Phone Field -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Your Phone Number</label>
                <input 
                  type="tel" 
                  x-model="callbackForm.phone"
                  required
                  placeholder="e.g., 0244123456"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-base">
              </div>

              <!-- Info Text -->
              <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                  <i class="fas fa-info-circle text-green-600 mt-0.5"></i>
                  <p class="text-sm text-green-800">The vendor will receive your contact details and call you back as soon as possible.</p>
                </div>
              </div>

              <!-- Submit Button -->
              <button 
                type="submit"
                :disabled="callbackSubmitting"
                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-6 py-4 rounded-xl font-semibold text-base transition active:scale-95">
                <span x-show="!callbackSubmitting">Request Callback</span>
                <span x-show="callbackSubmitting" class="flex items-center justify-center gap-2">
                  <i class="fas fa-spinner fa-spin"></i>
                  Sending...
                </span>
              </button>
            </form>
          </div>
        </div>
    </div>
</x-layouts.base>

<script id="vendor-images-data" type="application/json">
@json([
    'images' => $vendor->sample_work_images ? array_map(function($image) { 
        return asset('storage/' . $image); 
    }, $vendor->sample_work_images) : [],
    'count' => count($vendor->sample_work_images ?? [])
])
</script>

<script>
let currentImageIndex = 1;
const imageData = JSON.parse(document.getElementById('vendor-images-data').textContent);
const totalImages = imageData.count;
const images = imageData.images;

function changeMainImage(imageSrc, imageNumber) {
    const mainImage = document.getElementById('mainSampleImage');
    const imageCounter = document.getElementById('imageCounter');
    
    if (mainImage) {
        mainImage.src = imageSrc;
        currentImageIndex = imageNumber;
    }
    
    if (imageCounter) {
        imageCounter.textContent = imageNumber;
    }
}

function previousImage() {
    if (currentImageIndex > 1) {
        currentImageIndex--;
        changeMainImage(images[currentImageIndex - 1], currentImageIndex);
    }
}

function nextImage() {
    if (currentImageIndex < totalImages) {
        currentImageIndex++;
        changeMainImage(images[currentImageIndex - 1], currentImageIndex);
    }
}

// Thumbnail click handlers
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    thumbnails.forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function() {
            const imageSrc = this.getAttribute('data-image');
            const imageIndex = parseInt(this.getAttribute('data-index'));
            changeMainImage(imageSrc, imageIndex);
        });
    });
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
        previousImage();
    } else if (e.key === 'ArrowRight') {
        nextImage();
    }
});
</script>

