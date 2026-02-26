<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LaundryPro - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#5B8DEF',
                        secondary: '#8FB8FF',
                        accent: {
                            success: '#59C98C',
                            warning: '#FFB84D',
                            danger: '#FF7A7A'
                        },
                        cloud: {
                            100: '#EAF4FF',
                            200: '#DCEBFF',
                            300: '#CFE2FF'
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px 0 rgba(91, 141, 239, 0.08)',
                        'float': '0 8px 30px 0 rgba(91, 141, 239, 0.15)',
                    }
                }
            }
        }
    </script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Vite for Auto Refresh -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #EAF4FF;
            /* In case bg-admin.png doesn't load/cover everything */
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 20px 0 rgba(91, 141, 239, 0.08);    
        }

        /* Hover floating animation */
        .hover-float {
            transition: all 0.3s ease-in-out;
        }
        .hover-float:hover {
            transform: scale(1.02) translateY(-4px);
            box-shadow: 0 12px 30px 0 rgba(91, 141, 239, 0.15);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #8FB8FF;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #5B8DEF;
        }
    </style>
</head>
<body class="antialiased min-h-screen text-slate-700 overflow-hidden" x-data="{ sidebarOpen: true }">
    
    <!-- Full Background Cover -->
    <div class="fixed inset-0 z-[-1] bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/bg-admin.png') }}'); background-position: left bottom;">
        <!-- Fallback gradient if image not found -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#EAF4FF]/80 via-[#DCEBFF]/80 to-[#CFE2FF]/90 mix-blend-overlay"></div>
    </div>

    <div class="flex h-screen w-full relative z-0">
        <!-- Sidebar -->
        <!-- Sidebar -->
        <aside class="glass-panel w-72 h-full flex flex-col transition-all duration-300 z-20 flex-shrink-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full absolute'">
            
            <!-- Logo area -->
            <div class="h-20 flex items-center px-6 gap-3 pt-4 mb-4">
                <div class="w-10 h-10 bg-white/90 rounded-xl shadow-sm flex items-center justify-center text-primary relative">
                    <img src="{{ asset('images/icon.png') }}" alt="LaundryPro Logo" class="w-8 h-8 object-contain">
                </div>
                <h1 class="text-2xl font-bold text-primary tracking-tight">{{ $globalSettings['store_name'] ?? 'LaundryPro' }}</h1>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                
                @php
                    $role = Auth::user()->role ?? 'admin';
                    
                    $allNavItems = [
                        'admin' => [
                            ['name' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'active' => request()->routeIs('dashboard')],
                            ['name' => 'Order', 'url' => route('order.index'), 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'active' => request()->routeIs('order.index')],
                            ['name' => 'Layanan', 'url' => route('layanan.index'), 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'active' => request()->routeIs('layanan.index')],
                            ['name' => 'Pelanggan', 'url' => route('pelanggan.index'), 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'active' => request()->routeIs('pelanggan.index')],
                            ['name' => 'Laporan', 'url' => route('laporan.index'), 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'active' => request()->routeIs('laporan.index')],
                            ['name' => 'Karyawan / Kasir', 'url' => route('pengguna.index'), 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'active' => request()->routeIs('pengguna.index')],
                            ['name' => 'Pengaturan', 'url' => route('settings.index'), 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z', 'active' => request()->routeIs('settings.index')],
                        ],
                        'kasir' => [
                            ['name' => 'Dashboard Kasir', 'url' => route('kasir'), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'active' => request()->routeIs('kasir')],
                            ['name' => 'Order Baru', 'url' => route('order.index'), 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'active' => request()->routeIs('order.index')],
                            ['name' => 'Data Pelanggan', 'url' => route('pelanggan.index'), 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'active' => request()->routeIs('pelanggan.index')],
                            ['name' => 'Pengaturan Kasir', 'url' => route('settings.index'), 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z', 'active' => request()->routeIs('settings.index')],
                        ],
                        'owner' => [
                            ['name' => 'Executive View', 'url' => route('owner'), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'active' => request()->routeIs('owner')],
                            ['name' => 'Laporan Bisnis', 'url' => route('laporan.index'), 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'active' => request()->routeIs('laporan.index')],
                            ['name' => 'Pelanggan & VIP', 'url' => route('pelanggan.index'), 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'active' => request()->routeIs('pelanggan.index')],
                            ['name' => 'Profil / Pengaturan', 'url' => route('settings.index'), 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z', 'active' => request()->routeIs('settings.index')],
                        ]
                    ];
                    
                    $navItems = $allNavItems[$role] ?? $allNavItems['admin'];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ $item['url'] ?? '#' }}" class="group flex items-center justify-between px-4 py-3.5 rounded-xl text-[14.5px] font-medium transition-all duration-300 relative overflow-hidden {{ $item['active'] ? 'text-white' : 'text-slate-600 hover:text-white' }}">
                        @if($item['active'])
                            <div class="absolute inset-0 bg-gradient-to-r from-[#7FB3FF] to-[#5B8DEF] z-0 shadow-md"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-r from-[#7FB3FF] to-[#5B8DEF] opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0 shadow-md"></div>
                        @endif
                        
                        <div class="flex items-center gap-3.5 relative z-10 w-full">
                            <svg class="w-[22px] h-[22px] {{ $item['active'] ? 'text-white' : 'text-primary group-hover:text-white' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $item['icon'] }}"></path>
                            </svg>
                            {{ $item['name'] }}
                        </div>
                        

                    </a>
                @endforeach
            </nav>

            <!-- Logout Bottom -->
            <div class="p-4 mt-auto mb-4 border-t border-white/40">
                <a href="{{ route('logout') }}" class="group flex items-center justify-between px-4 py-3.5 rounded-xl text-[14.5px] font-medium text-slate-600 hover:text-white transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-400 to-red-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0 shadow-md"></div>
                    <div class="flex items-center gap-3.5 relative z-10">
                        <svg class="w-[22px] h-[22px] text-red-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </div>
                </a>
            </div>
        </aside>

        <!-- Main Content area -->
        <main class="flex-1 flex flex-col h-full overflow-hidden relative">
            
            <!-- Top Navbar -->
            <header class="h-[88px] flex items-center justify-between px-8 bg-white/20 backdrop-blur-sm border-b border-white/30 z-10">
                
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-600 hover:text-primary transition-colors focus:outline-none p-1 rounded-md bg-white/50 hover:bg-white/80">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h2 class="text-[22px] font-semibold text-slate-700 tracking-tight">@yield('header_title', 'Admin Dashboard')</h2>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Notification Bell & Avatar -->
                    <div class="flex items-center gap-1">
                        <div class="relative w-10 h-10 rounded-full flex items-center justify-center cursor-pointer hover:bg-white/50 transition-colors">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=6B9DF2&color=fff&rounded=true&bold=true" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                        </div>
                        
                        <button class="relative w-10 h-10 translate-x-1 rounded-full flex items-center justify-center text-slate-500 hover:text-slate-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span class="absolute top-[6px] right-[6px] w-[14px] h-[14px] rounded-full bg-[#ED6A6A] border-2 border-white flex items-center justify-center text-[8px] font-bold text-white">5</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-1 cursor-pointer text-slate-600 hover:text-primary transition-colors pl-2">
                        <span class="text-[13px] font-semibold">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </header>

            <!-- Scrollable Page Content -->
            <div class="flex-1 overflow-y-auto w-full">
                <div class="p-8 pb-20">
                    @yield('content')
                </div>
            </div>

        </main>
    </div>

    @stack('scripts')
</body>
</html>
