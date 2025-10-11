<x-vendor-layout>
    <x-slot name="title">My Services</x-slot>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">My Services</h2>
            <a href="{{ route('vendor.services.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-plus mr-2"></i>
                Add New Service
            </a>
        </div>
        <!-- Success/Error Messages -->
        @if (session('success'))
            <x-alert type="success" class="mb-6">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" class="mb-6">
                {{ session('error') }}
            </x-alert>
        @endif

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6">
                    @if($services->count() > 0)
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Service Title
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Price Range
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($services as $service)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $service->title }}
                                            </div>
                                            @if($service->description)
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($service->description, 50) }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-badge type="primary">
                                                {{ $service->category->name }}
                                            </x-badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($service->price_min && $service->price_max)
                                                GH₵ {{ number_format($service->price_min, 2) }} - GH₵ {{ number_format($service->price_max, 2) }}
                                            @elseif($service->price_min)
                                                GH₵ {{ number_format($service->price_min, 2) }}
                                            @else
                                                <span class="text-gray-500">Contact for quote</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-badge type="default">
                                                {{ ucfirst($service->pricing_type) }}
                                            </x-badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($service->is_active)
                                                <x-badge type="success">Active</x-badge>
                                            @else
                                                <x-badge type="warning">Inactive</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('vendor.services.edit', $service) }}" class="text-primary hover:text-purple-900">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('vendor.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="md:hidden space-y-4">
                            @foreach($services as $service)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ $service->title }}</h3>
                                    @if($service->is_active)
                                        <x-badge type="success">Active</x-badge>
                                    @else
                                        <x-badge type="warning">Inactive</x-badge>
                                    @endif
                                </div>
                                
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Category:</span>
                                        <x-badge type="primary" class="ml-2">{{ $service->category->name }}</x-badge>
                                    </div>
                                    
                                    <div>
                                        <span class="text-gray-500">Price:</span>
                                        <span class="text-gray-900 ml-2">
                                            @if($service->price_min && $service->price_max)
                                                GH₵ {{ number_format($service->price_min, 2) }} - GH₵ {{ number_format($service->price_max, 2) }}
                                            @elseif($service->price_min)
                                                GH₵ {{ number_format($service->price_min, 2) }}
                                            @else
                                                Contact for quote
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div>
                                        <span class="text-gray-500">Type:</span>
                                        <x-badge type="default" class="ml-2">{{ ucfirst($service->pricing_type) }}</x-badge>
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-end space-x-2">
                                    <a href="{{ route('vendor.services.edit', $service) }}">
                                        <x-button variant="outline" size="sm">
                                            Edit
                                        </x-button>
                                    </a>
                                    <form action="{{ route('vendor.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger" size="sm">
                                            Delete
                                        </x-button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No services yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first service.</p>
                            <div class="mt-6">
                                <a href="{{ route('vendor.services.create') }}">
                                    <x-button variant="primary">
                                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Your First Service
                                    </x-button>
                                </a>
                            </div>
                        </div>
                @endif
            </div>
        </div>
</x-vendor-layout>

