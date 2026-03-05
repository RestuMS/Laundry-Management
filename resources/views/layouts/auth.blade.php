<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LaundryService - @yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS is now compiled via Vite (no CDN needed) -->

    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #8cc3f7; /* Fallback color */
        }
    </style>
</head>
<body class="antialiased min-h-screen relative flex items-center justify-center p-4 overflow-hidden" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    
    <!-- Background Image -->
    <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat transition-opacity duration-1000 ease-out"
         :class="loaded ? 'opacity-100' : 'opacity-0'"
         style="background-image: url('{{ asset('images/background.png') }}');">
    </div>

    <!-- Main Content Container with Fade In -->
    <div class="relative z-10 w-full transition-all duration-700 ease-out transform"
         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        @yield('content')
    </div>
</body>
</html>
