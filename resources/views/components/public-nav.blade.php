<header class="bg-white border-b border-gray-200 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 py-3 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center">
      <!-- Logo -->
      <a href="{{ route('home') }}" class="flex items-center text-lg font-semibold text-indigo-600">
        <x-application-logo class="h-8 w-auto mr-2" />
        <span class="hidden sm:inline">KABZS EVENT</span>
      </a>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex space-x-6">
        <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('home') ? 'text-indigo-600' : '' }}">
          Home
        </a>
        <a href="{{ route('vendors.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('vendors.*') ? 'text-indigo-600' : '' }}">
          Vendors
        </a>
        <a href="{{ route('search.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium {{ request()->routeIs('search.*') ? 'text-indigo-600' : '' }}">
          Search
        </a>
      </nav>

      <!-- Auth Links -->
      <div class="flex items-center space-x-4">
        @guest
          <a href="{{ route('register') }}" class="hidden sm:inline-block text-indigo-600 hover:text-indigo-700 font-medium">
            Sign Up
          </a>
          <a href="{{ route('login') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
            Login
          </a>
        @else
          @php
            $dashboardRoute = 'dashboard';
            if (Auth::user()->hasRole('super_admin')) {
              $dashboardRoute = 'superadmin.dashboard';
            } elseif (Auth::user()->hasRole('admin')) {
              $dashboardRoute = 'admin.dashboard';
            } elseif (Auth::user()->hasRole('vendor')) {
              $dashboardRoute = 'vendor.dashboard';
            } elseif (Auth::user()->hasRole('client')) {
              $dashboardRoute = 'client.dashboard';
            }
          @endphp
          <a href="{{ route($dashboardRoute) }}" class="text-gray-700 hover:text-indigo-600 font-medium">
            Dashboard
          </a>
          <span class="hidden sm:inline text-gray-600 text-sm">{{ Auth::user()->name }}</span>
          <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-red-500 hover:text-red-600 font-medium">
              Logout
            </button>
          </form>
        @endguest
      </div>
    </div>
  </div>
</header>

