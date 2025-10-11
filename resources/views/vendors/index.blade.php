<x-layouts.base>
    <x-slot name="title">
        Find Trusted Event Vendors in Ghana | {{ config('app.name') }}
    </x-slot>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-primary to-purple-700 text-white py-12">
        <div class="container mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Find Trusted Event Vendors in Ghana</h1>
            <p class="text-lg text-purple-100">Browse verified service providers for your special events</p>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="bg-white border-b border-gray-200 py-6">
        <div class="container mx-auto">
            <form action="{{ route('vendors.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <!-- Search Bar -->
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search for photographer, caterer, decorator..."
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    >
                </div>

                <!-- Category Filter -->
                <div class="md:w-64">
                    <select 
                        name="category" 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                        onchange="this.form.submit()"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Button -->
                <x-button type="submit" variant="primary">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </x-button>
            </form>
        </div>
    </div>

    <!-- Vendors Grid -->
    <div class="py-12 bg-neutral">
        <div class="container mx-auto">
            @if($vendors->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($vendors as $vendor)
                    <x-card hoverable>
                        <!-- Vendor Image -->
                        @if($vendor->sample_work_images && count($vendor->sample_work_images) > 0)
                            <div class="h-48 bg-gray-100 overflow-hidden">
                                <img src="{{ asset('storage/' . $vendor->sample_work_images[0]) }}" 
                                     alt="{{ $vendor->business_name }}" 
                                     class="w-full h-full object-cover">
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
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ $vendor->business_name }}
                                </h3>
                                <x-badge type="verified">
                                    <svg class="w-3 h-3 mr-1 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified
                                </x-badge>
                            </div>

                            <!-- Rating -->
                            <div class="flex items-center mb-3">
                                <div class="flex items-center">
                                    @php
                                        $rating = round($vendor->rating_cached);
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
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
                                <x-badge type="primary">
                                    {{ $category->name }}
                                </x-badge>
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
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ Str::limit($vendor->address, 30) }}
                            </div>
                            @endif

                            <!-- View Profile Button -->
                            <x-button variant="primary" class="w-full" onclick="window.location='{{ route('vendors.show', $vendor->slug) }}'">
                                View Profile
                            </x-button>
                        </div>
                    </x-card>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $vendors->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No vendors found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    <div class="mt-6">
                        <x-button variant="primary" onclick="window.location='{{ route('vendors.index') }}'">
                            View All Vendors
                        </x-button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.base>

