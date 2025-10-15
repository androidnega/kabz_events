@props(['vendor'])

{{-- Smart Vendor Card Design - Clean & Unified --}}
<a href="{{ route('vendors.show', $vendor->slug) }}" class="block group">
    <div class="bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-200 overflow-hidden">
        {{-- Vendor Image/Logo --}}
        @php
            $previewUrl = $vendor->getPreviewImageUrl();
            if (!$previewUrl && $vendor->sample_work_images && count($vendor->sample_work_images) > 0) {
                $previewUrl = $vendor->getImageUrl($vendor->sample_work_images[0]);
            }
        @endphp
        
        @if($previewUrl && $previewUrl !== '')
            <div class="h-32 md:h-40 lg:h-48 bg-gray-100 overflow-hidden">
                <img src="{{ $previewUrl }}" 
                     alt="{{ $vendor->business_name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                     loading="lazy"
                     onerror="this.parentElement.innerHTML='<div class=\'h-full bg-gradient-to-br from-purple-100 to-teal-100 flex items-center justify-center\'><div class=\'text-4xl md:text-6xl font-bold text-purple-300\'>{{ strtoupper(substr($vendor->business_name, 0, 1)) }}</div></div>'">
            </div>
        @else
            <div class="h-32 md:h-40 lg:h-48 bg-gradient-to-br from-purple-100 to-teal-100 flex items-center justify-center overflow-hidden">
                <div class="text-4xl md:text-6xl font-bold text-purple-300">
                    {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                </div>
            </div>
        @endif

        {{-- Vendor Details --}}
        <div class="p-3 md:p-4">
            {{-- VIP Badge (Prominent at Top) --}}
            @if($vendor->hasVipBadge())
                <div class="mb-2">
                    <x-vip-badge :tier="$vendor->getVipTier()" size="sm" />
                </div>
            @endif

            {{-- Business Name with Verified Badge --}}
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm md:text-base font-semibold text-gray-800 group-hover:text-purple-600 transition-colors line-clamp-2 flex-1">
                    {{ $vendor->business_name }}
                </h3>
                @if($vendor->is_verified)
                    <div class="flex-shrink-0 ml-1">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20" title="Verified">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Rating --}}
            <div class="flex items-center mb-2">
                <div class="flex text-yellow-400">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-3 h-3 md:w-4 md:h-4 {{ $i <= round($vendor->rating_cached) ? 'fill-current' : 'fill-current text-gray-300' }}" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                    @endfor
                </div>
                <span class="ml-1 text-xs md:text-sm text-gray-600">
                    {{ number_format($vendor->rating_cached, 1) }}
                </span>
            </div>

            {{-- Category Badge --}}
            @if($vendor->services->isNotEmpty())
                @php
                    $category = $vendor->services->first()->category;
                @endphp
                <div class="mb-2">
                    <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded font-medium">
                        {{ $category->name }}
                    </span>
                </div>
            @endif

            {{-- Location --}}
            @if($vendor->address)
                <div class="flex items-center text-xs md:text-sm text-gray-600">
                    <svg class="w-3 h-3 md:w-4 md:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">{{ Str::limit($vendor->address, 25) }}</span>
                </div>
            @endif
        </div>
    </div>
</a>
