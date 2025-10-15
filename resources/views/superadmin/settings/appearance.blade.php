<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <x-card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-palette text-pink-600 mr-2"></i> Appearance & Theme Settings
                </h2>
                <span class="px-3 py-1 text-sm rounded-full bg-pink-100 text-pink-800">
                    Site Customization
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <ul class="list-disc list-inside text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.appearance.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Hero Section Settings -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-star text-yellow-500 mr-2"></i> Homepage Hero Section
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                            <input type="text" name="hero_title" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                   value="{{ $settings['hero_title'] ?? 'Find Perfect Event Vendors in Ghana' }}"
                                   placeholder="Find Perfect Event Vendors in Ghana">
                            <p class="text-xs text-gray-500 mt-1">Main heading displayed on the homepage</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                            <input type="text" name="hero_subtitle" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                   value="{{ $settings['hero_subtitle'] ?? 'Connect with verified service providers' }}"
                                   placeholder="Connect with verified service providers">
                            <p class="text-xs text-gray-500 mt-1">Subtitle text below the main heading</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Background Type</label>
                            <select name="hero_bg_type" id="heroBgType" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                    onchange="toggleBgOptions()">
                                <option value="gradient" {{ ($settings['hero_bg_type'] ?? 'gradient') === 'gradient' ? 'selected' : '' }}>
                                    Gradient (Default)
                                </option>
                                <option value="image" {{ ($settings['hero_bg_type'] ?? 'gradient') === 'image' ? 'selected' : '' }}>
                                    Custom Image
                                </option>
                            </select>
                        </div>

                        <div id="imageUploadSection" class="{{ ($settings['hero_bg_type'] ?? 'gradient') === 'image' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Background Image</label>
                            
                            @if(isset($settings['hero_bg_image']))
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $settings['hero_bg_image']) }}" 
                                         alt="Current Hero Background" 
                                         class="w-full max-w-md h-40 object-cover rounded-lg border border-gray-300">
                                    <p class="text-xs text-gray-500 mt-1">Current background image</p>
                                </div>
                            @endif

                            <input type="file" name="hero_bg_image" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <p class="text-xs text-gray-500 mt-1">Upload a new image (JPEG, PNG, WEBP - Max 2MB). Recommended: 1920x600px</p>
                        </div>
                    </div>
                </div>

                <!-- Site Color Settings -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-paint-brush text-blue-500 mr-2"></i> Site Color Scheme
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color" 
                                       class="h-12 w-20 border border-gray-300 rounded cursor-pointer"
                                       value="{{ $settings['primary_color'] ?? '#9333ea' }}">
                                <input type="text" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                       value="{{ $settings['primary_color'] ?? '#9333ea' }}"
                                       readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Main brand color (buttons, links, etc.)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="secondary_color" 
                                       class="h-12 w-20 border border-gray-300 rounded cursor-pointer"
                                       value="{{ $settings['secondary_color'] ?? '#a855f7' }}">
                                <input type="text" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                       value="{{ $settings['secondary_color'] ?? '#a855f7' }}"
                                       readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Secondary accent color</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="accent_color" 
                                       class="h-12 w-20 border border-gray-300 rounded cursor-pointer"
                                       value="{{ $settings['accent_color'] ?? '#ec4899' }}">
                                <input type="text" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                       value="{{ $settings['accent_color'] ?? '#ec4899' }}"
                                       readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Additional accent color</p>
                        </div>
                    </div>

                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-blue-700">
                            <strong>💡 Color Tips:</strong><br>
                            • Choose colors that represent your brand identity<br>
                            • Ensure good contrast for readability<br>
                            • Test colors on different devices and screens<br>
                            • Default colors: Primary (#9333ea), Secondary (#a855f7), Accent (#ec4899)
                        </p>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-eye text-green-500 mr-2"></i> Live Preview
                    </h3>
                    
                    <div id="heroPreview" class="rounded-lg overflow-hidden border border-gray-300">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-8 text-center">
                            <h1 id="previewTitle" class="text-3xl font-bold mb-2">
                                {{ $settings['hero_title'] ?? 'Find Perfect Event Vendors in Ghana' }}
                            </h1>
                            <p id="previewSubtitle" class="text-lg text-purple-100">
                                {{ $settings['hero_subtitle'] ?? 'Connect with verified service providers' }}
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Preview of how your hero section will appear (actual size may vary)</p>
                </div>

                <div class="flex items-center space-x-4">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Save Appearance Settings
                    </button>
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-external-link-alt mr-2"></i> View Live Site
                    </a>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        function toggleBgOptions() {
            const bgType = document.getElementById('heroBgType').value;
            const imageSection = document.getElementById('imageUploadSection');
            
            if (bgType === 'image') {
                imageSection.classList.remove('hidden');
            } else {
                imageSection.classList.add('hidden');
            }
        }

        // Live preview updates
        document.querySelector('input[name="hero_title"]').addEventListener('input', function(e) {
            document.getElementById('previewTitle').textContent = e.target.value || 'Find Perfect Event Vendors in Ghana';
        });

        document.querySelector('input[name="hero_subtitle"]').addEventListener('input', function(e) {
            document.getElementById('previewSubtitle').textContent = e.target.value || 'Connect with verified service providers';
        });

        // Update color preview text fields
        document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
            colorInput.addEventListener('input', function(e) {
                const textInput = e.target.nextElementSibling;
                if (textInput) {
                    textInput.value = e.target.value;
                }
                
                // Update preview gradient
                if (e.target.name === 'primary_color' || e.target.name === 'secondary_color') {
                    updatePreviewGradient();
                }
            });
        });

        function updatePreviewGradient() {
            const primary = document.querySelector('input[name="primary_color"]').value;
            const secondary = document.querySelector('input[name="secondary_color"]').value;
            const preview = document.querySelector('#heroPreview > div');
            preview.style.background = `linear-gradient(to right, ${primary}, ${secondary})`;
        }

        // Initialize on load
        toggleBgOptions();
    </script>
</x-app-layout>

