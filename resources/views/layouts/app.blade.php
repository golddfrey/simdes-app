{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html class="scroll-smooth" lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'SIMDES') }}</title>

    {{-- Vite / CSS --}}
    @if (app()->environment('local'))
      @vite('resources/css/app.css')
    @else
      <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    @endif

    <style>
      /* Small visual niceties for footer and flash */
      .flash-container { position: fixed; right: 1rem; top: 1rem; z-index: 60; }
      .flash-box { background: #ecfdf5; border-left: 4px solid #34d399; padding: 12px 16px; border-radius: 6px; box-shadow: 0 6px 20px rgba(2,6,23,0.08); color: #065f46; }
    </style>
  </head>

  {{-- Make body a column flex container so main can grow and push footer to bottom --}}
  <body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- flash / notifications (partial) --}}
    @include('partials.flash')

    {{-- Header --}}
    <header class="bg-blue-600 text-white">
      <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="font-bold text-xl">{{ config('app.name', 'SIMDES') }}</div>
        <div class="hidden md:flex space-x-6">
          <a href="#" class="hover:text-blue-200">Home</a>
          <a href="#" class="hover:text-blue-200">Features</a>
          <a href="#" class="hover:text-blue-200">About</a>
          <a href="#" class="hover:text-blue-200">Contact</a>
        </div>
      </nav>
    </header>

    {{-- Main content area: flex-1 makes it expand to fill available space --}}
    <main class="flex-1">
      @yield('content')
    </main>

    {{-- Footer: will sit after main. With body as flex-col and main flex-1,
         footer will be pushed to the bottom when content is short. --}}
    <footer class="bg-gray-900 text-white py-6">
      <div class="container mx-auto px-6 text-center">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'SIMDES') }}. All rights reserved.</p>
      </div>
    </footer>

    {{-- AlpineJS (defer) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Page-specific scripts --}}
    @stack('scripts')
  </body>
</html>
