@props(['vendor'])

{{-- Smart Vendor Card Design - Unified across the system --}}
<div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
    {{-- Vendor Image/Logo --}}
    @if($vendor->sample_work_images && count($vendor->sample_work_images) > 0)
        <div class="h-48 bg-gray-100 overflow-hidden relative">
            <img src="{{ asset('storage/' . $vendor->sample_work_images[0]) }}" 
                 alt="{{ $vendor->business_name }}" 
                 class="w-full h-full object-cover">
            
            {{-- Verified Badge Overlay --}}
            @if($vendor->is_verified)
                <div class="absolute top-2 right-2 bg-green-500 text-white rounded-full p-2 shadow-md">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            @endif
        </div>
    @else
        <div class="h-48 bg-gradient-to-br from-purple-100 to-teal-100 flex items-center justify-center overflow-hidden relative">
            {{-- Fallback: Show business initial as placeholder --}}
            <div class="text-6xl font-bold text-purple-300">
                {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
            </div>
            
            {{-- Optional: If vendor has a category, show an icon --}}
            @if($vendor->services->isNotEmpty())
                <div class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md">
                    <i class="fas fa-briefcase text-purple-600 text-sm"></i>
                </div>
            @endif
        </div>
    @endif

    {{-- Vendor Details --}}
    <div class="p-5">
        {{-- Business Name with Verified Badge --}}
        <div class="flex items-start justify-between mb-2">
            <h3 class="text-xl font-semibold text-gray-800 truncate flex-1">
                {{ $vendor->business_name }}
            </h3>
            @if($vendor->is_verified)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 ml-2 flex-shrink-0">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verified
                </span>
            @endif
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
            <div class="flex items-center text-sm text-gray-600 mb-3">
                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ Str::limit($vendor->address, 35) }}
            </div>
        @endif

        {{-- Price Range --}}
        @if($vendor->services->where('price_min', '>', 0)->isNotEmpty())
            <p class="text-sm font-semibold text-gray-700 mb-4">
                <span class="text-purple-600">
                    GH₵ {{ number_format($vendor->services->min('price_min'), 2) }}
                </span>
                @if($vendor->services->max('price_max') > 0)
                    - <span class="text-purple-600">GH₵ {{ number_format($vendor->services->max('price_max'), 2) }}</span>
                @endif
            </p>
        @endif

        {{-- View Profile Button --}}
        <a href="{{ route('vendors.show', $vendor->slug) }}" 
           class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
            View Profile
        </a>
    </div>
</div>

