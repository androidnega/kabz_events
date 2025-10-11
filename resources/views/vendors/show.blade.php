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
    <div class="py-12">
        <div class="container mx-auto">
            <!-- Vendor Name Header -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $vendor->business_name }}</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Sample Work Section -->
                    @if($vendor->sample_work_images && count($vendor->sample_work_images) > 0)
                    <x-card>
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                                {{ $vendor->sample_work_title ?? 'Sample Work' }}
                            </h2>
                            
                            <!-- Main Image -->
                            <div class="mb-4">
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $vendor->sample_work_images[0]) }}" 
                                         alt="Sample work from {{ $vendor->business_name }}" 
                                         class="main-sample-image w-full h-64 md:h-80 object-cover rounded-lg shadow-lg">
                                    <div class="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm image-counter">
                                        1/{{ count($vendor->sample_work_images) }}
                                    </div>
                                    <div class="absolute top-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                        {{ $vendor->business_name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Thumbnail Images -->
                            @if(count($vendor->sample_work_images) > 1)
                            <div class="grid grid-cols-5 gap-2">
                                @foreach($vendor->sample_work_images as $index => $image)
                                <div class="relative cursor-pointer group" data-image-src="{{ asset('storage/' . $image) }}" data-image-index="{{ $index + 1 }}" onclick="changeMainImage(this.getAttribute('data-image-src'), this.getAttribute('data-image-index'))">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Sample work {{ $index + 1 }}" 
                                         class="w-full h-16 md:h-20 object-cover rounded-lg border-2 border-gray-200 group-hover:border-primary transition">
                                    @if($index == 4 && count($vendor->sample_work_images) > 5)
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg">
                                        <span class="text-white text-sm font-bold">+{{ count($vendor->sample_work_images) - 5 }}</span>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </x-card>
                    @endif

                    <!-- About Section (moved after pictures) -->
                    <x-card>
                        <div class="p-6" x-data="{ expanded: false }">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Business</h2>
                            @if($vendor->description)
                                @php
                                    $descriptionLength = strlen($vendor->description);
                                    $shortDescription = $descriptionLength > 300 ? substr($vendor->description, 0, 300) . '...' : $vendor->description;
                                @endphp
                                <div class="text-gray-700 leading-relaxed">
                                    <p x-show="!expanded">{{ $shortDescription }}</p>
                                    <p x-show="expanded" x-cloak>{{ $vendor->description }}</p>
                                </div>
                                @if($descriptionLength > 300)
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

                <!-- Sticky Sidebar -->
                <div class="lg:col-span-1">
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
                                                {{ Str::limit($similar->business_name, 25) }}
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
    </div>
</x-layouts.base>

<script>
function changeMainImage(imageSrc, imageNumber) {
    const mainImage = document.querySelector('.main-sample-image');
    const imageCounter = document.querySelector('.image-counter');
    
    if (mainImage) {
        mainImage.src = imageSrc;
    }
    
    if (imageCounter) {
        imageCounter.textContent = imageNumber + '/{{ count($vendor->sample_work_images ?? []) }}';
    }
}
</script>

