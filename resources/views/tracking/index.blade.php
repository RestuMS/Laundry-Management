<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lacak Status Laundry - {{ $globalSettings['store_name'] ?? 'LaundryPro' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Alpine JS & Tailwind -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        'countdown-pulse': 'countdown-pulse 1s ease-in-out infinite',
                        'slide-in': 'slideIn 0.5s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        'pulse-glow': {
                            '0%, 100%': { opacity: 1, boxShadow: '0 0 0 0 rgba(37, 99, 235, 0.4)' },
                            '50%': { opacity: .8, boxShadow: '0 0 0 10px rgba(37, 99, 235, 0)' },
                        },
                        'countdown-pulse': {
                            '0%, 100%': { transform: 'scale(1)' },
                            '50%': { transform: 'scale(1.02)' },
                        },
                        'slideIn': {
                            '0%': { opacity: 0, transform: 'translateY(20px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' },
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
        .timeline-connector {
            background: linear-gradient(180deg, #3B82F6, #60A5FA);
        }
        .timeline-connector-pending {
            background: repeating-linear-gradient(
                180deg,
                #E2E8F0 0px,
                #E2E8F0 6px,
                transparent 6px,
                transparent 12px
            );
        }
    </style>

    <!-- Midtrans Snap JS -->
    <script type="text/javascript" 
            src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
</head>
<body class="antialiased min-h-screen relative overflow-x-hidden font-sans text-slate-800" x-data="trackingApp()">
    
    <!-- Floating Bubbles Background (Decorative) -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-float"></div>
        <div class="absolute top-[20%] right-[-10%] w-[500px] h-[500px] bg-sky-300/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-float-delayed"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[600px] h-[600px] bg-indigo-300/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-float"></div>
    </div>

    <div class="relative z-10 w-full min-h-screen py-10 px-4 flex flex-col items-center">
        
        <!-- Header -->
        <div class="text-center mb-10 mt-6 max-w-2xl mx-auto w-full" x-show="!result" x-transition.duration.700ms>
            <div class="inline-flex items-center justify-center p-4 bg-white/50 rounded-2xl shadow-sm mb-6 border border-white backdrop-blur-md">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-12 h-12 object-cover mr-3 rounded-xl shadow-md shadow-blue-500/20">
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
            <div class="w-full max-w-4xl bg-white rounded-[32px] p-0 mt-2 shadow-2xl relative overflow-hidden ring-1 ring-slate-100" x-transition.duration.700ms.scale.95>
                
                <!-- Background decorative element for top right -->
                <div class="absolute top-0 right-0 w-2/5 h-64 bg-[#F2F8FF] rounded-bl-[80px] z-0"></div>

                <div class="p-8 md:p-12 relative z-10">
                    <!-- Head Details -->
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8">
                        <div class="flex items-center gap-5">
                            <div class="w-[72px] h-[72px] rounded-[24px] bg-[#3B82F6] text-white flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[12px] font-black tracking-widest text-[#94A3B8] uppercase mb-1">KODE ORDER</p>
                                <h3 class="text-[28px] font-black text-[#1E293B] tracking-tight leading-none mb-2" x-text="result.order_code"></h3>
                                <p class="text-[15px] font-bold text-[#3B82F6] flex items-center gap-1.5 cursor-default">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> 
                                    <span x-text="result.customer_name"></span>
                                </p>
                            </div>
                        </div>
                        <!-- Countdown Timer / Estimated Finish -->
                        <div class="text-left md:text-center bg-white p-5 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] w-full md:w-auto mt-4 md:mt-0 z-10 border border-[#F1F5F9]">
                            <template x-if="result.countdown_target && result.status !== 'Selesai' && result.status !== 'Diambil'">
                                <div>
                                    <p class="text-[11px] font-black text-[#94A3B8] uppercase tracking-widest mb-2">⏰ ESTIMASI SELESAI</p>
                                    <p class="text-[14px] font-bold text-[#64748B] mb-3" x-text="result.estimated_finish"></p>
                                    <!-- Live Countdown -->
                                    <div class="flex items-center justify-center gap-2" x-data="countdown(result.countdown_target)">
                                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl px-3 py-2 min-w-[52px] text-center shadow-md">
                                            <span class="text-[20px] font-black block leading-none" x-text="days">0</span>
                                            <span class="text-[9px] font-bold uppercase tracking-widest opacity-80">Hari</span>
                                        </div>
                                        <span class="text-slate-300 font-black text-lg">:</span>
                                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl px-3 py-2 min-w-[52px] text-center shadow-md">
                                            <span class="text-[20px] font-black block leading-none" x-text="hours">0</span>
                                            <span class="text-[9px] font-bold uppercase tracking-widest opacity-80">Jam</span>
                                        </div>
                                        <span class="text-slate-300 font-black text-lg">:</span>
                                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl px-3 py-2 min-w-[52px] text-center shadow-md">
                                            <span class="text-[20px] font-black block leading-none" x-text="minutes">0</span>
                                            <span class="text-[9px] font-bold uppercase tracking-widest opacity-80">Mnt</span>
                                        </div>
                                        <span class="text-slate-300 font-black text-lg">:</span>
                                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl px-3 py-2 min-w-[52px] text-center shadow-md animate-countdown-pulse">
                                            <span class="text-[20px] font-black block leading-none" x-text="seconds">0</span>
                                            <span class="text-[9px] font-bold uppercase tracking-widest opacity-80">Dtk</span>
                                        </div>
                                    </div>
                                    <p class="text-[11px] font-semibold text-slate-400 mt-2 italic" x-show="isExpired">⚡ Proses mungkin sedikit lebih lama, mohon bersabar</p>
                                </div>
                            </template>
                            <template x-if="result.status === 'Selesai'">
                                <div class="text-center">
                                    <div class="text-[48px] mb-1">🎉</div>
                                    <p class="text-[16px] font-black text-green-600">Cucian Siap Diambil!</p>
                                    <p class="text-[12px] text-slate-400 font-medium mt-1">Silakan ambil di loket kami</p>
                                </div>
                            </template>
                            <template x-if="result.status === 'Diambil'">
                                <div class="text-center">
                                    <div class="text-[48px] mb-1">✅</div>
                                    <p class="text-[16px] font-black text-emerald-600">Sudah Diambil</p>
                                    <p class="text-[12px] text-slate-400 font-medium mt-1">Terima kasih!</p>
                                </div>
                            </template>
                            <template x-if="!result.countdown_target && result.status !== 'Selesai' && result.status !== 'Diambil'">
                                <div>
                                    <p class="text-[11px] font-black text-[#94A3B8] uppercase tracking-widest mb-1">ESTIMASI SELESAI</p>
                                    <p class="text-[18px] font-black text-[#1E293B]">Menunggu Penilaian</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Progress Bar Visual -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-[14px] font-bold text-slate-700 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Progress Keseluruhan
                            </h4>
                            <span class="text-[14px] font-black text-blue-600" x-text="result.progress_percentage + '%'"></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 via-blue-400 to-sky-400 transition-all duration-1000 ease-out relative"
                                 :style="`width: ${result.progress_percentage}%`">
                                <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Timeline (Vertical) -->
                    <div class="mb-8">
                        <h4 class="text-[16px] font-bold text-[#1E293B] mb-6 flex items-center gap-2">
                            <svg class="w-[22px] h-[22px] text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Timeline Status Pengerjaan
                        </h4>
                        
                        <div class="relative ml-2 md:ml-6">
                            <template x-for="(step, index) in result.status_timeline" :key="index">
                                <div class="flex items-start gap-4 mb-0 relative" :class="{'animate-slide-in': true}" :style="`animation-delay: ${index * 100}ms`">
                                    <!-- Timeline Node -->
                                    <div class="flex flex-col items-center shrink-0 relative z-10">
                                        <!-- Circle -->
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-[3px] transition-all duration-500 shrink-0"
                                             :class="{
                                                'bg-blue-500 border-blue-300 text-white shadow-[0_0_16px_rgba(59,130,246,0.4)]': index === getCurrentStatusIndex() && result.status !== 'Diambil',
                                                'bg-green-500 border-green-300 text-white shadow-[0_0_16px_rgba(34,197,94,0.4)]': step.completed && (index < getCurrentStatusIndex() || result.status === 'Diambil'),
                                                'bg-white border-slate-200 text-slate-300': !step.completed
                                             }">
                                            <!-- Check for completed -->
                                            <svg x-show="step.completed && index < getCurrentStatusIndex()" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <!-- Pulse for current -->
                                            <div x-show="index === getCurrentStatusIndex() && result.status !== 'Diambil'" class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                            <!-- Check for final -->
                                            <svg x-show="step.completed && result.status === 'Diambil'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <!-- Number for pending -->
                                            <span x-show="!step.completed" class="text-[12px] font-black" x-text="index + 1"></span>
                                        </div>
                                        <!-- Connector Line -->
                                        <div x-show="index < result.status_timeline.length - 1"
                                             class="w-[3px] h-12 rounded-full"
                                             :class="step.completed ? 'timeline-connector' : 'timeline-connector-pending'">
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="pt-1.5 pb-6 flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-[14px] font-bold"
                                                  :class="{
                                                    'text-blue-600': index === getCurrentStatusIndex() && result.status !== 'Diambil',
                                                    'text-slate-800': step.completed && index !== getCurrentStatusIndex(),
                                                    'text-slate-400': !step.completed
                                                  }"
                                                  x-text="step.status"></span>
                                            <!-- Current badge -->
                                            <span x-show="index === getCurrentStatusIndex() && result.status !== 'Diambil'"
                                                  class="text-[10px] font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full animate-pulse">
                                                Saat ini
                                            </span>
                                            <!-- Completed badge -->
                                            <span x-show="step.completed && index < getCurrentStatusIndex()"
                                                  class="text-[10px] font-bold bg-green-100 text-green-600 px-2 py-0.5 rounded-full">
                                                Selesai
                                            </span>
                                        </div>
                                        <!-- Timestamp -->
                                        <p x-show="step.timestamp" class="text-[12px] font-medium text-slate-400 mt-1 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span x-text="step.timestamp ? formatTimestamp(step.timestamp) : ''"></span>
                                            <template x-if="step.changed_by">
                                                <span class="text-slate-300">• oleh <span x-text="step.changed_by" class="text-slate-400"></span></span>
                                            </template>
                                        </p>
                                        <p x-show="!step.timestamp && !step.completed" class="text-[12px] font-medium text-slate-300 mt-1 italic">
                                            Menunggu proses...
                                        </p>
                                        <p x-show="!step.timestamp && step.completed" class="text-[12px] font-medium text-slate-400 mt-1 italic flex items-center gap-1.5" title="Status ini dilewati (diselesaikan otomatis)">
                                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            Otomatis selesai
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Order Info Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                        <div class="bg-[#F8FAFC] p-5 rounded-[20px] ring-1 ring-slate-100">
                            <p class="text-[11px] font-black text-[#94A3B8] uppercase tracking-widest mb-2">LAYANAN</p>
                            <p class="text-[16px] font-black text-[#1E293B] leading-tight" x-text="result.service_name"></p>
                        </div>
                        <div class="bg-[#F8FAFC] p-5 rounded-[20px] ring-1 ring-slate-100">
                            <p class="text-[11px] font-black text-[#94A3B8] uppercase tracking-widest mb-2">BERAT / QTY</p>
                            <p class="text-[16px] font-black text-[#1E293B]"><span x-text="parseFloat(result.weight).toFixed(2)"></span> <span class="text-[14px] text-[#64748B] font-medium">Satuan</span></p>
                        </div>
                        <div class="bg-[#F8FAFC] p-5 rounded-[20px] ring-1 ring-slate-100">
                            <p class="text-[11px] font-black text-[#94A3B8] uppercase tracking-widest mb-2">TANGGAL MASUK</p>
                            <p class="text-[16px] font-black text-[#1E293B]" x-text="result.created_at"></p>
                        </div>
                        <div class="bg-[#F0F7FF] border border-[#E0EFFF] p-5 rounded-[20px] relative flex flex-col justify-center">
                            <p class="text-[11px] font-black text-[#3B82F6] uppercase tracking-widest mb-1">TOTAL BIAYA</p>
                            <p class="text-[20px] font-black text-[#3B82F6] mb-1">Rp <span x-text="formatRupiah(result.total_price)"></span></p>
                            <div>
                                <span class="text-[11px] font-black inline-block px-3 py-1 rounded-[8px] text-[#10B981] bg-[#D1FAE5]"
                                      x-show="result.payment_status === 'Lunas'">Lunas</span>
                                <span class="text-[11px] font-black inline-block px-3 py-1 rounded-[8px] text-[#F59E0B] bg-[#fef3c7]"
                                      x-show="result.payment_status === 'DP'">DP Sebagian</span>
                                <span class="text-[11px] font-black inline-block px-3 py-1 rounded-[8px] text-[#EF4444] bg-[#FEE2E2]"
                                      x-show="result.payment_status === 'Belum Bayar' || result.payment_status === 'Belum Lunas'">Belum Lunas</span>
                            </div>
                            
                            <!-- Midtrans Payment Button -->
                            <template x-if="result.remaining_balance > 0">
                                <div class="mt-4 pt-3 border-t border-blue-200 border-dashed">
                                    <p class="text-[11px] font-bold text-slate-500 mb-2 flex justify-between items-center">
                                        Sisa Bayar: 
                                        <span class="text-blue-700 text-[13px] font-black">Rp <span x-text="formatRupiah(result.remaining_balance)"></span></span>
                                    </p>
                                    <button @click="payWithMidtrans" :disabled="isPaying" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-xl text-[12px] shadow-sm shadow-blue-500/30 transition-all flex justify-center items-center gap-2 transform hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed">
                                        <svg x-show="isPaying" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <svg x-show="!isPaying" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        <span x-text="isPaying ? 'Harap Tunggu...' : 'Bayar Sekarang'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Photo Documentation Gallery (for customers) -->
                    <template x-if="result.photos && (result.photos.masuk?.length || result.photos.proses?.length || result.photos.selesai?.length)">
                        <div class="mt-8">
                            <h4 class="text-[16px] font-bold text-[#1E293B] mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Dokumentasi Foto Cucian
                            </h4>

                            <template x-for="[type, label, color] in [['masuk', '📥 Saat Masuk', 'blue'], ['proses', '🔄 Saat Proses', 'amber'], ['selesai', '✅ Saat Selesai', 'emerald']]" :key="type">
                                <div x-show="result.photos[type]?.length > 0" class="mb-4">
                                    <p class="text-[12px] font-bold mb-2 uppercase tracking-widest"
                                       :class="color === 'blue' ? 'text-blue-500' : (color === 'amber' ? 'text-amber-500' : 'text-emerald-500')"
                                       x-text="label"></p>
                                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                                        <template x-for="photo in result.photos[type]" :key="photo.url">
                                            <div class="relative group rounded-xl overflow-hidden border border-slate-100 shadow-sm aspect-square cursor-pointer" @click="lightboxUrl = photo.url; lightboxOpen = true">
                                                <img :src="photo.url" :alt="photo.caption || 'Dokumentasi'" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <p class="text-[10px] text-white font-medium truncate" x-text="photo.caption || photo.created_at"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Rating Section: Only when Diambil -->
                    <template x-if="result.status === 'Diambil'">
                        <div class="mt-8 border-t border-slate-100 pt-8">
                            <!-- Already Rated -->
                            <template x-if="result.has_rated">
                                <div class="text-center bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-6 border border-amber-100">
                                    <div class="text-4xl mb-2">⭐</div>
                                    <p class="text-[15px] font-bold text-amber-700 mb-1">Terima kasih atas penilaian Anda!</p>
                                    <div class="flex items-center justify-center gap-1 mb-2">
                                        <template x-for="i in 5" :key="i">
                                            <svg class="w-6 h-6 transition-colors" :class="i <= result.rating.stars ? 'text-amber-400' : 'text-slate-200'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </template>
                                    </div>
                                    <p x-show="result.rating.comment" class="text-[13px] text-amber-600 font-medium italic" x-text="'\"' + result.rating.comment + '\"'"></p>
                                </div>
                            </template>

                            <!-- Rating Form -->
                            <template x-if="!result.has_rated">
                                <div x-data="{
                                    stars: 0,
                                    hovered: 0,
                                    comment: '',
                                    isSubmitting: false,
                                    successMsg: '',
                                    errorMsg: '',
                                    async submitRating() {
                                        if (!this.stars) return;
                                        this.isSubmitting = true;
                                        this.errorMsg = '';
                                        try {
                                            const res = await fetch('/api/rating', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                                                },
                                                body: JSON.stringify({ order_code: result.order_code, rating: this.stars, comment: this.comment })
                                            });
                                            const data = await res.json();
                                            if (!res.ok) throw new Error(data.message || 'Gagal mengirim rating.');
                                            this.successMsg = data.message;
                                            result.has_rated = true;
                                            result.rating = { stars: this.stars, comment: this.comment };
                                        } catch(e) {
                                            this.errorMsg = e.message;
                                        } finally {
                                            this.isSubmitting = false;
                                        }
                                    }
                                }">
                                    <div class="text-center mb-5">
                                        <div class="text-3xl mb-2">🎉</div>
                                        <h4 class="text-[16px] font-bold text-slate-800 mb-1">Cucian sudah diambil!</h4>
                                        <p class="text-[13px] text-slate-500 font-medium">Bagaimana pengalaman Anda menggunakan layanan kami?</p>
                                    </div>

                                    <!-- Stars -->
                                    <div class="flex items-center justify-center gap-2 mb-4">
                                        <template x-for="i in 5" :key="i">
                                            <button
                                                @click="stars = i"
                                                @mouseenter="hovered = i"
                                                @mouseleave="hovered = 0"
                                                class="transition-transform hover:scale-110 focus:outline-none"
                                            >
                                                <svg class="w-10 h-10 transition-colors duration-150"
                                                     :class="i <= (hovered || stars) ? 'text-amber-400' : 'text-slate-200'"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Star Label -->
                                    <p class="text-center text-[13px] font-bold mb-4 h-5"
                                       :class="stars >= 4 ? 'text-emerald-500' : (stars >= 3 ? 'text-amber-500' : (stars > 0 ? 'text-red-400' : 'text-slate-300'))">
                                        <span x-show="stars === 1">😞 Sangat Buruk</span>
                                        <span x-show="stars === 2">😐 Kurang Memuaskan</span>
                                        <span x-show="stars === 3">🙂 Cukup Baik</span>
                                        <span x-show="stars === 4">😊 Memuaskan</span>
                                        <span x-show="stars === 5">🤩 Luar Biasa!</span>
                                        <span x-show="stars === 0">Pilih bintang penilaian</span>
                                    </p>

                                    <!-- Comment -->
                                    <textarea
                                        x-model="comment"
                                        rows="3"
                                        maxlength="300"
                                        placeholder="Tulis komentar (opsional)... misal: ramah, pelayanan cepat, baju harum"
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 rounded-xl font-medium text-[13px] outline-none transition-all resize-none mb-3"
                                    ></textarea>

                                    <!-- Alerts -->
                                    <div x-show="errorMsg" class="mb-3 p-3 bg-red-50 text-red-600 rounded-xl text-[13px] font-bold border border-red-200 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span x-text="errorMsg"></span>
                                    </div>

                                    <!-- Submit -->
                                    <button
                                        @click="submitRating"
                                        :disabled="!stars || isSubmitting"
                                        class="w-full py-3.5 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-500 hover:to-orange-600 text-white rounded-xl font-bold text-[14px] shadow-[0_4px_15px_rgba(251,191,36,0.4)] transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                    >
                                        <svg x-show="isSubmitting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        <span x-show="!isSubmitting">⭐ Kirim Penilaian</span>
                                        <span x-show="isSubmitting">Mengirim...</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Last Updated Info -->
                    <div class="mt-6 text-center" x-show="result.status_updated_at">
                        <p class="text-[12px] text-slate-400 font-medium flex items-center justify-center gap-1.5 mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Terakhir diupdate: <span x-text="result.status_updated_at" class="font-semibold text-slate-500"></span>
                        </p>

                        <!-- Complaint Button -->
                        <button @click="complaintModalOpen = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 hover:bg-red-500 text-red-500 hover:text-white border border-red-200 hover:border-red-500 rounded-xl font-bold text-[13px] transition-all transform hover:-translate-y-0.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Ajukan Keluhan/Komplain
                        </button>

                        <!-- Status Keluhan Section -->
                        <template x-if="result.complaints && result.complaints.length > 0">
                            <div class="mt-6 w-full text-left">
                                <h4 class="text-[14px] font-bold text-slate-700 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Riwayat Keluhan Anda
                                </h4>
                                <div class="space-y-3">
                                    <template x-for="(c, i) in result.complaints" :key="i">
                                        <div class="bg-white rounded-2xl p-4 border shadow-sm text-left"
                                             :class="{
                                                'border-amber-200 bg-amber-50/30': c.status === 'Menunggu',
                                                'border-blue-200 bg-blue-50/30': c.status === 'Direview',
                                                'border-emerald-200 bg-emerald-50/30': c.status === 'Disetujui',
                                                'border-red-200 bg-red-50/30': c.status === 'Ditolak',
                                                'border-purple-200 bg-purple-50/30': c.status === 'Diganti Uang',
                                             }">
                                            <!-- Header baris keluhan -->
                                            <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                                                <span class="text-[11px] text-slate-400 font-medium" x-text="'Dikirim: ' + c.created_at"></span>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold"
                                                      :class="{
                                                        'bg-amber-100 text-amber-700': c.status === 'Menunggu',
                                                        'bg-blue-100 text-blue-700': c.status === 'Direview',
                                                        'bg-emerald-100 text-emerald-700': c.status === 'Disetujui',
                                                        'bg-red-100 text-red-700': c.status === 'Ditolak',
                                                        'bg-purple-100 text-purple-700': c.status === 'Diganti Uang',
                                                      }">
                                                    <!-- Icon status -->
                                                    <template x-if="c.status === 'Menunggu'"><span>⏳</span></template>
                                                    <template x-if="c.status === 'Direview'"><span>🔍</span></template>
                                                    <template x-if="c.status === 'Disetujui'"><span>✅</span></template>
                                                    <template x-if="c.status === 'Ditolak'"><span>❌</span></template>
                                                    <template x-if="c.status === 'Diganti Uang'"><span>💰</span></template>
                                                    <span x-text="c.status"></span>
                                                </span>
                                            </div>
                                            <!-- Isi keluhan -->
                                            <p class="text-[13px] text-slate-700 font-medium leading-relaxed mb-2" x-text="c.description"></p>
                                            <!-- Catatan admin / solusi -->
                                            <template x-if="c.resolution_notes">
                                                <div class="mt-2 p-3 rounded-xl border"
                                                     :class="{
                                                        'bg-emerald-50 border-emerald-200': c.status === 'Disetujui' || c.status === 'Diganti Uang',
                                                        'bg-slate-50 border-slate-200': c.status !== 'Disetujui' && c.status !== 'Diganti Uang'
                                                     }">
                                                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1"
                                                       :class="c.status === 'Disetujui' || c.status === 'Diganti Uang' ? 'text-emerald-600' : 'text-slate-400'">💬 Respons Admin:</p>
                                                    <p class="text-[13px] font-medium text-slate-700" x-text="c.resolution_notes"></p>
                                                </div>
                                            </template>
                                            <!-- Jika belum ada respons -->
                                            <template x-if="!c.resolution_notes && c.status === 'Menunggu'">
                                                <p class="text-[11px] text-slate-400 italic mt-1">⏳ Tim kami sedang memproses keluhan Anda...</p>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                </div>
            </div>
        </template>

        <!-- Complaint Modal -->
        <div x-show="complaintModalOpen" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click="complaintModalOpen = false" @keydown.escape.window="complaintModalOpen = false">
            <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-lg overflow-hidden flex flex-col transform transition-all" @click.stop>
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-red-50">
                    <h3 class="text-lg font-bold text-red-600 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Formulir Komplain
                    </h3>
                    <button @click="complaintModalOpen = false" class="w-8 h-8 rounded-full bg-white text-slate-400 hover:text-slate-600 flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="p-6">
                    <p class="text-[13px] text-slate-500 mb-4 font-medium">Mohon beritahu kami apa yang salah dengan pesanan Anda. Tim kami akan meninjau keluhan ini dan memberikan solusi kompensasi.</p>
                    
                    <div class="mb-4">
                        <label class="block text-[12px] font-bold text-slate-700 mb-2 uppercase tracking-wide">Nomor Order</label>
                        <input type="text" x-model="result.order_code" readonly class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-600 outline-none">
                    </div>

                    <div class="mb-4">
                        <label class="block text-[12px] font-bold text-slate-700 mb-2 uppercase tracking-wide">Deskripsi Keluhan</label>
                        <textarea x-model="complaintForm.description" rows="4" placeholder="Cth: Ada baju yang luntur, pakaian kurang bersih, setrikaan kurang rapi, dll..." class="w-full px-4 py-3 bg-white border border-slate-300 focus:border-red-400 focus:ring-4 focus:ring-red-500/10 rounded-xl font-medium text-[14px] outline-none transition-all resize-none"></textarea>
                    </div>

                    <div x-show="complaintForm.successMsg" class="mb-4 p-3 bg-green-50 text-green-600 rounded-xl text-[13px] font-bold flex items-center gap-2 border border-green-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span x-text="complaintForm.successMsg"></span>
                    </div>

                    <div x-show="complaintForm.errorMsg" class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-[13px] font-bold flex items-center gap-2 border border-red-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="complaintForm.errorMsg"></span>
                    </div>

                    <button @click="submitComplaint" :disabled="complaintForm.isSubmitting || !complaintForm.description" class="w-full py-3.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-[0_4px_15px_rgba(239,68,68,0.4)] flex items-center justify-center gap-2">
                        <svg x-show="complaintForm.isSubmitting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="complaintForm.isSubmitting ? 'Mengirim...' : 'Kirim Keluhan'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Photo Lightbox -->
        <div x-show="lightboxOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click="lightboxOpen = false" @keydown.escape.window="lightboxOpen = false">
            <img :src="lightboxUrl" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl" @click.stop>
            <button @click="lightboxOpen = false" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/40 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Footer Info -->
        <div class="mt-auto pt-8 text-center text-[12px] text-slate-400 font-medium z-10" x-show="!result">
            &copy; {{ date('Y') }} {{ $globalSettings['store_name'] ?? 'LaundryPro' }}. All rights reserved.<br>
            Powered by Enterprise Tracking System.
        </div>

    </div>

    <script>
        // Countdown Timer Component
        function countdown(targetDate) {
            return {
                days: '00',
                hours: '00',
                minutes: '00',
                seconds: '00',
                isExpired: false,
                interval: null,

                init() {
                    this.updateCountdown();
                    this.interval = setInterval(() => this.updateCountdown(), 1000);
                },

                updateCountdown() {
                    const target = new Date(targetDate).getTime();
                    const now = new Date().getTime();
                    const diff = target - now;

                    if (diff <= 0) {
                        this.days = '00';
                        this.hours = '00';
                        this.minutes = '00';
                        this.seconds = '00';
                        this.isExpired = true;
                        if (this.interval) clearInterval(this.interval);
                        return;
                    }

                    this.isExpired = false;
                    this.days = String(Math.floor(diff / (1000 * 60 * 60 * 24))).padStart(2, '0');
                    this.hours = String(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                    this.minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                    this.seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');
                },

                destroy() {
                    if (this.interval) clearInterval(this.interval);
                }
            }
        }

        function trackingApp() {
            return {
                searchQuery: '',
                isLoading: false,
                isPaying: false,
                errorMsg: '',
                result: null,
                lightboxOpen: false,
                lightboxUrl: '',
                complaintModalOpen: false,
                complaintForm: {
                    description: '',
                    isSubmitting: false,
                    successMsg: '',
                    errorMsg: ''
                },
                
                statuses: [
                    'Menunggu Konfirmasi', 'Diterima', 'Dicuci', 'Dikeringkan', 'Disetrika',
                    'Quality Control', 'Selesai', 'Diambil'
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

                async submitComplaint() {
                    if (!this.complaintForm.description || !this.result?.order_code) return;
                    
                    this.complaintForm.isSubmitting = true;
                    this.complaintForm.successMsg = '';
                    this.complaintForm.errorMsg = '';

                    try {
                        const response = await fetch('/api/complaint', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                order_code: this.result.order_code,
                                description: this.complaintForm.description
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Terjadi kesalahan saat mengirim keluhan.');
                        }

                        this.complaintForm.successMsg = data.message;
                        this.complaintForm.description = '';
                        setTimeout(() => {
                            this.complaintModalOpen = false;
                            this.complaintForm.successMsg = '';
                        }, 3000);

                    } catch (error) {
                        this.complaintForm.errorMsg = error.message;
                    } finally {
                        this.complaintForm.isSubmitting = false;
                    }
                },
                
                async payWithMidtrans() {
                    if (!this.result?.order_code || this.isPaying) return;
                    this.isPaying = true;

                    try {
                        const response = await fetch(`/api/order/${this.result.order_code}/pay`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Gagal menyiapkan pembayaran.');
                        }

                        // Trigger Midtrans Snap Popup
                        snap.pay(data.snap_token, {
                            onSuccess: (result) => {
                                // Auto reload tracking data
                                this.searchOrder();
                            },
                            onPending: (result) => {
                                this.searchOrder();
                            },
                            onError: (result) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Transaksi Gagal',
                                    text: 'Pembayaran atau koneksi gagal, silakan coba lagi.',
                                    confirmButtonColor: '#3b82f6'
                                });
                            },
                            onClose: () => {
                                this.isPaying = false;
                            }
                        });
                        
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: error.message,
                            confirmButtonColor: '#3b82f6'
                        });
                        this.isPaying = false;
                    }
                },

                getCurrentStatusIndex() {
                    if (!this.result) return -1;
                    return this.statuses.indexOf(this.result.status);
                },
                
                formatTimestamp(isoString) {
                    if (!isoString) return '';
                    const d = new Date(isoString);
                    if (isNaN(d)) return isoString;
                    
                    const options = { 
                        day: '2-digit', month: 'short', year: 'numeric', 
                        hour: '2-digit', minute: '2-digit',
                        hour12: false 
                    };
                    return d.toLocaleDateString('id-ID', options);
                },
                
                formatDateStr(datetime) {
                    if (!datetime) return '';
                    const d = new Date(datetime);
                    if (isNaN(d)) return datetime;
                    const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                    return d.toLocaleDateString('id-ID', options).replace(/\./g, ':');
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
