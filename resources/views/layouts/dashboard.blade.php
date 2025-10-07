<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Dashboard' }} | KABZS Event</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">
  <!-- Dashboard Navigation -->
  <x-dashboard-nav />

  <!-- Page Header (Optional) -->
  @if (isset($header))
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{ $header }}
      </div>
    </header>
  @endif

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Success/Error Messages -->
    @if (session('success'))
      <x-alert type="success" class="mb-4">
        {{ session('success') }}
      </x-alert>
    @endif

    @if (session('error'))
      <x-alert type="error" class="mb-4">
        {{ session('error') }}
      </x-alert>
    @endif

    <!-- Page Content -->
    {{ $slot }}
  </main>

  <!-- Footer (Optional) -->
  <footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <p class="text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} KABZS Event. All rights reserved.
      </p>
    </div>
  </footer>
</body>
</html>

