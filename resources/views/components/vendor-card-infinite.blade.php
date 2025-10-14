@props(['vendor'])

{{-- Smart Vendor Card for Homepage (Clickable Wrapper) - Unified Design --}}
<a href="{{ route('vendors.show', $vendor->slug) }}" class="block group">
    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
        {{-- Vendor Image/Logo --}}
        @php
            $previewUrl = $vendor->getPreviewImageUrl();
            if (!$previewUrl && $vendor->sample_work_images && count($vendor->sample_work_images) > 0) {
                $previewUrl = $vendor->getImageUrl($vendor->sample_work_images[0]);
            }
        @endphp
        
        @if($previewUrl)
            <div class="h-48 bg-gray-100 overflow-hidden">
                <img src="{{ $previewUrl }}" 
                     alt="{{ $vendor->business_name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy">
            </div>
        @else
            <div class="h-48 bg-gradient-to-br from-purple-100 to-teal-100 flex items-center justify-center overflow-hidden">
                {{-- Fallback: Show business initial as placeholder --}}
                <div class="text-6xl font-bold text-purple-300">
                    {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                </div>
            </div>
        @endif

        {{-- Vendor Details --}}
        <div class="p-5">
            {{-- Business Name with Status Badges --}}
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-xl font-semibold text-gray-800 truncate flex-1 group-hover:text-purple-600 transition-colors">
                    {{ $vendor->business_name }}
                </h3>
                <div class="flex flex-col gap-1 ml-2 flex-shrink-0">
                    @php
                        $hasActiveSubscription = $vendor->activeSubscription() !== null;
                    @endphp
                    
                    {{-- VIP/Subscribed Badge --}}
                    @if($hasActiveSubscription)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                            <i class="fas fa-crown mr-1"></i> VIP
                        </span>
                    @endif
                    
                    {{-- Verified Badge --}}
                    @if($vendor->is_verified)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                            <i class="fas fa-check-circle mr-1"></i> Verified
                        </span>
                    @endif
                </div>
            </div>

            {{-- Rating --}}
            <div class="flex items-center mb-3">
                <div class="flex text-yellow-400">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($vendor->rating_cached))
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @elseif($i - 0.5 <= $vendor->rating_cached)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="half-{{ $vendor->id }}">
                                        <stop offset="50%" stop-color="currentColor"/>
                                        <stop offset="50%" stop-color="#e5e7eb" stop-opacity="1"/>
                                    </linearGradient>
                                </defs>
                                <path fill="url(#half-{{ $vendor->id }})" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                <span class="ml-2 text-sm text-gray-600">
                    {{ number_format($vendor->rating_cached, 1) }} ({{ $vendor->reviews->count() }})
                </span>
            </div>

            {{-- Categories --}}
            @if($vendor->services->isNotEmpty())
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach($vendor->services->pluck('category')->unique('id')->take(3) as $category)
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">
                            {{ $category->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Description (if available) --}}
            @if($vendor->description)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ Str::limit($vendor->description, 100) }}
                </p>
            @endif

            {{-- Location --}}
            @if($vendor->address)
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ Str::limit($vendor->address, 35) }}
                </div>
            @endif
        </div>
    </div>
</a>
