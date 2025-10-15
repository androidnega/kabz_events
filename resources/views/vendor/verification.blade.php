<x-vendor-layout>
    <x-slot name="title">Verification</x-slot>

    @push('styles')
    <script src="{{ asset('js/verification-wizard.js') }}"></script>
    @endpush

    <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-900">Vendor Verification</h2>

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

                            @if($request->decidedBy)
                                <p class="text-gray-700 mb-2">
                                    <strong>Reviewed by:</strong> {{ $request->decidedBy->name }}
                                    <span class="text-xs text-gray-500">({{ $request->decidedBy->getRoleNames()->first() }})</span>
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
                {{-- Multi-Step Verification Request Form --}}
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">
                        Complete our comprehensive verification process to build trust with clients and unlock premium features!
                    </p>
                </div>

                <div x-data="createVerificationWizard('{{ addslashes($vendor->business_name) }}', '{{ addslashes(auth()->user()->name) }}', '{{ addslashes(auth()->user()->email) }}', '{{ addslashes($vendorCategory) }}')" x-init="$nextTick(() => {
                    window.verificationStoreUrl = '{{ route('vendor.verification.store') }}';
                    window.verificationIndexUrl = '{{ route('vendor.verification') }}';
                    window.regionsData = {{ $regions->toJson() }};
                })">
                    {{-- Progress Indicator --}}
                    <div class="mb-6 sm:mb-8 px-2 sm:px-0">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col items-center flex-1" :class="step >= 1 ? 'text-purple-600' : 'text-gray-400'">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center border-2 text-xs sm:text-sm font-bold mb-1 sm:mb-2"
                                     :class="step >= 1 ? 'bg-purple-600 border-purple-600 text-white' : 'bg-white border-gray-300'">
                                    1
                                </div>
                                <span class="text-[10px] sm:text-xs font-medium text-center">Business</span>
                            </div>
                            <div class="flex-1 h-0.5 sm:h-1 mx-1 sm:mx-2 -mt-6 sm:-mt-8" :class="step >= 2 ? 'bg-purple-600' : 'bg-gray-300'"></div>
                            <div class="flex flex-col items-center flex-1" :class="step >= 2 ? 'text-purple-600' : 'text-gray-400'">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center border-2 text-xs sm:text-sm font-bold mb-1 sm:mb-2"
                                     :class="step >= 2 ? 'bg-purple-600 border-purple-600 text-white' : 'bg-white border-gray-300'">
                                    2
                                </div>
                                <span class="text-[10px] sm:text-xs font-medium text-center">Contact</span>
                            </div>
                            <div class="flex-1 h-0.5 sm:h-1 mx-1 sm:mx-2 -mt-6 sm:-mt-8" :class="step >= 3 ? 'bg-purple-600' : 'bg-gray-300'"></div>
                            <div class="flex flex-col items-center flex-1" :class="step >= 3 ? 'text-purple-600' : 'text-gray-400'">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center border-2 text-xs sm:text-sm font-bold mb-1 sm:mb-2"
                                     :class="step >= 3 ? 'bg-purple-600 border-purple-600 text-white' : 'bg-white border-gray-300'">
                                    3
                                </div>
                                <span class="text-[10px] sm:text-xs font-medium text-center">Evidence</span>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                        {{-- PAGE 1: Business Information --}}
                        <div x-show="step === 1" x-cloak>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Business Information</h3>

                            {{-- Business Name (Read-only) --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="business_name" value="Business Name *" class="text-sm" />
                                <x-text-input 
                                    type="text" 
                                    id="business_name"
                                    x-model="formData.business_name"
                                    class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed text-sm sm:text-base"
                                    required
                                    :value="$vendor->business_name"
                                    readonly
                                />
                                <p class="mt-1 text-xs text-gray-500">Pre-filled from your profile</p>
                            </div>

                            {{-- Business Category (Read-only) --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="business_category" value="Business Category *" class="text-sm" />
                                <x-text-input 
                                    type="text" 
                                    id="business_category"
                                    x-model="formData.business_category"
                                    class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed text-sm sm:text-base"
                                    required
                                    value="{{ $vendorCategory }}"
                                    readonly
                                />
                                <input type="hidden" name="business_category" :value="formData.business_category">
                                <p class="mt-1 text-xs text-gray-500">Pre-filled from your vendor services</p>
                            </div>

                            {{-- Business Registration Number --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="business_registration" value="Business Registration Number (Optional)" class="text-sm" />
                                <x-text-input 
                                    type="text" 
                                    id="business_registration"
                                    x-model="formData.business_registration_number"
                                    class="mt-1 block w-full text-sm sm:text-base"
                                    placeholder="e.g., CS123456789"
                                />
                                <p class="mt-1 text-xs text-gray-500">If your business is registered with the Registrar General</p>
                            </div>

                            {{-- Business Description --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="business_description" value="Business Description *" class="text-sm" />
                                <textarea 
                                    id="business_description"
                                    x-model="formData.business_description"
                                    rows="4"
                                    class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm text-sm sm:text-base"
                                    placeholder="Tell us about your business, services, and what makes you unique..."
                                    required
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500">Minimum 50 characters</p>
                            </div>

                            {{-- Years in Operation --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="years_in_operation" value="Years in Operation *" class="text-sm" />
                                <x-text-input 
                                    type="number" 
                                    id="years_in_operation"
                                    x-model="formData.years_in_operation"
                                    class="mt-1 block w-full text-sm sm:text-base"
                                    min="0"
                                    max="100"
                                    required
                                />
                            </div>

                            {{-- Business Location - Dynamic Cascading Selects --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label value="Business Location *" class="text-sm mb-2" />
                                
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3">
                                    {{-- Region --}}
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Region</label>
                                        <select 
                                            x-model="formData.business_region"
                                            @change="onRegionChange(); selectedRegion = window.regionsData.find(r => r.name === formData.business_region)"
                                            class="block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm text-sm"
                                            required
                                        >
                                            <option value="">Select Region</option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->name }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    {{-- District --}}
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">District</label>
                                        <select 
                                            x-model="formData.business_district"
                                            @change="onDistrictChange(); selectedDistrict = selectedRegion?.districts.find(d => d.name === formData.business_district)"
                                            :disabled="!formData.business_region"
                                            class="block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed text-sm"
                                            required
                                        >
                                            <option value="">Select District</option>
                                            <template x-if="selectedRegion">
                                                <template x-for="district in selectedRegion.districts" :key="district.id">
                                                    <option :value="district.name" x-text="district.name"></option>
                                                </template>
                                            </template>
                                        </select>
                                    </div>
                                    
                                    {{-- Town --}}
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Town</label>
                                        <select 
                                            x-model="formData.business_town"
                                            :disabled="!formData.business_district"
                                            class="block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed text-sm"
                                            required
                                        >
                                            <option value="">Select Town</option>
                                            <template x-if="selectedDistrict">
                                                <template x-for="town in selectedDistrict.towns" :key="town.id">
                                                    <option :value="town.name" x-text="town.name"></option>
                                                </template>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Select your business location from the dropdown menus</p>
                            </div>

                            {{-- Business Logo --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="business_logo" value="Business Logo *" class="text-sm" />
                                <input 
                                    type="file" 
                                    id="business_logo"
                                    @change="formData.business_logo = $event.target.files[0]"
                                    accept=".jpg,.jpeg,.png"
                                    required
                                    class="block w-full text-xs sm:text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none
                                           file:mr-2 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-xs sm:file:text-sm file:font-semibold
                                           file:bg-purple-50 file:text-purple-700
                                           hover:file:bg-purple-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG (max 2MB)</p>
                            </div>
                        </div>

                        {{-- PAGE 2: Contact and Identity Details --}}
                        <div x-show="step === 2" x-cloak>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Contact & Identity Details</h3>

                            {{-- Full Name (Read-only) --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="contact_full_name" value="Full Name *" class="text-sm" />
                                <x-text-input 
                                    type="text" 
                                    id="contact_full_name"
                                    x-model="formData.contact_full_name"
                                    class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed text-sm sm:text-base"
                                    :value="auth()->user()->name"
                                    readonly
                                    required
                                />
                                <p class="mt-1 text-xs text-gray-500">Pre-filled from your account</p>
                            </div>

                            {{-- Role/Position --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="contact_role" value="Role/Position *" class="text-sm" />
                                <x-text-input 
                                    type="text" 
                                    id="contact_role"
                                    x-model="formData.contact_role"
                                    class="mt-1 block w-full text-sm sm:text-base"
                                    placeholder="e.g., Owner, Manager, Director"
                                    required
                                />
                            </div>

                            {{-- Phone Number --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="contact_phone" value="Phone Number *" class="text-sm" />
                                <x-text-input 
                                    type="tel" 
                                    id="contact_phone"
                                    x-model="formData.contact_phone"
                                    class="mt-1 block w-full text-sm sm:text-base"
                                    placeholder="0XX XXX XXXX"
                                    required
                                />
                            </div>

                            {{-- Email Address (Read-only) --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="contact_email" value="Email Address *" class="text-sm" />
                                <x-text-input 
                                    type="email" 
                                    id="contact_email"
                                    x-model="formData.contact_email"
                                    class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed text-sm sm:text-base"
                                    :value="auth()->user()->email"
                                    readonly
                                    required
                                />
                                <p class="mt-1 text-xs text-gray-500">Pre-filled from your account</p>
                            </div>

                            {{-- National ID Type --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="national_id_type" value="National ID Type *" class="text-sm" />
                                <select 
                                    id="national_id_type"
                                    x-model="formData.national_id_type"
                                    class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm text-sm sm:text-base"
                                    required
                                >
                                    <option value="">Select ID type</option>
                                    <option value="Ghana Card">Ghana Card</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Voter ID">Voter ID</option>
                                    <option value="Driver's License">Driver's License</option>
                                </select>
                            </div>

                            {{-- National ID Number --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="national_id_number" value="National ID Number *" class="text-sm" />
                                <x-text-input 
                                    type="text" 
                                    id="national_id_number"
                                    x-model="formData.national_id_number"
                                    class="mt-1 block w-full text-sm sm:text-base"
                                    placeholder="Enter your ID number"
                                    required
                                />
                            </div>

                            {{-- Upload ID Card --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="id_document" value="Upload ID Card *" class="text-sm" />
                                <input 
                                    type="file" 
                                    id="id_document"
                                    @change="formData.id_document = $event.target.files[0]"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="block w-full text-xs sm:text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none
                                           file:mr-2 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-xs sm:file:text-sm file:font-semibold
                                           file:bg-purple-50 file:text-purple-700
                                           hover:file:bg-purple-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">Clear photo of your ID card (JPG, PNG, PDF - max 2MB)</p>
                            </div>

                            {{-- Profile Picture --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="profile_picture" value="Profile Picture *" class="text-sm" />
                                <input 
                                    type="file" 
                                    id="profile_picture"
                                    @change="formData.profile_picture = $event.target.files[0]"
                                    accept=".jpg,.jpeg,.png"
                                    required
                                    class="block w-full text-xs sm:text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none
                                           file:mr-2 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-xs sm:file:text-sm file:font-semibold
                                           file:bg-purple-50 file:text-purple-700
                                           hover:file:bg-purple-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">Recent photo of yourself (JPG, PNG - max 2MB)</p>
                            </div>
                        </div>

                        {{-- PAGE 3: Verification Evidence and Agreement --}}
                        <div x-show="step === 3" x-cloak>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Verification Evidence & Agreement</h3>

                            {{-- Social Media Handles --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label value="Social Media Handles (Optional)" class="text-sm mb-2" />
                                
                                <div class="space-y-2 sm:space-y-3">
                                    <div>
                                        <label class="text-xs sm:text-sm text-gray-600">Facebook</label>
                                        <x-text-input 
                                            type="url" 
                                            x-model="formData.facebook"
                                            class="mt-1 block w-full text-sm sm:text-base"
                                            placeholder="https://facebook.com/yourpage"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="text-xs sm:text-sm text-gray-600">Instagram</label>
                                        <x-text-input 
                                            type="url" 
                                            x-model="formData.instagram"
                                            class="mt-1 block w-full text-sm sm:text-base"
                                            placeholder="https://instagram.com/yourprofile"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="text-xs sm:text-sm text-gray-600">Twitter/X</label>
                                        <x-text-input 
                                            type="url" 
                                            x-model="formData.twitter"
                                            class="mt-1 block w-full text-sm sm:text-base"
                                            placeholder="https://twitter.com/yourhandle"
                                        />
                                    </div>
                                </div>
                            </div>

                            {{-- Website/Portfolio Link --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="website_url" value="Website or Portfolio Link (Optional)" class="text-sm" />
                                <x-text-input 
                                    type="url" 
                                    id="website_url"
                                    x-model="formData.website_url"
                                    class="mt-1 block w-full text-sm sm:text-base"
                                    placeholder="https://yourwebsite.com"
                                />
                            </div>

                            {{-- Proof of Past Events --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="proof_of_events" value="Proof of Past Events (Optional)" class="text-sm" />
                                <input 
                                    type="file" 
                                    id="proof_of_events"
                                    @change="handleMultipleFiles($event, 'proof_of_events')"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    multiple
                                    class="block w-full text-xs sm:text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none
                                           file:mr-2 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-xs sm:file:text-sm file:font-semibold
                                           file:bg-purple-50 file:text-purple-700
                                           hover:file:bg-purple-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload photos from previous events (up to 5 files, max 2MB each)</p>
                            </div>

                            {{-- Reference Letter --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="reference_letter" value="Reference or Endorsement Letter (Optional)" class="text-sm" />
                                <input 
                                    type="file" 
                                    id="reference_letter"
                                    @change="formData.reference_letter = $event.target.files[0]"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="block w-full text-xs sm:text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none
                                           file:mr-2 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-xs sm:file:text-sm file:font-semibold
                                           file:bg-purple-50 file:text-purple-700
                                           hover:file:bg-purple-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">Letter from a client or industry professional (PDF, max 2MB)</p>
                            </div>

                            {{-- Reason for Verification --}}
                            <div class="mb-3 sm:mb-4">
                                <x-input-label for="verification_reason" value="Why do you want to get verified? *" class="text-sm" />
                                <textarea 
                                    id="verification_reason"
                                    x-model="formData.verification_reason"
                                    rows="4"
                                    class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm text-sm sm:text-base"
                                    placeholder="Tell us why verification is important for your business..."
                                    required
                                ></textarea>
                            </div>

                            {{-- Confirmation Checkboxes --}}
                            <div class="mb-3 sm:mb-4 space-y-3 p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-start">
                                    <input 
                                        type="checkbox" 
                                        id="details_confirmed"
                                        x-model="formData.details_confirmed"
                                        required
                                        class="mt-0.5 sm:mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                    />
                                    <label for="details_confirmed" class="ml-2 sm:ml-3 text-xs sm:text-sm text-gray-700">
                                        I confirm that all details provided are true and accurate. *
                                    </label>
                                </div>

                                <div class="flex items-start">
                                    <input 
                                        type="checkbox" 
                                        id="terms_agreed"
                                        x-model="formData.terms_agreed"
                                        required
                                        class="mt-0.5 sm:mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                    />
                                    <label for="terms_agreed" class="ml-2 sm:ml-3 text-xs sm:text-sm text-gray-700">
                                        I agree to the <a href="#" class="text-purple-600 hover:text-purple-700 underline">terms and conditions</a>. *
                                    </label>
                                </div>
                            </div>

                            {{-- Info Box --}}
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 sm:p-4">
                                <h4 class="text-xs sm:text-sm font-semibold text-purple-900 mb-2">📋 What happens next?</h4>
                                <ul class="text-xs sm:text-sm text-purple-800 space-y-1">
                                    <li>• Our team will review your submission within 24-48 hours</li>
                                    <li>• You'll be notified via email about the decision</li>
                                    <li>• Once approved, you'll get a verified badge ✓</li>
                                    <li>• Verified vendors get priority in search results</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Navigation Buttons --}}
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 sm:pt-6 border-t border-gray-200">
                            <div class="w-full sm:w-auto">
                                <button 
                                    type="button"
                                    x-show="step > 1"
                                    @click="step--"
                                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm sm:text-base font-medium hover:bg-gray-300 transition"
                                >
                                    <i class="fas fa-arrow-left mr-2"></i> Previous
                                </button>
                                <a href="{{ route('dashboard') }}" 
                                   x-show="step === 1"
                                   class="block w-full sm:w-auto text-center px-4 sm:px-6 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm sm:text-base font-medium hover:bg-gray-300 transition">
                                    Cancel
                                </a>
                            </div>

                            <div class="w-full sm:w-auto">
                                <button 
                                    type="button"
                                    x-show="step < 3"
                                    @click="nextStep"
                                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-purple-600 text-white rounded-lg text-sm sm:text-base font-medium hover:bg-purple-700 transition"
                                >
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                                <button 
                                    type="submit"
                                    x-show="step === 3"
                                    :disabled="submitting"
                                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-green-600 text-white rounded-lg text-sm sm:text-base font-medium hover:bg-green-700 disabled:bg-gray-400 transition"
                                >
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span x-text="submitting ? 'Submitting...' : 'Submit Request'"></span>
                                </button>
                            </div>
                        </div>
                </form>
                </div>
            @endif
    </div>
</x-vendor-layout>
