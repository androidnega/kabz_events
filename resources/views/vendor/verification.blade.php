<x-vendor-layout>
    <x-slot name="title">Verification</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-900">Vendor Verification</h2>

            @if(session('success'))
                <x-alert type="success" class="mb-4">
                    {{ session('success') }}
                </x-alert>
            @endif

            @if(session('error'))
                <x-alert type="danger" class="mb-4">
                    {{ session('error') }}
                </x-alert>
            @endif

            @if($request)
                {{-- Verification Status Display --}}
                <div class="bg-gray-50 border-l-4 @if($request->status === 'approved') border-green-500 @elseif($request->status === 'rejected') border-red-500 @else border-yellow-500 @endif p-4 rounded">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                Verification Status: 
                                <span class="capitalize @if($request->status === 'approved') text-green-600 @elseif($request->status === 'rejected') text-red-600 @else text-yellow-600 @endif">
                                    {{ $request->status }}
                                </span>
                            </h3>
                            
                            <p class="text-gray-700 mb-2">
                                <strong>Submitted:</strong> {{ $request->submitted_at->format('d M Y, h:i A') }}
                            </p>

                            @if($request->decided_at)
                                <p class="text-gray-700 mb-2">
                                    <strong>Decided:</strong> {{ $request->decided_at->format('d M Y, h:i A') }}
                                </p>
                            @endif

                            @if($request->admin_note)
                                <div class="mt-3 p-3 bg-white rounded border border-gray-200">
                                    <p class="text-sm font-semibold text-gray-700 mb-1">Admin Note:</p>
                                    <p class="text-gray-600">{{ $request->admin_note }}</p>
                                </div>
                            @endif

                            @if($request->status === 'pending')
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                                    <p class="text-sm text-blue-800">
                                        ⏳ Your verification request is being reviewed. We'll update you soon!
                                    </p>
                                </div>
                            @elseif($request->status === 'approved')
                                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded">
                                    <p class="text-sm text-green-800">
                                        ✅ Congratulations! Your vendor account is now verified. You can now enjoy all verified vendor benefits.
                                    </p>
                                </div>
                            @elseif($request->status === 'rejected')
                                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                                    <p class="text-sm text-red-800">
                                        ❌ Your verification request was not approved. Please review the admin note and resubmit with correct documentation.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                {{-- Verification Request Form --}}
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">
                        Get verified to build trust with clients and unlock premium features! Upload your Ghana Card or Passport to get started.
                    </p>
                </div>

                <form action="{{ route('vendor.verification.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- ID Document Upload --}}
                    <div>
                        <x-input-label for="id_document" value="Upload Ghana Card or Passport *" class="mb-2" />
                        <input 
                            type="file" 
                            name="id_document" 
                            id="id_document"
                            accept=".jpg,.jpeg,.png,.pdf"
                            required
                            class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-purple-50 file:text-purple-700
                                   hover:file:bg-purple-100"
                        />
                        <p class="mt-1 text-sm text-gray-500">Accepted formats: JPG, PNG, PDF (max 2MB)</p>
                        @error('id_document')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Facebook Link --}}
                    <div>
                        <x-input-label for="facebook" value="Facebook Page (Optional)" class="mb-2" />
                        <x-text-input 
                            type="url" 
                            name="social_links[facebook]" 
                            id="facebook"
                            placeholder="https://facebook.com/yourpage"
                            class="w-full"
                        />
                        <p class="mt-1 text-sm text-gray-500">Help clients find you on social media</p>
                        @error('social_links.facebook')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Instagram Link --}}
                    <div>
                        <x-input-label for="instagram" value="Instagram Profile (Optional)" class="mb-2" />
                        <x-text-input 
                            type="url" 
                            name="social_links[instagram]" 
                            id="instagram"
                            placeholder="https://instagram.com/yourprofile"
                            class="w-full"
                        />
                        <p class="mt-1 text-sm text-gray-500">Showcase your work on Instagram</p>
                        @error('social_links.instagram')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-purple-900 mb-2">📋 What happens next?</h4>
                        <ul class="text-sm text-purple-800 space-y-1">
                            <li>• Our team will review your documents within 24-48 hours</li>
                            <li>• You'll see your verification status on this page</li>
                            <li>• Once approved, you'll get a verified badge ✓</li>
                            <li>• Verified vendors appear higher in search results</li>
                        </ul>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 underline">
                            Back to Dashboard
                        </a>
                        <x-button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold">
                            Submit Verification Request
                        </x-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-vendor-layout>

