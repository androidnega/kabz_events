@php
$user = Auth::user();
@endphp

<nav x-data="{ open: false }" class="bg-white shadow-md w-full border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">
      <!-- Logo -->
      <div class="flex items-center">
        <a href="/" class="flex items-center text-lg font-semibold text-indigo-600">
          <x-application-logo class="h-8 w-auto mr-2" />
          <span class="hidden sm:inline">KABZS EVENT</span>
        </a>
      </div>

      <!-- Desktop Navigation Links -->
      <div class="hidden md:flex space-x-6">
        @role('super_admin')
          <a href="{{ route('superadmin.dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('superadmin.*') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-1' : '' }}">
            Dashboard
          </a>
          <a href="{{ route('superadmin.backups.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('superadmin.backups.*') ? 'text-indigo-600' : '' }}">
            Backups
          </a>
          <a href="{{ route('superadmin.locations.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('superadmin.locations.*') ? 'text-indigo-600' : '' }}">
            Locations
          </a>
          <a href="{{ route('superadmin.sms.test') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('superadmin.sms.*') ? 'text-indigo-600' : '' }}">
            SMS Test
          </a>
        @endrole

        @role('admin')
          <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-1' : '' }}">
            Dashboard
          </a>
          <a href="{{ route('admin.verifications.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('admin.verifications.*') ? 'text-indigo-600' : '' }}">
            Verifications
          </a>
          <a href="{{ route('admin.clients.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('admin.clients.*') ? 'text-indigo-600' : '' }}">
            Clients
          </a>
          <a href="{{ route('admin.reports.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('admin.reports.*') ? 'text-indigo-600' : '' }}">
            Reports
          </a>
        @endrole

        @role('vendor')
          <a href="{{ route('vendor.dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('vendor.dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-1' : '' }}">
            Dashboard
          </a>
          <a href="{{ route('vendor.services.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('vendor.services.*') ? 'text-indigo-600' : '' }}">
            My Services
          </a>
          <a href="{{ route('vendor.verification') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('vendor.verification') ? 'text-indigo-600' : '' }}">
            Verification
          </a>
          <a href="{{ route('vendor.subscriptions') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('vendor.subscriptions') ? 'text-indigo-600' : '' }}">
            Subscription
          </a>
        @endrole

        @role('client')
          <a href="{{ route('client.dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('client.dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-1' : '' }}">
            Dashboard
          </a>
          <a href="{{ route('vendors.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('vendors.*') ? 'text-indigo-600' : '' }}">
            Find Vendors
          </a>
          <a href="{{ route('search.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('search.*') ? 'text-indigo-600' : '' }}">
            Search
          </a>
        @endrole
      </div>

      <!-- Account Controls (Desktop) -->
      <div class="hidden md:flex items-center space-x-4">
        {{-- Notification Bell --}}
        <x-notification-bell />
        
        <span class="text-gray-600 text-sm">{{ $user->name }}</span>
        <x-dropdown align="right" width="48">
          <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </x-slot>

          <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">
              Profile Settings
            </x-dropdown-link>
            <x-dropdown-link :href="route('home')">
              Public Site
            </x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <x-dropdown-link :href="route('logout')"
                  onclick="event.preventDefault(); this.closest('form').submit();">
                Log Out
              </x-dropdown-link>
            </form>
          </x-slot>
        </x-dropdown>
      </div>

      <!-- Mobile Menu Button -->
      <div class="flex md:hidden">
        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden">
    <div class="px-2 pt-2 pb-3 space-y-1">
      @role('super_admin')
        <a href="{{ route('superadmin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Dashboard</a>
        <a href="{{ route('superadmin.backups.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Backups</a>
        <a href="{{ route('superadmin.locations.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Locations</a>
        <a href="{{ route('superadmin.sms.test') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">SMS Test</a>
      @endrole

      @role('admin')
        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Dashboard</a>
        <a href="{{ route('admin.verifications.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Verifications</a>
        <a href="{{ route('admin.clients.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Clients</a>
        <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Reports</a>
      @endrole

      @role('vendor')
        <a href="{{ route('vendor.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Dashboard</a>
        <a href="{{ route('vendor.services.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">My Services</a>
        <a href="{{ route('vendor.verification') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Verification</a>
        <a href="{{ route('vendor.subscriptions') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Subscription</a>
      @endrole

      @role('client')
        <a href="{{ route('client.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Dashboard</a>
        <a href="{{ route('vendors.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Find Vendors</a>
        <a href="{{ route('search.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Search</a>
      @endrole

      <div class="border-t border-gray-200 pt-4 pb-1">
        <div class="px-3">
          <div class="text-base font-medium text-gray-800">{{ $user->name }}</div>
          <div class="text-sm font-medium text-gray-500">{{ $user->email }}</div>
        </div>
        <div class="mt-3 space-y-1">
          <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Profile</a>
          <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Public Site</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-gray-50">
              Log Out
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</nav>

