<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>LaundryPro - @yield('title')</title>

    <!-- Preconnect to CDNs for faster DNS resolution -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Phosphor Icons (Modern Minimalist Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Alpine.js (Defer to avoid render blocking) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart JS (Defer to avoid render blocking) -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
    
    <!-- SweetAlert2 (Defer) -->
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind CSS (Play CDN blocks main thread, preconnect helps speed) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3B82F6', // Blue 500
                        'primary-light': '#EFF6FF', // Blue 50
                        secondary: '#64748B', // Slate 500
                        background: '#F8FAFC', // Slate 50
                        card: '#FFFFFF',
                    },
                    boxShadow: {
                        'floating': '0 10px 40px -10px rgba(0,0,0,0.08)',
                        'bottom-nav': '0 -4px 20px rgba(0,0,0,0.05)',
                        'soft': '0 2px 10px rgba(0,0,0,0.02)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-up': 'slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Vite disabled to fix reloading latency ->  {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}} -->

    <style>
        body {
            background-color: #F8FAFC;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-top: 1px solid rgba(241,245,249,0.8);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(241, 245, 249, 0.8);
        }

        /* Override glass-card to Mobile Floating Card Style */
        .glass-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            border: 1px solid rgba(241,245,249, 0.8);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        @media(max-width: 1023px){
            .glass-card:active {
                transform: scale(0.98);
            }
        }
        
        @media(min-width: 1024px){
            .hover-float:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 25px -5px rgba(0,0,0,0.08);
            }
        }
    </style>
