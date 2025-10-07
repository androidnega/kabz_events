<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <x-card class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Client Details</h2>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="text-lg font-semibold">{{ $client->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-lg font-semibold">{{ $client->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Joined</p>
                    <p class="text-lg font-semibold">{{ $client->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Reviews</p>
                    <p class="text-lg font-semibold">{{ $reviews->count() }}</p>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4 mt-8">Recent Reviews</h3>
            @forelse($reviews as $review)
                <div class="p-4 bg-gray-50 rounded-lg mb-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium">{{ $review->vendor->business_name }}</p>
                            <p class="text-sm text-yellow-600">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-2">{{ $review->comment }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No reviews yet</p>
            @endforelse

            <div class="mt-6">
                <a href="{{ route('admin.clients.index') }}" class="text-teal-600 hover:underline">
                    ← Back to Clients List
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>

