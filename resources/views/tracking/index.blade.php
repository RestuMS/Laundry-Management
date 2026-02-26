<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lacak Status Laundry - {{ $globalSettings['store_name'] ?? 'LaundryPro' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Alpine JS & Tailwind -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        primary: '#2563EB',
                        secondary: '#38BDF8',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'pulse-glow': 'pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
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
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
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
<body class="antialiased min-h-screen relative overflow-x-hidden font-sans text-slate-800" x-data="trackingApp()">
    
    <!-- Floating Bubbles Background (Decorative) -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-float"></div>
        <div class="absolute top-[20%] right-[-10%] w-[500px] h-[500px] bg-sky-300/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-float-delayed"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[600px] h-[600px] bg-indigo-300/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-float"></div>
    </div>

    <div class="relative z-10 w-full min-h-screen py-10 px-4 flex flex-col items-center">
        
        <!-- Login Quick Action -->
        <div class="absolute top-4 right-4 md:top-8 md:right-8 z-50">
            <a href="{{ route('login') }}" class="glass-panel px-4 md:px-5 py-2.5 rounded-2xl text-[13px] md:text-[14px] font-bold text-primary hover:bg-white/90 hover:shadow-xl transition-all flex items-center gap-2 group border border-blue-100">
                <svg class="w-4 h-4 text-primary group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                Sign In / Kelola
            </a>
        </div>
        
        <!-- Header -->
        <div class="text-center mb-10 mt-6 max-w-2xl mx-auto w-full" x-show="!result" x-transition.duration.700ms>
            <div class="inline-flex items-center justify-center p-4 bg-white/50 rounded-2xl shadow-sm mb-6 border border-white backdrop-blur-md">
                <img src="{{ asset('images/icon.png') }}" alt="Logo" class="w-12 h-12 object-contain mr-3">
                <h1 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">{{ $globalSettings['store_name'] ?? 'LaundryPro' }}</h1>
            </div>
            <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight leading-tight">Lacak Status Pakaian Anda Secara Real-time</h2>
            <p class="text-slate-500 mt-4 text-[15px] font-medium max-w-md mx-auto">Masukkan Nomor HP atau Kode Referensi Order Anda untuk melihat progres cucian saat ini.</p>
        </div>

        <!-- Search Box -->
        <div class="w-full max-w-xl mx-auto mb-10" :class="{'mt-4': result}">
            <div class="glass-panel rounded-3xl p-3 flex shadow-xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-secondary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <input type="text" x-model="searchQuery" @keydown.enter="searchOrder" placeholder="Contoh: 08123456789 atau ORD-62FA..." class="flex-1 w-full bg-transparent border-0 px-6 py-4 text-[16px] font-semibold text-slate-700 placeholder-slate-400 focus:ring-0 outline-none relative z-10">
                <button @click="searchOrder" :disabled="isLoading" class="bg-gradient-to-r from-primary to-secondary hover:from-blue-700 hover:to-blue-500 text-white font-bold px-8 py-4 rounded-2xl shadow-lg transform hover:-translate-y-0.5 transition-all relative z-10 disabled:opacity-70 flex items-center gap-2">
                    <span x-show="!isLoading">Lacak</span>
                    <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    
                    <svg x-show="isLoading" style="display: none;" class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
            
            <div x-show="errorMsg" style="display: none;" x-transition class="mt-4 p-4 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-[14px] font-semibold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-text="errorMsg"></span>
            </div>
        </div>

        <!-- Skeleton Loading -->
        <div x-show="isLoading" style="display: none;" class="w-full max-w-3xl glass-panel rounded-[32px] p-8 mt-4 shadow-xl">
            <div class="flex flex-col md:flex-row gap-8 mb-8">
                <div class="w-24 h-24 rounded-2xl shimmer shrink-0"></div>
                <div class="w-full space-y-4">
                    <div class="h-8 w-1/3 rounded-lg shimmer"></div>
                    <div class="h-4 w-1/4 rounded-lg shimmer"></div>
                    <br>
                    <div class="h-4 w-1/2 rounded-lg shimmer"></div>
                    <div class="h-4 w-1/3 rounded-lg shimmer"></div>
                </div>
            </div>
            <div class="h-12 w-full rounded-2xl shimmer mb-4"></div>
            <div class="h-20 w-full rounded-2xl shimmer"></div>
        </div>

        <!-- Result Card -->
        <template x-if="result">
            <div class="w-full max-w-3xl glass-panel rounded-[32px] p-0 md:p-2 mt-2 shadow-2xl relative overflow-hidden" x-transition.duration.700ms.scale.95>
                
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-bl-[100px] -z-10"></div>

                <div class="p-6 md:p-8">
                    <!-- Head Details -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center shadow-lg shadow-blue-500/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[12px] font-bold tracking-wider text-slate-400 uppercase mb-1">Kode Order</p>
                                <h3 class="text-2xl font-black text-slate-800 tracking-tight" x-text="result.order_code"></h3>
                                <p class="text-[14px] font-semibold text-primary mt-1 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> <span x-text="result.customer_name"></span></p>
                            </div>
                        </div>
                        <div class="text-left md:text-right bg-white/60 p-4 rounded-2xl border border-white w-full md:w-auto">
                            <p class="text-[12px] font-bold text-slate-400 uppercase mb-1">Estimasi Selesai</p>
                            <p class="text-[16px] font-bold text-slate-800" x-text="result.estimated_finish || 'Menunggu Penilaian'"></p>
                        </div>
                    </div>

                    <!-- Workflow Timeline 3D -->
                    <div class="mb-12 bg-white/40 p-6 md:p-8 rounded-[24px] border border-white">
                        <h4 class="text-[14px] font-bold text-slate-800 mb-8 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Status Pengerjaan
                        </h4>
                        
                        <div class="relative w-full max-w-4xl mx-auto py-2">
                            <!-- Progress Line Background -->
                            <div class="absolute top-[22px] left-[10%] right-[10%] h-1.5 bg-slate-200 rounded-full z-0"></div>
                            
                            <!-- Active Progress Line -->
                            <div class="absolute top-[22px] left-[10%] h-1.5 progress-line-active rounded-full z-0 transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(37,99,235,0.5)]" :style="`width: ${calculateProgressWidth()}%`"></div>

                            <div class="relative z-10 flex justify-between">
                                <template x-for="(status, index) in statuses" :key="index">
                                    <div class="flex flex-col items-center group w-1/5 relative">
                                        
                                        <!-- Node dot -->
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition-all duration-500 mb-3 border-4"
                                             :class="(index <= getCurrentStatusIndex()) ? getNodeBgClass(index) + ' border-white transform scale-110 shadow-blue-500/20 ' + (index === getCurrentStatusIndex() && result.status !== 'Diambil' ? 'animate-pulse-glow z-20' : 'z-10') : 'bg-slate-100 border-white text-slate-300 z-10'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="status.icon"></path></svg>
                                        </div>
                                        
                                        <!-- Label -->
                                        <div class="text-[12px] font-bold text-center mt-1 transition-colors duration-300 hidden md:block"
                                             :class="(index <= getCurrentStatusIndex()) ? 'text-slate-800' : 'text-slate-400'"
                                             x-text="status.name"></div>
                                        
                                        <!-- Tooltip for Mobile & Desktop -->
                                        <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-xl whitespace-nowrap z-30 pointer-events-none md:hidden"
                                             x-text="status.name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Current Status Text Alert -->
                        <div class="mt-8 mx-auto max-w-lg bg-gradient-to-r p-[1.5px] rounded-xl"
                             :class="result.status === 'Selesai' ? 'from-green-400 to-emerald-500' : (result.status === 'Diambil' ? 'from-slate-300 to-slate-400' : 'from-primary to-secondary')">
                            <div class="bg-white rounded-xl p-4 flex items-center justify-center gap-3">
                                <span class="relative flex h-3 w-3" x-show="result.status !== 'Diambil'">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="result.status === 'Selesai' ? 'bg-emerald-400' : 'bg-blue-400'"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3" :class="result.status === 'Selesai' ? 'bg-emerald-500' : 'bg-blue-500'"></span>
                                </span>
                                <p class="text-[14px] font-bold text-center" :class="result.status === 'Diambil' ? 'text-slate-600' : 'text-slate-800'">
                                    Status saat ini: <span x-text="result.status" :class="result.status === 'Selesai' ? 'text-emerald-600 font-black' : 'text-primary'"></span>
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Order Info Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                            <p class="text-[11px] font-bold text-slate-400 uppercase mb-1">Layanan</p>
                            <p class="text-[14px] font-bold text-slate-800" x-text="result.service_name"></p>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                            <p class="text-[11px] font-bold text-slate-400 uppercase mb-1">Berat / Qty</p>
                            <p class="text-[14px] font-bold text-slate-800"><span x-text="result.weight"></span> <span class="text-[12px] text-slate-500 font-medium">Satuan</span></p>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                            <p class="text-[11px] font-bold text-slate-400 uppercase mb-1">Tanggal Masuk</p>
                            <p class="text-[14px] font-bold text-slate-800" x-text="result.created_at"></p>
                        </div>
                        <div class="bg-primary/5 border border-primary/20 p-4 rounded-2xl">
                            <p class="text-[11px] font-bold text-primary opacity-80 uppercase mb-1">Total Biaya</p>
                            <p class="text-[16px] font-black text-primary">Rp <span x-text="formatRupiah(result.total_price)"></span></p>
                            <p class="text-[11px] font-bold mt-1 inline-block px-2 py-0.5 rounded-full"
                               :class="result.payment_status === 'Lunas' ? 'bg-green-100 text-green-700' : (result.payment_status === 'DP' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700')"
                               x-text="result.payment_status"></p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </template>
        
        <!-- Footer Info -->
        <div class="mt-auto pt-8 text-center text-[12px] text-slate-400 font-medium z-10" x-show="!result">
            &copy; {{ date('Y') }} {{ $globalSettings['store_name'] ?? 'LaundryPro' }}. All rights reserved.<br>
            Powered by Enterprise Tracking System.
        </div>

    </div>

    <script>
        function trackingApp() {
            return {
                searchQuery: '',
                isLoading: false,
                errorMsg: '',
                result: null,
                
                // Defined workflow
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
                    // Check URL parameter for auto search (QR CODE Scanner feature)
                    const urlParams = new URLSearchParams(window.location.search);
                    const q = urlParams.get('q');
                    if (q) {
                        this.searchQuery = q;
                        this.searchOrder();
                    }
                },

                async searchOrder() {
                    if (!this.searchQuery) return;
                    
                    this.isLoading = true;
                    this.errorMsg = '';
                    this.result = null;
                    
                    try {
                        const response = await fetch(`/api/track?q=${encodeURIComponent(this.searchQuery)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        const json = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(json.message || 'Terjadi kesalahan sistem.');
                        }
                        
                        // Fake slight delay for smooth transition effect if too fast
                        setTimeout(() => {
                            this.result = json.data;
                            this.isLoading = false;
                        }, 600);
                        
                    } catch (error) {
                        setTimeout(() => {
                            this.errorMsg = error.message;
                            this.isLoading = false;
                        }, 500);
                    }
                },
                
                getCurrentStatusIndex() {
                    if (!this.result) return -1;
                    const dbStatus = this.result.status;
                    
                    // Map db string to array index
                    const map = {
                        'Diterima': 0, 'Dicuci': 1, 'Dikeringkan': 2, 'Disetrika': 3,
                        'Quality Control': 3, // Group QC with Disetrika or keep it at visual 3
                        'Selesai': 4, 'Diambil': 5
                    };
                    
                    return map[dbStatus] !== undefined ? map[dbStatus] : 0;
                },
                
                calculateProgressWidth() {
                    const idx = this.getCurrentStatusIndex();
                    if (idx < 0) return 0;
                    if (idx === 0) return 0;
                    if (idx === 5) return 100;
                    return (idx / (this.statuses.length - 1)) * 100; // Assuming 6 items = 5 segments. 1/5 = 20%
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
</body>
</html>
