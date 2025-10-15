<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="mb-6">
            <a href="{{ route('superadmin.settings.index') }}" class="text-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>

        <x-card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-envelope text-indigo-600 mr-2"></i> SMTP Email Configuration
                </h2>
                <span class="px-3 py-1 text-sm rounded-full bg-indigo-100 text-indigo-800">
                    Email Server
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.settings.smtp.update') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="smtp_host" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                               value="{{ $settings['smtp_host'] ?? '' }}"
                               placeholder="smtp.gmail.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="number" name="smtp_port" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                               value="{{ $settings['smtp_port'] ?? '587' }}"
                               placeholder="587">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                    <input type="text" name="smtp_username" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['smtp_username'] ?? '' }}"
                           placeholder="your-email@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                    <input type="password" name="smtp_password" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['smtp_password'] ?? '' }}"
                           placeholder="Your SMTP Password">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                    <select name="smtp_encryption" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Email Address</label>
                    <input type="email" name="smtp_from_address" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['smtp_from_address'] ?? '' }}"
                           placeholder="noreply@kabzsevent.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                    <input type="text" name="smtp_from_name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['smtp_from_name'] ?? '' }}"
                           placeholder="KABZS EVENT">
                </div>

                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <p class="text-sm text-green-700">
                        <strong>✅ Recommended SMTP Settings (KABZS EVENT):</strong><br>
                        <strong>Host:</strong> mail.kabzevents.com<br>
                        <strong>Port:</strong> 465 (SSL) or 587 (TLS)<br>
                        <strong>Username:</strong> noreply@kabzevents.com<br>
                        <strong>Encryption:</strong> SSL (for port 465) or TLS (for port 587)<br>
                        <strong>From Email:</strong> noreply@kabzevents.com<br>
                        <strong>From Name:</strong> KABZS EVENT<br><br>
                        <em>💡 Using "noreply" prevents users from replying to automated emails.</em>
                    </p>
                </div>

                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded">
                    <p class="text-sm text-indigo-700">
                        <strong>Other Common SMTP Settings:</strong><br>
                        Gmail: smtp.gmail.com:587 (TLS)<br>
                        Outlook: smtp-mail.outlook.com:587 (TLS)<br>
                        SendGrid: smtp.sendgrid.net:587 (TLS)
                    </p>
                </div>

                <div class="flex items-center space-x-4">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Save Configuration
                    </button>
                    <a href="{{ route('superadmin.settings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>

        @if($settings['smtp_host'] ?? false)
        <x-card class="p-6 mt-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-paper-plane text-blue-600 mr-2"></i> Test Email Configuration
            </h3>
            <p class="text-sm text-gray-600 mb-4">Send a test email to verify your SMTP settings are working correctly.</p>
            
            <form method="POST" action="{{ route('superadmin.settings.smtp.test') }}" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Email</label>
                    <input type="email" name="test_email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           placeholder="Enter email address to test">
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i> Send Test Email
                </button>
            </form>
        </x-card>
        @endif
    </div>
</x-app-layout>

