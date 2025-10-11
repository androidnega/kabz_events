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
                        <x-vendor-card :vendor="$vendor" />
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
                        <a href="{{ route('vendors.index') }}">
                            <x-button variant="primary">
                                View All Vendors
                            </x-button>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.base>