</head>
<body class="text-slate-800 antialiased font-sans selection:bg-primary selection:text-white pb-safe" x-data="{ mobileMenuOpen: false }">

    <!-- Full Background Cover -->
    <div class="fixed inset-0 z-[-1] bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/bg-admin.png') }}'); background-position: left bottom;">
        <!-- Fallback gradient if image not found -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#EAF4FF]/80 via-[#DCEBFF]/80 to-[#CFE2FF]/90 mix-blend-overlay"></div>
    </div>

    @php
        $role = auth()->user()?->role ?? 'admin';
        
        // Define Menus Per Role
        $menus = [
            'admin' => [
                ['name' => 'Home', 'url' => route('dashboard'), 'icon' => 'ph-house', 'active' => request()->routeIs('dashboard')],
                ['name' => 'Order', 'url' => route('order.index'), 'icon' => 'ph-receipt', 'active' => request()->routeIs('order.*')],
                ['name' => 'Layanan', 'url' => route('layanan.index'), 'icon' => 'ph-washing-machine', 'active' => request()->routeIs('layanan.*')],
                ['name' => 'Pelanggan', 'url' => route('pelanggan.index'), 'icon' => 'ph-users', 'active' => request()->routeIs('pelanggan.*')],
                ['name' => 'Laporan', 'url' => route('laporan.index'), 'icon' => 'ph-chart-line-up', 'active' => request()->routeIs('laporan.*')],
                ['name' => 'Keuangan', 'url' => route('expense.index'), 'icon' => 'ph-wallet', 'active' => request()->routeIs('expense.*')],
                ['name' => 'Inventaris', 'url' => route('inventory.index'), 'icon' => 'ph-box-box', 'active' => request()->routeIs('inventory.*')],
                ['name' => 'Karyawan', 'url' => route('pengguna.index'), 'icon' => 'ph-identification-badge', 'active' => request()->routeIs('pengguna.*')],
                ['name' => 'Pengaturan', 'url' => route('settings.index'), 'icon' => 'ph-gear', 'active' => request()->routeIs('settings.*')],
            ],
            'kasir' => [
                ['name' => 'Home', 'url' => route('kasir'), 'icon' => 'ph-house', 'active' => request()->routeIs('kasir')],
                ['name' => 'Transaksi', 'url' => route('order.index'), 'icon' => 'ph-receipt', 'active' => request()->routeIs('order.*')],
                ['name' => 'Pelanggan', 'url' => route('pelanggan.index'), 'icon' => 'ph-users', 'active' => request()->routeIs('pelanggan.*')],
                ['name' => 'Pengaturan', 'url' => route('settings.index'), 'icon' => 'ph-gear', 'active' => request()->routeIs('settings.*')],
            ],
            'owner' => [
                ['name' => 'Home', 'url' => route('owner'), 'icon' => 'ph-house', 'active' => request()->routeIs('owner')],
                ['name' => 'Laporan', 'url' => route('laporan.index'), 'icon' => 'ph-chart-pie', 'active' => request()->routeIs('laporan.*')],
                ['name' => 'Pengeluaran', 'url' => route('expense.index'), 'icon' => 'ph-wallet', 'active' => request()->routeIs('expense.*')],
                ['name' => 'Inventaris', 'url' => route('inventory.index'), 'icon' => 'ph-box-box', 'active' => request()->routeIs('inventory.*')],
                ['name' => 'Profil', 'url' => route('settings.index'), 'icon' => 'ph-user-circle', 'active' => request()->routeIs('settings.*')],
            ]
        ];

        $currentNav = $menus[$role] ?? $menus['admin'];
        
        // Extract subset for Bottom Nav (Strictly Home, Action, Settings for mobile aesthetics)
        $bottomNavItems = [];
        $middleCreateUrl = route('order.create'); // FAB Center action
        
        // Find indices of Home and Settings based on role map
        $homeIndex = 0; // usually 0
        $settingsIndex = count($currentNav) - 1; // usually last element
        
        if ($role == 'owner') {
           $bottomNavItems = [$currentNav[0], $currentNav[$settingsIndex]];
        } elseif ($role == 'kasir') {
           // For Kasir, strict 3 icons only (Home, FAB, Order)
           $bottomNavItems = [$currentNav[0], $currentNav[1]];
        } else {
           $bottomNavItems = [$currentNav[0], $currentNav[1] /* Order index */, $currentNav[$settingsIndex]];
        }
    @endphp

    <!-- Mobile Top Header (Sticky) -->
    <header class="fixed top-0 inset-x-0 h-[65px] z-40 glass-header px-5 flex items-center justify-between lg:hidden transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center text-primary relative">
                <i class="ph-fill ph-drop text-xl"></i>
            </div>
            <div>
                <h1 class="text-[17px] font-bold text-slate-800 leading-tight tracking-tight">Laundry<span class="text-primary">Pro</span></h1>
            </div>
        </div>
        
        <div class="flex items-center gap-2.5">
            <!-- Notification Bell -->
            <button class="w-9 h-9 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition-colors relative shadow-soft">
                <i class="ph ph-bell text-lg"></i>
                <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
            </button>
            @if($role == 'admin')
            <a href="{{ route('settings.index') }}" class="w-9 h-9 rounded-full overflow-hidden border-2 border-white shadow-soft">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'User') }}&background=3B82F6&color=fff&bold=true" alt="Avatar" class="w-full h-full object-cover">
            </a>
            @else
            <!-- Logic Logout PWA untuk Owner dan Kasir (Akses Cepat Header) -->
            <a href="{{ route('logout') }}" onclick="return confirm('Apakah Anda yakin ingin Log Out?')" class="w-9 h-9 flex items-center justify-center rounded-full bg-red-50 text-red-600 border border-red-100 shadow-soft hover:bg-red-500 hover:text-white transition-colors">
                <i class="ph ph-sign-out text-base font-bold"></i>
            </a>
            @endif
        </div>
    </header>

    <div class="flex min-h-screen pt-[65px] lg:pt-0 pb-[80px] lg:pb-0">
        
        <!-- Desktop Sidebar (Hidden on Mobile) -->
        <aside class="hidden lg:flex flex-col w-[280px] fixed inset-y-0 left-0 bg-white border-r border-slate-100 z-30 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
            <div class="h-[80px] flex md:flex-col lg:flex-row items-center px-6 gap-3 pt-6 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-primary relative">
                    <i class="ph-fill ph-drop text-2xl"></i>
                </div>
                <h1 class="text-[20px] font-bold text-slate-800 tracking-tight">Laundry<span class="text-primary">Pro</span></h1>
            </div>

            <div class="px-6 pb-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'User') }}&background=3B82F6&color=fff&bold=true" class="w-10 h-10 rounded-full shadow-sm">
                    <div>
                        <div class="text-[14px] font-bold text-slate-700">{{ auth()->user()?->name ?? 'Admin' }}</div>
                        <div class="text-[12px] font-medium text-slate-400 capitalize">{{ $role }} Account</div>
                    </div>
                </div>
            </div>

            <p class="px-6 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 mt-2">Main Menu</p>
            
            <nav class="flex-1 px-4 overflow-y-auto space-y-1.5 no-scrollbar">
                @foreach($currentNav as $item)
                    <a href="{{ $item['url'] }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ $item['active'] ? 'bg-primary text-white shadow-[0_4px_12px_rgba(59,130,246,0.3)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                        <i class="ph {{ $item['icon'] }} text-[20px] {{ $item['active'] ? 'text-white ph-fill' : 'text-slate-400' }}"></i>
                        {{ $item['name'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 mt-auto mb-2">
                <a href="{{ route('logout') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold text-red-500 hover:bg-red-50 transition-colors">
                    <i class="ph ph-sign-out text-[20px]"></i>
                    Log Out
                </a>
            </div>
        </aside>

        <!-- Main Content (Safe area) -->
        <main class="flex-1 lg:ml-[280px] w-full relative max-w-full overflow-x-hidden p-4 sm:p-6 lg:p-8">
            
            <!-- Welcome Header (Desktop Only) -->
            <div class="hidden lg:flex items-center justify-between mb-8 animate-fade-in">
                <div>
                    <h2 class="text-[24px] font-bold text-slate-800 tracking-tight">@yield('header_title', 'Dashboard')</h2>
                    <p class="text-[14px] font-medium text-slate-500 mt-1">Pantau transaksi dan operasional dengan mudah.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="w-[42px] h-[42px] rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-primary transition-colors shadow-sm relative">
                        <i class="ph ph-bell text-[20px]"></i>
                        <span class="absolute top-[10px] right-[10px] w-[8px] h-[8px] bg-red-500 rounded-full border border-white"></span>
                    </button>
                    <a href="{{ route('order.create') }}" class="px-5 py-2.5 bg-primary text-white rounded-full font-bold text-[14px] shadow-[0_4px_12px_rgba(59,130,246,0.3)] hover:scale-105 transition-transform flex items-center gap-2">
                        <i class="ph ph-plus text-lg"></i> Transaksi Baru
                    </a>
                </div>
            </div>

            <!-- Global Alert Messages -->
            <div class="animate-slide-up w-full max-w-7xl mx-auto">
                @if(session('success'))
                    <div class="mb-5 bg-green-50 border border-green-100 text-green-700 px-5 py-3.5 rounded-xl text-[13.5px] font-semibold flex items-center gap-3 animate-fade-in shadow-sm">
                        <i class="ph-fill ph-check-circle text-[22px] text-green-500"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-5 bg-red-50 border border-red-100 text-red-700 px-5 py-3.5 rounded-xl text-[13.5px] font-semibold flex items-center gap-3 animate-fade-in shadow-sm">
                        <i class="ph-fill ph-warning-circle text-[22px] text-red-500"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Yield actual page content here -->
                @yield('content')
            </div>

        </main>
    </div>

    <!-- Mobile Bottom Navigation (Visible only on lg:hidden) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 glass-nav z-40 pb-safe shadow-bottom-nav px-2 rounded-t-[20px]">
        <div class="flex items-center justify-around h-[70px] max-w-md mx-auto relative">
            
            @foreach($bottomNavItems as $index => $item)
                @php
                    $isMiddleFab = false;
                    if ($role == 'admin') {
                        $isMiddleFab = ($index == 1);
                    } elseif ($role == 'kasir') {
                        $isMiddleFab = ($index == 0); // Inject exactly between Home and Order
                    }
                    $isMiddleOwnerSpace = ($index == 1) && ($role == 'owner');
                @endphp

                <!-- If role is owner and it's the middle, inject spacing -->
                @if($isMiddleOwnerSpace)
                    <div class="w-12 h-12 md:hidden"></div>
                @endif
                
                <a href="{{ $item['url'] }}" class="flex flex-col items-center justify-center gap-1 w-16 group focus:outline-none">
                    <div class="relative px-3 py-1 rounded-full transition-all duration-300 {{ $item['active'] && !request()->routeIs('order.create') ? 'bg-blue-50' : '' }}">
                        <i class="ph {{ $item['icon'] }} text-[24px] transition-colors {{ $item['active'] && !request()->routeIs('order.create') ? 'text-primary ph-fill' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    </div>
                    <span class="text-[10px] font-bold text-center {{ $item['active'] && !request()->routeIs('order.create') ? 'text-primary' : 'text-slate-400' }}">
                        {{ $item['name'] }}
                    </span>
                </a>

                <!-- If role is admin or kasir, inject the FAB Button right after the 1st item (index=0 output, then FAB, then index=1 output) wait! index==1 is actually the order page for kasir! -->
                @if($isMiddleFab)
                    <!-- Main center FAB -->
                    <a href="{{ $middleCreateUrl }}" class="relative -top-6 flex flex-col items-center justify-center group focus:outline-none z-10 mx-2">
                        <div class="w-[52px] h-[52px] rounded-full bg-primary flex items-center justify-center text-white shadow-[0_8px_20px_rgba(59,130,246,0.4)] transform transition-transform border-[4px] border-[#F8FAFC] active:scale-95">
                            <i class="ph ph-plus text-[24px] font-bold"></i>
                        </div>
                    </a>
                @endif
            @endforeach

            <!-- More Menu Burger -->
            @if(count($currentNav) > 4)
            <button @click="mobileMenuOpen = true" class="flex flex-col items-center justify-center gap-1 w-16 group focus:outline-none">
                <div class="relative px-3 py-1 rounded-full transition-all duration-300">
                    <i class="ph ph-squares-four text-[24px] text-slate-400 group-hover:text-slate-600"></i>
                </div>
                <span class="text-[10px] font-bold text-center text-slate-400">Menu</span>
            </button>
            @endif

        </div>
    </nav>

    <!-- Mobile Offcanvas Menu (Bottom Sheet) -->
    <div x-show="mobileMenuOpen" x-cloak class="lg:hidden fixed inset-0 z-50 flex flex-col justify-end" style="display: none;">
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
        
        <!-- Sheet Content -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="translate-y-full" 
             x-transition:enter-end="translate-y-0" 
             x-transition:leave="transition ease-in duration-200 transform" 
             x-transition:leave-start="translate-y-0" 
             x-transition:leave-end="translate-y-full" 
             class="relative w-full bg-white rounded-t-[30px] p-6 pb-12 shadow-2xl max-h-[75vh] flex flex-col">
            
            <!-- Handle bar -->
            <div class="w-12 h-[5px] bg-slate-200 rounded-full mx-auto mb-6"></div>
            
            <h3 class="text-[18px] font-bold text-slate-800 mb-5 px-2 tracking-tight">Semua Fitur Menu</h3>
            
            <div class="flex-1 overflow-y-auto no-scrollbar pb-6 px-4 -mt-2">
                <div class="grid grid-cols-4 md:grid-cols-5 gap-y-7 gap-x-4">
                    @foreach($currentNav as $item)
                    <a href="{{ $item['url'] }}" class="flex flex-col items-center gap-2 group active:scale-95 transition-transform">
                        <div class="w-[65px] h-[65px] rounded-[18px] flex items-center justify-center transition-colors shadow-[0_2px_10px_rgba(0,0,0,0.03)] {{ $item['active'] ? 'bg-blue-50/80 border border-blue-200 text-primary' : 'bg-white border border-slate-100 text-slate-500 hover:bg-slate-50' }}">
                            <i class="ph {{ $item['icon'] }} text-[28px] {{ $item['active'] ? 'ph-fill' : 'group-hover:text-primary' }}"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-center leading-tight mt-1 {{ $item['active'] ? 'text-primary' : 'text-slate-600' }}">{{ $item['name'] }}</span>
                    </a>
                    @endforeach
                    
                    <a href="{{ route('logout') }}" class="flex flex-col items-center gap-2 group active:scale-95 transition-transform">
                        <div class="w-[65px] h-[65px] rounded-[18px] bg-red-50/50 border border-red-100 flex items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.03)] text-red-500 hover:bg-red-50 transition-colors">
                            <i class="ph ph-sign-out text-[28px]"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-center leading-tight mt-1 text-red-600">Log Out</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
