@props(['vendor'])

<a href="{{ route('vendors.show', $vendor->slug) }}" class="block group">
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-gray-300 transition-colors duration-200">
        <!-- Vendor Image -->
        @if($vendor->sample_work_images && count($vendor->sample_work_images) > 0)
            <div class="h-48 bg-gray-100 overflow-hidden">
                <img src="{{ asset('storage/' . $vendor->sample_work_images[0]) }}" 
                     alt="{{ $vendor->business_name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
        @else
            <div class="h-48 bg-gradient-to-br from-purple-100 to-indigo-100 flex items-center justify-center">
                <span class="text-6xl text-primary font-bold">
                    {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                </span>
            </div>
        @endif

        <!-- Vendor Info -->
        <div class="p-6">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary transition-colors">
                    {{ $vendor->business_name }}
                </h3>
                @if($vendor->is_verified)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verified
                </span>
                @endif
            </div>

            <!-- Rating -->
            <div class="flex items-center mb-3">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($vendor->rating_cached) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="ml-2 text-sm text-gray-600">
                    {{ number_format($vendor->rating_cached, 1) }}
                </span>
            </div>

            <!-- Categories -->
            @if($vendor->services->count() > 0)
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($vendor->services->pluck('category')->unique('id')->take(3) as $category)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                    {{ $category->name }}
                </span>
                @endforeach
            </div>
            @endif

            <!-- Description -->
            @if($vendor->description)
            <p class="text-sm text-gray-600 mb-4">
                {{ Str::limit($vendor->description, 100) }}
            </p>
            @endif

            <!-- Location -->
            @if($vendor->address)
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ Str::limit($vendor->address, 30) }}
            </div>
            @endif
        </div>
    </div>
</a>
