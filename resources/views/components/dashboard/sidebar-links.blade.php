@php
    $role = auth()->user()->getRoleNames()->first();
@endphp

<div class="space-y-1">
    {{-- Common link for all users --}}
    <a href="{{ route('dashboard') }}" 
       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
        <i class="fas fa-tachometer-alt text-lg w-5"></i>
        <span>Dashboard</span>
    </a>

    {{-- Role-specific links --}}
    @if($role === 'super_admin')
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Configuration</h3>
        </div>
        
        <a href="{{ route('superadmin.settings.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('superadmin.settings.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-cog text-lg w-5"></i>
            <span>Settings Center</span>
        </a>
        
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">System Management</h3>
        </div>
        
        <a href="{{ route('superadmin.sms.test') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('superadmin.sms.test') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-sms text-lg w-5"></i>
            <span>SMS Test</span>
        </a>
        
        <a href="{{ route('superadmin.locations.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('superadmin.locations.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-map-marker-alt text-lg w-5"></i>
            <span>Locations</span>
        </a>
        
        <a href="{{ route('superadmin.backups.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('superadmin.backups.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-database text-lg w-5"></i>
            <span>Backups</span>
        </a>

        <a href="{{ route('admin.media.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.media.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-cloud text-lg w-5"></i>
            <span>Media Management</span>
        </a>

        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-users-cog text-lg w-5"></i>
            <span>Manage Users</span>
        </a>

    @elseif($role === 'admin')
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</h3>
        </div>
        
        <a href="{{ route('admin.verifications.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.verifications.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-user-check text-lg w-5"></i>
            <span>Verifications</span>
        </a>
        
        <a href="{{ route('vendors.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('vendors.index') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-store-alt text-lg w-5"></i>
            <span>All Vendors</span>
        </a>
        
        <a href="{{ route('admin.clients.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.clients.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-users text-lg w-5"></i>
            <span>Clients</span>
        </a>
        
        <a href="{{ route('admin.reports.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-flag text-lg w-5"></i>
            <span>Reports</span>
        </a>

        <a href="{{ route('admin.media.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.media.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-cloud text-lg w-5"></i>
            <span>Media Management</span>
        </a>

        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-users-cog text-lg w-5"></i>
            <span>User Management</span>
        </a>

    @elseif($role === 'vendor')
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">My Business</h3>
        </div>
        
        <a href="{{ route('vendor.services.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('vendor.services.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-concierge-bell text-lg w-5"></i>
            <span>My Services</span>
        </a>
        
        <a href="{{ route('vendor.messages') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('vendor.messages*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-envelope text-lg w-5"></i>
            <span>Messages</span>
        </a>
        
        <a href="{{ route('vendor.verification') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('vendor.verification') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-certificate text-lg w-5"></i>
            <span>Verification</span>
        </a>
        
        <a href="{{ route('vendor.subscriptions') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('vendor.subscriptions') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-gem text-lg w-5"></i>
            <span>Subscription</span>
        </a>

    @elseif($role === 'client')
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">My Activity</h3>
        </div>
        
        <a href="{{ route('vendors.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('vendors.index') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-search text-lg w-5"></i>
            <span>Find Vendors</span>
        </a>
        
        <a href="{{ route('client.conversations') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('client.messages*') || request()->routeIs('client.conversations') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-comments text-lg w-5"></i>
            <span>Messages</span>
        </a>
        
        <a href="{{ route('search.index') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('search.*') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
            <i class="fas fa-th-large text-lg w-5"></i>
            <span>Browse Categories</span>
        </a>
    @endif

    {{-- Common Links Section --}}
    <div class="pt-4 pb-2">
        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</h3>
    </div>
    
    <a href="{{ route('profile.edit') }}" 
       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('profile.edit') ? 'bg-primary bg-opacity-10 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
        <i class="fas fa-user-cog text-lg w-5"></i>
        <span>Profile Settings</span>
    </a>
</div>

