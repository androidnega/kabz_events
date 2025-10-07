@props(['vendor'])

<div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
    {{-- Vendor Image/Logo --}}
    <div class="h-48 bg-gradient-to-br from-purple-100 to-teal-100 flex items-center justify-center overflow-hidden relative">
        {{-- Always show the business initial as placeholder --}}
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

    {{-- Vendor Details --}}
    <div class="p-5">
        {{-- Business Name --}}
        <h3 class="text-xl font-semibold text-gray-800 mb-2 truncate">
            {{ $vendor->business_name }}
            @if($vendor->is_verified)
                <span class="text-green-500 text-sm" title="Verified Vendor">✓</span>
            @endif
        </h3>

        {{-- Categories --}}
        @if($vendor->services->isNotEmpty())
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach($vendor->services->pluck('category')->unique('id')->take(3) as $category)
                    <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                        {{ $category->name }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Location --}}
        @if($vendor->address)
            <p class="text-sm text-gray-600 mb-2 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ Str::limit($vendor->address, 30) }}
            </p>
        @endif

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

