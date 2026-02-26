<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Manajemen Laundry & Tracking - {{ $globalSettings['store_name'] ?? 'LaundryPro' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Alpine JS & Tailwind -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'],
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563EB',
                        secondary: '#38BDF8',
                    },
                    animation: {
                        'float': 'float 8s ease-in-out infinite',
                        'float-delayed': 'float 8s ease-in-out 4s infinite',
                        'float-slow': 'float 12s ease-in-out 2s infinite',
                        'pulse-glow': 'pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0) translateX(0)' },
                            '50%': { transform: 'translateY(-20px) translateX(10px)' },
                        },
                        'pulse-glow': {
                            '0%, 100%': { opacity: 1, boxShadow: '0 0 0 0 rgba(37, 99, 235, 0.4)' },
                            '50%': { opacity: .8, boxShadow: '0 0 0 10px rgba(37, 99, 235, 0)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #f8fafc; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
        }
        .progress-line-active { background: linear-gradient(90deg, #2563EB, #38BDF8); }
        .shimmer {
            background: #f6f7f8;
            background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-repeat: no-repeat;
            background-size: 800px 100%; 
            animation-duration: 1.2s;
            animation-fill-mode: forwards; 
            animation-iteration-count: infinite;
            animation-name: placeholderShimmer;
            animation-timing-function: linear;
        }
        @keyframes placeholderShimmer {
            0% { background-position: -468px 0; }
            100% { background-position: 468px 0; }
        }
    </style>
</head>
<body class="antialiased min-h-screen relative overflow-hidden font-sans text-slate-800" x-data="trackingApp()">
    
    <!-- Background Gradient & Animations -->
    <div class="fixed inset-0 z-0 bg-gradient-to-br from-[#f8fafc] via-[#e0f2fe] to-[#bae6fd]">
        <!-- Glass/Bubble Shapes -->
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-secondary/30 rounded-full mix-blend-multiply filter blur-3xl opacity-60 animate-float"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-primary/20 rounded-full mix-blend-multiply filter blur-3xl opacity-60 animate-float-delayed"></div>
        <div class="absolute top-[30%] left-[40%] w-[400px] h-[400px] bg-indigo-300/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-float-slow"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col p-4 md:p-8">
        
        <!-- Main Dual Panel Container -->
        <div class="m-auto w-full max-w-6xl lg:h-[90vh] glass-panel rounded-[2rem] md:rounded-[3rem] overflow-hidden flex flex-col lg:flex-row shadow-[0_20px_60px_-15px_rgba(37,99,235,0.2)]">
            
            <!-- LEFT PANEL: Illustration & Branding (Hidden on very small screens) -->
            <div class="hidden lg:flex w-full lg:w-5/12 bg-gradient-to-br from-primary to-blue-800 p-8 xl:p-12 text-white flex-col justify-between relative overflow-hidden group">
                <!-- Abstract Design inside Left Panel -->
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white/10 rounded-full filter blur-2xl transform group-hover:scale-110 transition-transform duration-700"></div>
                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-secondary/20 rounded-full filter blur-3xl transform group-hover:scale-110 transition-transform duration-700"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-lg flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                <circle cx="12" cy="14" r="4"></circle>
                                <line x1="8" y1="6" x2="16" y2="6"></line>
                                <line x1="8" y1="10" x2="8.01" y2="10"></line>
                                <path d="M16 10l2 4l-1 2l-2-4"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-poppins font-bold tracking-tight line-clamp-1">{{ $globalSettings['store_name'] ?? 'LaundryPro' }}</h1>
                    </div>
                    
                    <h2 class="text-3xl xl:text-4xl 2xl:text-5xl font-poppins font-black leading-tight mb-6">
                        Solusi <span class="text-secondary">Laundry</span> Premium Masa Kini.
                    </h2>
                    <p class="text-blue-100 text-sm xl:text-lg mb-8 leading-relaxed font-light">
                        Kelola bisnis laundry lebih profesional dengan sistem kasir yang cerdas, dan biarkan pelanggan melacak laundry mereka sesuka hati, real-time tanpa batas.
                    </p>
                </div>

                <!-- Features List in Left Panel -->
                <div class="relative z-10 space-y-3 xl:space-y-4">
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-3 xl:p-4 rounded-2xl hover:bg-white/20 transition-colors">
                        <div class="bg-secondary/20 p-2 rounded-lg text-secondary shrink-0">
                            <svg class="w-5 h-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="font-medium text-[14px] xl:text-[15px]">Tracking Cepat & Resposif</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-3 xl:p-4 rounded-2xl hover:bg-white/20 transition-colors">
                        <div class="bg-secondary/20 p-2 rounded-lg text-secondary shrink-0">
                            <svg class="w-5 h-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <span class="font-medium text-[14px] xl:text-[15px]">Keamanan Data Terjamin</span>
                    </div>
                </div>
                
                <!-- Bubble Elements Illustration Background -->
                <div class="absolute bottom-4 right-4 text-white/5 pointer-events-none">
                    <svg class="w-40 h-40 xl:w-48 xl:h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
            </div>

            <!-- RIGHT PANEL: Logic (Login & Tracking) -->
            <div class="w-full lg:w-7/12 p-6 md:p-12 relative h-auto lg:h-full overflow-y-auto custom-scrollbar">
                
                <!-- Top Nav / Back Button -->
                <div class="flex justify-end mb-4">
                    <p class="text-sm font-medium text-slate-400">Pusat Autentikasi & Layanan</p>
                </div>

                <!-- ALERTS -->
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl relative mb-6 font-medium text-[14px] flex items-center shadow-sm" role="alert">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl relative mb-6 font-medium text-[14px] flex items-center shadow-sm" role="alert">
                         <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- X-Alpine State wrapper for toggling between views smoothly -->
                <div class="relative w-full transition-all duration-500 transform origin-top" :class="result ? 'opacity-0 h-0 hidden' : 'opacity-100 h-auto'">
                    
                    <!-- 1. FORM LOGIN SIGN IN -->
                    <div class="mb-10">
                        <h2 class="text-3xl font-poppins font-black tracking-tight text-slate-800 mb-2">Sign In Area</h2>
                        <p class="text-slate-500 mb-8 font-medium">Bagi Admin, Kasir, atau Owner silakan login untuk mengelola sistem.</p>
                        
                        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                            @csrf
                            
                            <!-- Email -->
                            <div class="group">
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5 transition-colors group-focus-within:text-primary">Email Account</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                        <svg class="w-[20px] h-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                        class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : 'border-slate-200 focus:border-primary focus:ring-primary/20' }} rounded-xl text-slate-800 font-medium text-[15px] focus:outline-none focus:ring-4 focus:bg-white transition-all placeholder-slate-400" 
                                        placeholder="admin@laundry.com" />
                                </div>
                                @error('email')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium tracking-wide">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="group">
                                <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5 transition-colors group-focus-within:text-primary">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                        <svg class="w-[20px] h-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                        class="block w-full pl-11 pr-11 py-3.5 bg-slate-50/50 border {{ $errors->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : 'border-slate-200 focus:border-primary focus:ring-primary/20' }} rounded-xl text-slate-800 font-medium text-[15px] focus:outline-none focus:ring-4 focus:bg-white transition-all placeholder-slate-400" 
                                        placeholder="Enter your security key" />
                                    <!-- Eye button -->
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary focus:outline-none transition-colors">
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium tracking-wide">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Options -->
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 bg-white border-slate-300 rounded text-primary focus:ring-primary cursor-pointer">
                                    <label for="remember" class="ml-2.5 block text-sm text-slate-600 font-medium cursor-pointer select-none">
                                        Ingat Saya
                                    </label>
                                </div>
                                <div class="text-sm">
                                    <a href="#" class="font-bold text-primary hover:text-blue-700 transition-colors">Lupa Password?</a>
                                </div>
                            </div>

                            <!-- Summit Button -->
                            <div class="pt-3">
                                <button type="submit" class="w-full flex justify-center items-center gap-2 py-4 px-4 rounded-xl text-[15px] font-black tracking-wide text-white bg-gradient-to-r from-primary to-blue-600 shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none active:scale-[0.98]">
                                    Sign In securely
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 text-center">
                            <p class="text-[14px] font-medium text-slate-500">
                                Anggota baru tim kami? 
                                <a href="{{ route('register') }}" class="font-bold text-primary hover:underline transition-all">Register Account</a>
                            </p>
                        </div>
                    </div>


                    <!-- DIVIDER MODERN -->
                    <div class="relative py-8">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-4 bg-white/50 backdrop-blur-sm text-sm font-bold text-slate-400 rounded-full border border-slate-200/50">
                                ATAU CUSTOMER INGIN
                            </span>
                        </div>
                    </div>

                    
                    <!-- 2. TRACKING CUSTOMER TANPA LOGIN -->
                    <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-3xl p-6 md:p-8 border border-blue-100 shadow-[inset_0_2px_10px_rgba(255,255,255,1)] relative overflow-hidden group">
                        
                        <!-- Decorative bg tracking block -->
                        <div class="absolute top-0 right-0 -mr-12 -mt-12 w-32 h-32 bg-secondary/10 rounded-full filter blur-xl"></div>
                        
                        <div class="relative z-10">
                            <h3 class="text-2xl font-poppins font-black text-slate-800 mb-1 flex items-center gap-2">
                                Cek Status Laundry
                                <span class="bg-emerald-100 text-emerald-600 text-[10px] uppercase font-black px-2 py-1 rounded-full border border-emerald-200">Tanpa Login</span>
                            </h3>
                            <p class="text-[14px] font-medium text-slate-500 mb-6">Punya pesanan? Lacak realtime tanpa perlu masuk ke sistem.</p>

                            <!-- Mini Search Frame -->
                            <div class="w-full relative shadow-sm rounded-2xl bg-white focus-within:ring-4 focus-within:ring-primary/20 focus-within:border-primary border border-slate-200 overflow-hidden flex items-center transition-all p-1">
                                <div class="pl-4 pr-1 text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" x-model="searchQuery" @keydown.enter="searchOrder" 
                                       placeholder="Nomor HP / Kode ORD..." 
                                       class="flex-1 w-full bg-transparent border-0 px-3 py-3 text-[15px] font-semibold text-slate-700 placeholder-slate-400 focus:ring-0 outline-none">
                                
                                <button @click="searchOrder" :disabled="isLoading" class="bg-primary hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow transform hover:-translate-y-0.5 transition-all disabled:opacity-70 flex items-center gap-2 text-sm justify-center min-w-[120px]">
                                    <span x-show="!isLoading">Cari Order</span>
                                    <svg x-show="isLoading" style="display: none;" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </button>
                            </div>

                            <div x-show="errorMsg" style="display: none;" x-transition class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-600 text-[13px] font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="errorMsg"></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 3. HASIL TRACKING (Show when result is active) -->
                <div x-show="result" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;" class="w-full">
                    
                    <button @click="resetTracking()" class="mb-6 flex items-center gap-2 text-slate-500 hover:text-primary font-bold text-sm bg-slate-100 px-4 py-2 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Login
                    </button>

                    <!-- The Content Card Result -->
                    <div class="w-full bg-white rounded-[2rem] p-6 md:p-8 shadow-2xl relative overflow-hidden border border-slate-100">
                        
                        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-primary/5 to-secondary/5 rounded-bl-[100px] -z-10"></div>

                        <!-- Head Details -->
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[12px] font-bold tracking-wider text-slate-400 uppercase mb-1">Kode Order Valid</p>
                                    <h3 class="text-2xl font-black text-slate-800 tracking-tight" x-text="result.order_code"></h3>
                                    <p class="text-[14px] font-semibold text-primary mt-1 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> <span x-text="result.customer_name"></span></p>
                                </div>
                            </div>
                            <div class="text-left md:text-right bg-slate-50 p-4 rounded-2xl border border-slate-200 w-full md:w-auto shrink-0">
                                <p class="text-[11px] font-bold text-slate-400 uppercase mb-1">Estimasi Selesai / Jadi</p>
                                <p class="text-[16px] font-bold text-slate-800" x-text="result.estimated_finish || 'Menunggu Penilaian'"></p>
                            </div>
                        </div>

                        <!-- Workflow Timeline 3D -->
                        <div class="mb-10 bg-slate-50 p-6 rounded-[24px] border border-slate-200/60 shadow-inner">
                            <h4 class="text-[14px] font-bold text-slate-800 mb-8 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Lacak Progres Cucian
                            </h4>
                            
                            <div class="relative w-full overflow-x-auto pb-6 -mb-6 hide-scrollbar">
                                <div class="min-w-[600px] w-full relative pt-2 pb-8 px-4">
                                    <!-- Progress Background Line -->
                                    <div class="absolute top-[22px] left-[5%] right-[5%] h-1.5 bg-slate-200 rounded-full z-0"></div>
                                    
                                    <!-- Active Progress Line -->
                                    <div class="absolute top-[22px] left-[5%] h-1.5 progress-line-active rounded-full z-0 transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(37,99,235,0.5)]" :style="`width: ${calculateProgressWidth()}%`"></div>
        
                                    <div class="relative z-10 flex justify-between w-full">
                                        <template x-for="(status, index) in statuses" :key="index">
                                            <div class="flex flex-col items-center group w-1/6 relative">
                                                <!-- Node Ball -->
                                                <div class="w-11 h-11 rounded-full flex items-center justify-center shadow-md transition-all duration-500 mb-3 border-4"
                                                     :class="(index <= getCurrentStatusIndex()) ? getNodeBgClass(index) + ' border-white transform scale-110 shadow-blue-500/20 ' + (index === getCurrentStatusIndex() && result.status !== 'Diambil' ? 'animate-pulse-glow z-20' : 'z-10') : 'bg-slate-100 border-white text-slate-300 z-10'">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="status.icon"></path></svg>
                                                </div>
                                                
                                                <!-- Status Name -->
                                                <div class="text-[11px] font-bold text-center mt-1 transition-colors duration-300"
                                                     :class="(index <= getCurrentStatusIndex()) ? 'text-slate-800 tracking-tight' : 'text-slate-400'"
                                                     x-text="status.name"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Box Status Saat ini -->
                            <div class="mt-6 mx-auto w-full bg-slate-800 p-[1.5px] rounded-2xl overflow-hidden"
                                 :class="result.status === 'Selesai' ? 'bg-gradient-to-r from-emerald-400 to-green-500' : (result.status === 'Diambil' ? 'bg-gradient-to-r from-slate-300 to-slate-400' : 'bg-gradient-to-r from-primary to-secondary')">
                                <div class="bg-white rounded-[14px] p-4 flex items-center justify-between gap-3 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <span class="relative flex h-3 w-3" x-show="result.status !== 'Diambil'">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="result.status === 'Selesai' ? 'bg-emerald-400' : 'bg-blue-400'"></span>
                                          <span class="relative inline-flex rounded-full h-3 w-3" :class="result.status === 'Selesai' ? 'bg-emerald-500' : 'bg-blue-500'"></span>
                                        </span>
                                        <p class="text-[13px] font-bold text-slate-500">
                                            Status Terbaru:
                                        </p>
                                    </div>
                                    <div class="text-[15px] font-bold px-4 py-1.5 rounded-lg whitespace-nowrap" 
                                        :class="result.status === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : (result.status === 'Diambil' ? 'bg-slate-100 text-slate-700' : 'bg-blue-50 text-primary')" x-text="result.status">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- General Information Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-white border border-slate-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Layanan Laundry</p>
                                <p class="text-[14px] font-bold text-slate-800 line-clamp-1" x-text="result.service_name"></p>
                            </div>
                            <div class="bg-white border border-slate-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Berat / Pcs</p>
                                <p class="text-[14px] font-bold text-slate-800"><span x-text="result.weight"></span> <span class="text-[12px] text-slate-500 font-medium">Satuan</span></p>
                            </div>
                            <div class="bg-white border border-slate-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Tgl Masuk</p>
                                <p class="text-[13px] font-bold text-slate-800 whitespace-nowrap" x-text="result.created_at"></p>
                            </div>
                            <div class="bg-slate-800 p-4 rounded-2xl shadow-lg transform hover:-translate-y-1 transition-transform relative overflow-hidden group">
                                <div class="absolute inset-0 bg-gradient-to-r from-primary to-blue-600 opacity-20"></div>
                                <div class="relative z-10">
                                    <p class="text-[10px] font-bold text-blue-200 uppercase mb-1">Total Tagihan</p>
                                    <p class="text-[16px] md:text-[18px] font-black text-white mb-2">Rp <span x-text="formatRupiah(result.total_price)"></span></p>
                                    <p class="text-[10px] font-bold uppercase inline-flex items-center gap-1 bg-white/10 px-2 py-1 rounded-md"
                                       :class="result.payment_status === 'Lunas' ? 'text-emerald-400' : (result.payment_status === 'DP' ? 'text-amber-400' : 'text-red-400')">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                       <span x-text="result.payment_status"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
    </div>

    <!-- Fullscreen Loading Overlay for search trigger -->
    <div x-show="isLoading" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm" x-transition>
        <div class="bg-white p-6 rounded-3xl shadow-2xl flex flex-col items-center">
            <svg class="animate-spin w-10 h-10 text-primary mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <p class="text-sm font-bold text-slate-700">Mencari Database...</p>
        </div>
    </div>


    <!-- Alpine Logic Application -->
    <script>
        function trackingApp() {
            return {
                searchQuery: '',
                isLoading: false,
                errorMsg: '',
                result: null,
                
                statuses: [
                    { name: 'Diterima', icon: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4' },
                    { name: 'Dicuci', icon: 'M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10z M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M7 8h2' },
                    { name: 'Dikeringkan', icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z' },
                    { name: 'Disetrika', icon: 'M4 17h14c1.5 0 3-1.5 3-3 0-3-3-5-6-5H4v8z M4 9V6c0-1.5 1.5-3 3-3h5c1.5 0 3 1.5 3 3v3' },
                    { name: 'Quality Control', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' },
                    { name: 'Selesai', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
                    { name: 'Diambil', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z M9 15l2 2 4-4' }
                ],
                
                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const q = urlParams.get('q');
                    if (q) {
                        this.searchQuery = q;
                        // Hapus param dari URL biar clean tanpa refresh
                        window.history.replaceState({}, document.title, window.location.pathname);
                        this.searchOrder();
                    }
                },

                async searchOrder() {
                    if (!this.searchQuery) return;
                    
                    this.isLoading = true;
                    this.errorMsg = '';
                    
                    try {
                        const response = await fetch(`/api/track?q=${encodeURIComponent(this.searchQuery)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        const json = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(json.message || 'Data tidak ditemukan.');
                        }
                        
                        setTimeout(() => {
                            this.result = json.data;
                            this.isLoading = false;
                        }, 500);
                        
                    } catch (error) {
                        setTimeout(() => {
                            this.errorMsg = error.message;
                            this.isLoading = false;
                        }, 500);
                    }
                },

                resetTracking() {
                    this.result = null;
                    this.searchQuery = '';
                    this.errorMsg = '';
                },
                
                getCurrentStatusIndex() {
                    if (!this.result) return -1;
                    const dbStatus = this.result.status;
                    const map = {
                        'Diterima': 0, 'Dicuci': 1, 'Dikeringkan': 2, 'Disetrika': 3,
                        'Quality Control': 3,
                        'Selesai': 4, 'Diambil': 5
                    };
                    return map[dbStatus] !== undefined ? map[dbStatus] : 0;
                },
                
                calculateProgressWidth() {
                    const idx = this.getCurrentStatusIndex();
                    if (idx < 0) return 0;
                    if (idx === 0) return 0;
                    if (idx === 5) return 100;
                    return (idx / (this.statuses.length - 1)) * 100;
                },
                
                getNodeBgClass(index) {
                    if (index === 5 && this.getCurrentStatusIndex() === 5) return 'bg-slate-700 text-white border-slate-700';
                    if (index === 4 && this.getCurrentStatusIndex() >= 4) return 'bg-emerald-500 text-white border-emerald-500';
                    return 'bg-gradient-to-r from-primary to-secondary text-white';
                },

                formatRupiah(angka) {
                    var number_string = angka.toString().replace(/[^,\d]/g, ''),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    return rupiah;
                }
            }
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }
    </style>
</body>
</html>
