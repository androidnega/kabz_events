<header class="bg-white border-b border-gray-200 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 py-3 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center">
      <!-- Logo -->
      <a href="{{ route('home') }}" class="flex items-center">
        <x-kabzs-logo class="h-10 w-auto" />
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
          {{-- All authenticated users go to unified dashboard --}}
          <a href="{{ route('dashboard') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
            Dashboard
          </a>
          <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-gray-700 hover:text-red-600 font-medium text-sm">
              Logout
            </button>
          </form>
        @endguest
      </div>
    </div>
  </div>
</header>

