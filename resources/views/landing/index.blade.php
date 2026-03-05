<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['store_name'] ?? 'LaundryPro' }} - Layanan Laundry Profesional</title>
    <meta name="description" content="Layanan laundry profesional dengan tracking real-time. Cuci, setrika, dan dry cleaning dengan kualitas terbaik.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { font-family: 'Poppins', sans-serif; }
        
        html { scroll-behavior: smooth; }
        
        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #1a56db 100%);
        }
        .glass { 
            background: rgba(255,255,255,0.08); 
            backdrop-filter: blur(16px); 
            border: 1px solid rgba(255,255,255,0.12); 
        }
        .glass-white { 
            background: rgba(255,255,255,0.85); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255,255,255,0.6); 
        }
        .card-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 25px 60px rgba(0,0,0,0.12); }
        
        .float-animation { animation: floatAnim 6s ease-in-out infinite; }
        .float-animation-delayed { animation: floatAnim 6s ease-in-out 2s infinite; }
        @keyframes floatAnim {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #60a5fa, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .blob-1 { animation: blob 7s infinite; }
        .blob-2 { animation: blob 7s infinite 2s; }
        .blob-3 { animation: blob 7s infinite 4s; }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        
        .service-icon {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .section-divider {
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            height: 1px;
        }
    </style>
</head>
<body class="antialiased bg-white text-slate-800 overflow-x-hidden" x-data="landingApp()">
    
    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" 
         :class="scrolled ? 'bg-white/90 backdrop-blur-xl shadow-lg shadow-slate-200/50' : 'bg-transparent'"
         @scroll.window="scrolled = window.scrollY > 50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="#hero" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="ph-fill ph-t-shirt text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-extrabold" :class="scrolled ? 'text-slate-800' : 'text-white'">{{ $settings['store_name'] ?? 'LaundryPro' }}</span>
                </a>
                
                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#layanan" class="text-[14px] font-semibold transition-colors" :class="scrolled ? 'text-slate-600 hover:text-blue-600' : 'text-white/80 hover:text-white'">Layanan</a>
                    <a href="#harga" class="text-[14px] font-semibold transition-colors" :class="scrolled ? 'text-slate-600 hover:text-blue-600' : 'text-white/80 hover:text-white'">Harga</a>
                    <a href="#order" class="text-[14px] font-semibold transition-colors" :class="scrolled ? 'text-slate-600 hover:text-blue-600' : 'text-white/80 hover:text-white'">Order Online</a>
                    <a href="#tracking" class="text-[14px] font-semibold transition-colors" :class="scrolled ? 'text-slate-600 hover:text-blue-600' : 'text-white/80 hover:text-white'">Tracking</a>
                    <a href="#kontak" class="text-[14px] font-semibold transition-colors" :class="scrolled ? 'text-slate-600 hover:text-blue-600' : 'text-white/80 hover:text-white'">Kontak</a>
                </div>
                
                <!-- CTA -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 rounded-xl text-[13px] font-bold bg-white/10 backdrop-blur border border-white/20 hover:bg-white hover:text-blue-600 transition-all" :class="scrolled ? 'text-slate-700 bg-slate-100 border-slate-200' : 'text-white'">
                        <i class="ph ph-sign-in text-lg"></i> Login Karyawan
                    </a>
                    <a href="#order" class="px-5 py-2.5 rounded-xl text-[13px] font-bold bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all">
                        Order Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="hero" class="hero-gradient relative min-h-screen flex items-center overflow-hidden">
        <!-- Decorative blobs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl blob-1"></div>
            <div class="absolute top-1/2 -left-40 w-[500px] h-[500px] rounded-full bg-indigo-500/15 blur-3xl blob-2"></div>
            <div class="absolute -bottom-20 right-1/4 w-80 h-80 rounded-full bg-purple-500/10 blur-3xl blob-3"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 md:py-40">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Left: Copy -->
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass text-[13px] font-semibold text-blue-300 mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Buka Setiap Hari • 07:00 - 21:00
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-white leading-tight tracking-tight mb-6">
                        Layanan Laundry <span class="gradient-text">Profesional</span> & Terpercaya
                    </h1>
                    <p class="text-lg text-blue-200/80 font-medium mb-8 max-w-lg leading-relaxed">
                        Percayakan pakaian Anda kepada kami. Cuci bersih, wangi, dan rapi dengan teknologi modern dan pengeringan cepat.
                    </p>
                    <div class="flex flex-wrap gap-4 mb-10">
                        <a href="#order" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold text-[15px] shadow-2xl shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 transition-all flex items-center gap-2">
                            <i class="ph ph-shopping-cart-simple text-xl"></i>
                            Order Online
                        </a>
                        <a href="#tracking" class="px-8 py-4 rounded-2xl glass text-white font-bold text-[15px] hover:bg-white/20 transition-all flex items-center gap-2">
                            <i class="ph ph-magnifying-glass text-xl"></i>
                            Lacak Pesanan
                        </a>
                    </div>
                    
                    <!-- Social Proof -->
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-2xl font-black text-white">{{ number_format($totalCustomers) }}+</p>
                            <p class="text-[12px] text-blue-300 font-medium">Pelanggan Puas</p>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black text-white">{{ number_format($totalOrders) }}+</p>
                            <p class="text-[12px] text-blue-300 font-medium">Order Selesai</p>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div>
                            <p class="text-2xl font-black text-white">⭐ 4.9</p>
                            <p class="text-[12px] text-blue-300 font-medium">Rating</p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Visual -->
                <div class="hidden md:flex justify-center relative">
                    <div class="relative">
                        <!-- Feature cards floating -->
                        <div class="glass rounded-3xl p-6 w-72 float-animation">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg">
                                    <i class="ph-fill ph-check-circle text-white text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-[15px]">Quality Control</p>
                                    <p class="text-blue-200/60 text-[12px] font-medium">Cek kualitas setiap cucian</p>
                                </div>
                            </div>
                            <div class="h-2 rounded-full bg-white/10 overflow-hidden">
                                <div class="h-full w-[92%] rounded-full bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                            </div>
                            <p class="text-right text-[11px] font-bold text-emerald-300 mt-1">92% Tingkat Kepuasan</p>
                        </div>
                        
                        <div class="glass rounded-3xl p-5 w-64 float-animation-delayed absolute -bottom-10 -right-10">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                                    <i class="ph-fill ph-clock text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-[14px]">Proses Cepat</p>
                                    <p class="text-blue-200/60 text-[12px] font-medium">Selesai dalam 1-2 hari</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="glass rounded-2xl px-5 py-3 absolute top-4 -right-6 float-animation" style="animation-delay: 1s;">
                            <div class="flex items-center gap-2">
                                <i class="ph-fill ph-map-pin text-red-400 text-lg"></i>
                                <span class="text-white/80 text-[13px] font-semibold">{{ $settings['store_address'] ?? 'Lokasi Strategis' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave Bottom -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,48C672,43,768,53,864,64C960,75,1056,85,1152,80C1248,75,1344,53,1392,42.7L1440,32L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- WHY US SECTION -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-[13px] font-bold text-blue-600 uppercase tracking-widest mb-3">Mengapa Memilih Kami?</p>
                <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">Pelayanan Terbaik untuk <span class="text-blue-600">Pakaian Anda</span></h2>
            </div>
            <div class="grid md:grid-cols-4 gap-6">
                @php
                $features = [
                    ['icon' => 'ph-drop', 'color' => 'from-blue-500 to-cyan-500', 'shadow' => 'blue', 'title' => 'Cuci Bersih', 'desc' => 'Menggunakan deterjen premium dan air bersih untuk hasil maksimal'],
                    ['icon' => 'ph-timer', 'color' => 'from-amber-500 to-orange-500', 'shadow' => 'amber', 'title' => 'Proses Cepat', 'desc' => 'Estimasi 1-2 hari kerja dengan opsi express 6 jam'],
                    ['icon' => 'ph-shield-check', 'color' => 'from-emerald-500 to-teal-500', 'shadow' => 'emerald', 'title' => 'Aman & Terjamin', 'desc' => 'Garansi cuci ulang jika hasil tidak memuaskan'],
                    ['icon' => 'ph-map-pin', 'color' => 'from-purple-500 to-fuchsia-500', 'shadow' => 'purple', 'title' => 'Antar Jemput', 'desc' => 'Layanan pickup & delivery gratis dalam radius 5 km'],
                ];
                @endphp
                @foreach($features as $f)
                <div class="bg-white rounded-3xl p-6 card-hover border border-slate-100 shadow-sm text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $f['color'] }} flex items-center justify-center mx-auto mb-5 shadow-lg shadow-{{ $f['shadow'] }}-500/25 group-hover:scale-110 transition-transform">
                        <i class="ph-fill {{ $f['icon'] }} text-white text-2xl"></i>
                    </div>
                    <h3 class="text-[16px] font-bold text-slate-800 mb-2">{{ $f['title'] }}</h3>
                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SERVICES & PRICING -->
    <section id="layanan" class="py-20 bg-gradient-to-b from-slate-50 to-white">
        <div id="harga" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-[13px] font-bold text-blue-600 uppercase tracking-widest mb-3">Layanan & Harga</p>
                <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">Pilih Layanan Sesuai <span class="text-blue-600">Kebutuhan Anda</span></h2>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services as $service)
                @php
                    $name = strtolower($service->service_name);
                    // Map service name to illustration image
                    if (str_contains($name, 'selimut') || str_contains($name, 'bedcover') || str_contains($name, 'sprei')) {
                        $image = '/images/selimut.png';
                        $icon = 'ph-bed';
                    } elseif (str_contains($name, 'sepatu') || str_contains($name, 'shoes')) {
                        $image = '/images/sepatu.png';
                        $icon = 'ph-sneaker';
                    } elseif (str_contains($name, 'jas') || str_contains($name, 'blazer') || str_contains($name, 'suit') || str_contains($name, 'dry')) {
                        $image = '/images/jas.png';
                        $icon = 'ph-coat-hanger';
                    } elseif (str_contains($name, 'setrika') || str_contains($name, 'press')) {
                        $image = '/images/setrika.png';
                        $icon = 'ph-lightning';
                    } elseif (str_contains($name, 'satuan') || str_contains($name, 'pcs') || str_contains($name, 'piece')) {
                        $image = '/images/satuan.png';
                        $icon = 'ph-hanger';
                    } elseif (str_contains($name, 'express') || str_contains($name, 'kilat')) {
                        $image = '/images/satuan.png';
                        $icon = 'ph-rocket-launch';
                    } elseif (str_contains($name, 'lipat') || str_contains($name, 'cuci')) {
                        $image = '/images/satuan.png';
                        $icon = 'ph-t-shirt';
                    } else {
                        $image = null;
                        $icon = 'ph-t-shirt';
                    }
                @endphp
                <div class="bg-white rounded-3xl card-hover border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-[60px] -z-0 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <!-- Service Illustration -->
                    <div class="relative z-10 bg-gradient-to-br from-slate-50 to-blue-50/50 flex items-center justify-center pt-6 pb-2 px-6">
                        @if($image)
                        <img src="{{ $image }}" alt="{{ $service->service_name }}" class="w-28 h-28 object-contain group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="service-icon w-20 h-20 rounded-2xl flex items-center justify-center">
                            <i class="ph-fill {{ $icon }} text-white text-3xl"></i>
                        </div>
                        @endif
                    </div>

                    <!-- Service Info -->
                    <div class="relative z-10 p-6 pt-4">
                        <div class="mb-3">
                            <h3 class="text-[16px] font-bold text-slate-800">{{ $service->service_name }}</h3>
                            <p class="text-[12px] text-slate-400 font-medium">per {{ $service->unit ?? 'kg' }}</p>
                        </div>
                        @if($service->description)
                        <p class="text-[13px] text-slate-500 font-medium mb-4 leading-relaxed">{{ $service->description }}</p>
                        @endif
                        <div class="flex items-end justify-between">
                            <div>
                                <span class="text-[12px] text-slate-400 font-medium">Mulai dari</span>
                                <p class="text-2xl font-black text-blue-600">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                            </div>
                            <button @click="addToOrder({{ $service->id }}, '{{ addslashes($service->service_name) }}', {{ $service->price }}, '{{ $service->unit ?? 'kg' }}')" 
                                    class="px-4 py-2 rounded-xl bg-blue-50 text-blue-600 text-[13px] font-bold hover:bg-blue-600 hover:text-white transition-all flex items-center gap-1.5">
                                <i class="ph ph-plus-circle text-lg"></i> Pilih
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-slate-400">
                    <i class="ph ph-package text-5xl mb-4 block"></i>
                    <p class="font-semibold">Layanan belum tersedia.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ONLINE ORDER FORM -->
    <section id="order" class="py-20 bg-white relative overflow-hidden">
        <!-- Decorative -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-50 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-50 rounded-full translate-x-1/2 translate-y-1/2 blur-3xl"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12">
                <p class="text-[13px] font-bold text-blue-600 uppercase tracking-widest mb-3">Order Online</p>
                <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">Pesan Langsung dari <span class="text-blue-600">Rumah Anda</span></h2>
                <p class="text-slate-500 mt-3 text-[15px] font-medium max-w-md mx-auto">Isi formulir di bawah dan kami akan segera memproses pesanan Anda.</p>
            </div>
            
            <div class="bg-white rounded-[32px] p-8 md:p-10 shadow-2xl shadow-slate-200/50 border border-slate-100">
                <!-- Order Success Message -->
                <div x-show="orderSuccess" x-cloak x-transition class="text-center py-10">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-amber-500/30">
                        <i class="ph-fill ph-clock text-white text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">Order Terkirim! ⏳</h3>
                    <p class="text-slate-500 font-medium mb-1">Kode pesanan Anda:</p>
                    <div class="inline-block bg-blue-50 border-2 border-blue-200 rounded-2xl px-8 py-4 mb-4">
                        <p class="text-3xl font-black text-blue-600 tracking-wider" x-text="successOrderCode"></p>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-4 max-w-md mx-auto">
                        <div class="flex items-start gap-3 text-left">
                            <i class="ph-fill ph-info text-amber-500 text-xl mt-0.5 shrink-0"></i>
                            <div>
                                <p class="text-[13px] font-bold text-amber-700 mb-1">Menunggu Konfirmasi Admin</p>
                                <p class="text-[12px] text-amber-600 font-medium leading-relaxed">Pesanan Anda sedang direview oleh tim kami. Anda akan mendapat notifikasi via WhatsApp setelah dikonfirmasi.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info: Transfer -->
                    <div x-show="paymentMethod === 'Transfer'" class="max-w-md mx-auto mb-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-5 text-left">
                            <p class="text-[12px] font-bold text-blue-500 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                <i class="ph-fill ph-bank text-base"></i> Silakan Transfer ke:
                            </p>
                            <!-- Mandiri -->
                            <div class="bg-white rounded-xl p-4 mb-3 border border-blue-100 shadow-sm">
                                <div class="flex items-center gap-3 mb-2">
                                    <img src="/images/mandiri.png" alt="Bank Mandiri" class="w-10 h-10 rounded-lg object-contain bg-white p-1 border border-slate-100">
                                    <div>
                                        <p class="text-[14px] font-bold text-slate-700">Bank Mandiri</p>
                                        <p class="text-[11px] text-slate-400 font-medium">Transfer Bank</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-3">
                                    <span class="text-[17px] font-black text-slate-800 tracking-wide" id="mandiriNo">1380027856181</span>
                                    <button @click="copyToClipboard('1380027856181', 'mandiri')" class="text-[11px] font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                        <i class="ph ph-copy text-sm"></i>
                                        <span x-text="copiedAccount === 'mandiri' ? 'Tersalin!' : 'Salin'"></span>
                                    </button>
                                </div>
                            </div>
                            <!-- Dana -->
                            <div class="bg-white rounded-xl p-4 border border-blue-100 shadow-sm">
                                <div class="flex items-center gap-3 mb-2">
                                    <img src="/images/Dana.png" alt="DANA" class="w-10 h-10 rounded-lg object-contain">
                                    <div>
                                        <p class="text-[14px] font-bold text-slate-700">DANA</p>
                                        <p class="text-[11px] text-slate-400 font-medium">E-Wallet</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-3">
                                    <span class="text-[17px] font-black text-slate-800 tracking-wide">089649469769</span>
                                    <button @click="copyToClipboard('089649469769', 'dana')" class="text-[11px] font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                        <i class="ph ph-copy text-sm"></i>
                                        <span x-text="copiedAccount === 'dana' ? 'Tersalin!' : 'Salin'"></span>
                                    </button>
                                </div>
                            </div>
                            <p class="text-[11px] text-blue-500 font-medium mt-3 text-center">⚠️ Cantumkan kode order (<span x-text="successOrderCode" class="font-bold"></span>) saat transfer.</p>
                        </div>
                    </div>

                    <!-- Payment Info: COD -->
                    <div x-show="paymentMethod === 'Bayar di Tempat'" class="max-w-md mx-auto mb-4">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-left">
                            <div class="flex items-start gap-3">
                                <i class="ph-fill ph-storefront text-emerald-500 text-xl mt-0.5 shrink-0"></i>
                                <div>
                                    <p class="text-[13px] font-bold text-emerald-700 mb-1">Bayar di Tempat</p>
                                    <p class="text-[12px] text-emerald-600 font-medium leading-relaxed">Pembayaran dilakukan saat Anda mengantarkan cucian ke toko kami. Metode: Cash, QRIS, atau Debit.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-[13px] text-slate-400 font-medium mb-6">Simpan kode ini untuk melacak status pesanan.</p>
                    <div class="flex justify-center gap-4">
                        <a :href="'/track?q=' + successOrderCode" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold text-[14px] shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2">
                            <i class="ph ph-magnifying-glass text-lg"></i> Lacak Pesanan
                        </a>
                        <button @click="orderSuccess = false; resetOrder()" class="px-6 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold text-[14px] hover:bg-slate-200 transition-all">
                            Order Lagi
                        </button>
                    </div>
                </div>
                
                <!-- Order Form -->
                <div x-show="!orderSuccess">
                    <!-- Step indicators -->
                    <div class="flex items-center justify-center gap-2 mb-8">
                        <div class="flex items-center gap-2" :class="step === 1 ? 'text-blue-600' : 'text-slate-400'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-bold" :class="step === 1 ? 'bg-blue-600 text-white' : step > 1 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500'">
                                <span x-show="step <= 1">1</span><i x-show="step > 1" class="ph-fill ph-check"></i>
                            </div>
                            <span class="text-[13px] font-bold hidden sm:inline">Pilih Layanan</span>
                        </div>
                        <div class="w-12 h-0.5 bg-slate-200" :class="step > 1 && 'bg-blue-400'"></div>
                        <div class="flex items-center gap-2" :class="step === 2 ? 'text-blue-600' : 'text-slate-400'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-bold" :class="step === 2 ? 'bg-blue-600 text-white' : step > 2 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500'">
                                <span x-show="step <= 2">2</span><i x-show="step > 2" class="ph-fill ph-check"></i>
                            </div>
                            <span class="text-[13px] font-bold hidden sm:inline">Data Pelanggan</span>
                        </div>
                        <div class="w-12 h-0.5 bg-slate-200" :class="step > 2 && 'bg-blue-400'"></div>
                        <div class="flex items-center gap-2" :class="step === 3 ? 'text-blue-600' : 'text-slate-400'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-bold" :class="step === 3 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'">3</div>
                            <span class="text-[13px] font-bold hidden sm:inline">Konfirmasi</span>
                        </div>
                    </div>
                    
                    <!-- Step 1: Select Services -->
                    <div x-show="step === 1" x-transition>
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="ph-fill ph-shopping-bag text-blue-500 text-xl"></i> Layanan Dipilih
                        </h3>
                        
                        <div x-show="orderItems.length === 0" class="bg-slate-50 rounded-2xl p-8 text-center border-2 border-dashed border-slate-200">
                            <i class="ph ph-shopping-cart text-4xl text-slate-300 mb-3 block"></i>
                            <p class="text-slate-400 font-semibold">Belum ada layanan dipilih.</p>
                            <p class="text-slate-400 text-[13px]">Pilih layanan dari daftar di <a href="#layanan" class="text-blue-500 underline">atas</a>.</p>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="(item, idx) in orderItems" :key="idx">
                                <div class="bg-slate-50 rounded-2xl p-4 flex items-center justify-between border border-slate-100">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="service-icon w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                                            <i class="ph-fill ph-t-shirt text-white"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[14px] font-bold text-slate-700 truncate" x-text="item.name"></p>
                                            <p class="text-[12px] text-slate-400" x-text="'Rp ' + formatRupiah(item.price) + ' / ' + item.unit"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 ml-2">
                                        <div class="flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden">
                                            <button @click="decrementItem(idx)" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors text-lg font-bold">−</button>
                                            <input type="number" x-model.number="item.quantity" min="0.1" step="0.1" class="w-14 text-center text-[14px] font-bold text-slate-700 border-0 bg-transparent focus:ring-0 p-0">
                                            <button @click="item.quantity += (item.unit === 'kg' ? 0.5 : 1)" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-blue-500 hover:bg-blue-50 transition-colors text-lg font-bold">+</button>
                                        </div>
                                        <p class="text-[14px] font-black text-slate-700 w-24 text-right" x-text="'Rp ' + formatRupiah(item.price * item.quantity)"></p>
                                        <button @click="orderItems.splice(idx, 1)" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Total -->
                        <div x-show="orderItems.length > 0" class="mt-6 bg-blue-50 rounded-2xl p-5 border border-blue-100 flex items-center justify-between">
                            <span class="text-[15px] font-bold text-slate-700">Total Estimasi</span>
                            <span class="text-2xl font-black text-blue-600" x-text="'Rp ' + formatRupiah(getTotal())"></span>
                        </div>
                        
                        <button x-show="orderItems.length > 0" @click="step = 2" class="w-full mt-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-2xl font-bold text-[15px] shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            Lanjut ke Data Pelanggan <i class="ph ph-arrow-right text-lg"></i>
                        </button>
                    </div>
                    
                    <!-- Step 2: Customer Info -->
                    <div x-show="step === 2" x-transition>
                        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <i class="ph-fill ph-user-circle text-blue-500 text-xl"></i> Data Pelanggan
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[13px] font-bold text-slate-600 mb-2 block">Nama Lengkap *</label>
                                <input type="text" x-model="customerName" placeholder="Contoh: Budi Santoso" class="w-full px-5 py-3.5 rounded-xl border border-slate-200 text-[14px] font-medium focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                            <div>
                                <label class="text-[13px] font-bold text-slate-600 mb-2 block">Nomor WhatsApp *</label>
                                <input type="tel" x-model="customerPhone" placeholder="Contoh: 08123456789" class="w-full px-5 py-3.5 rounded-xl border border-slate-200 text-[14px] font-medium focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                            <div>
                                <label class="text-[13px] font-bold text-slate-600 mb-2 block">Alamat (untuk pickup/delivery)</label>
                                <textarea x-model="customerAddress" rows="2" placeholder="Alamat lengkap Anda..." class="w-full px-5 py-3.5 rounded-xl border border-slate-200 text-[14px] font-medium focus:ring-2 focus:ring-blue-100 transition-all resize-none"></textarea>
                            </div>
                            <div>
                                <label class="text-[13px] font-bold text-slate-600 mb-2 block">Catatan (opsional)</label>
                                <input type="text" x-model="orderNotes" placeholder="Misal: ada pakaian yang luntur, jangan dicampur, dll." class="w-full px-5 py-3.5 rounded-xl border border-slate-200 text-[14px] font-medium focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button @click="step = 1" class="px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold text-[14px] hover:bg-slate-200 transition-all">
                                <i class="ph ph-arrow-left"></i> Kembali
                            </button>
                            <button @click="goToConfirmation()" class="flex-1 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-2xl font-bold text-[15px] shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                Lanjut ke Konfirmasi <i class="ph ph-arrow-right text-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Confirmation -->
                    <div x-show="step === 3" x-transition>
                        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <i class="ph-fill ph-receipt text-blue-500 text-xl"></i> Konfirmasi Pesanan
                        </h3>
                        
                        <!-- Customer Summary -->
                        <div class="bg-slate-50 rounded-2xl p-5 mb-4 border border-slate-100">
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-2">Data Pelanggan</p>
                            <p class="text-[15px] font-bold text-slate-700" x-text="customerName"></p>
                            <p class="text-[13px] text-slate-500 font-medium" x-text="customerPhone"></p>
                            <p x-show="customerAddress" class="text-[13px] text-slate-500 font-medium" x-text="customerAddress"></p>
                        </div>
                        
                        <!-- Items Summary -->
                        <div class="bg-slate-50 rounded-2xl p-5 mb-4 border border-slate-100">
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-3">Layanan Dipesan</p>
                            <template x-for="(item, idx) in orderItems" :key="idx">
                                <div class="flex justify-between py-2 border-b border-slate-200 last:border-0">
                                    <span class="text-[14px] font-medium text-slate-700" x-text="item.name + ' × ' + item.quantity + ' ' + item.unit"></span>
                                    <span class="text-[14px] font-bold text-slate-700" x-text="'Rp ' + formatRupiah(item.price * item.quantity)"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Total -->
                        <div class="bg-blue-50 rounded-2xl p-5 mb-5 border border-blue-100 flex justify-between">
                            <span class="text-[16px] font-bold text-slate-700">Total</span>
                            <span class="text-2xl font-black text-blue-600" x-text="'Rp ' + formatRupiah(getTotal())"></span>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-5">
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider mb-3">Metode Pembayaran</p>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="paymentMethod = 'Bayar di Tempat'" class="p-4 rounded-2xl border-2 transition-all text-left" :class="paymentMethod === 'Bayar di Tempat' ? 'border-blue-500 bg-blue-50 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="paymentMethod === 'Bayar di Tempat' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-400'">
                                            <i class="ph-fill ph-storefront text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-bold" :class="paymentMethod === 'Bayar di Tempat' ? 'text-blue-700' : 'text-slate-600'">Bayar di Tempat</p>
                                            <p class="text-[11px] font-medium" :class="paymentMethod === 'Bayar di Tempat' ? 'text-blue-500' : 'text-slate-400'">Cash / QRIS / Debit</p>
                                        </div>
                                    </div>
                                </button>
                                <button type="button" @click="paymentMethod = 'Transfer'" class="p-4 rounded-2xl border-2 transition-all text-left" :class="paymentMethod === 'Transfer' ? 'border-blue-500 bg-blue-50 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="paymentMethod === 'Transfer' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-400'">
                                            <i class="ph-fill ph-bank text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-bold" :class="paymentMethod === 'Transfer' ? 'text-blue-700' : 'text-slate-600'">Transfer</p>
                                            <p class="text-[11px] font-medium" :class="paymentMethod === 'Transfer' ? 'text-blue-500' : 'text-slate-400'">Bank / E-Wallet</p>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <!-- Transfer Info Preview -->
                            <div x-show="paymentMethod === 'Transfer'" x-transition class="mt-3 bg-blue-50 rounded-xl p-3 border border-blue-100">
                                <p class="text-[11px] text-blue-600 font-medium"><i class="ph ph-info"></i> Info rekening akan ditampilkan setelah order dikirim. Cantumkan kode order saat transfer.</p>
                            </div>
                        </div>
                        
                        <p x-show="orderNotes" class="text-[13px] text-slate-500 mb-4"><strong>Catatan:</strong> <span x-text="orderNotes"></span></p>
                        
                        <div class="flex gap-3">
                            <button @click="step = 2" class="px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold text-[14px] hover:bg-slate-200 transition-all">
                                <i class="ph ph-arrow-left"></i> Kembali
                            </button>
                            <button @click="submitOrder()" :disabled="isSubmitting" class="flex-1 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-bold text-[15px] shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                                <svg x-show="isSubmitting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <i x-show="!isSubmitting" class="ph-fill ph-paper-plane-tilt text-xl"></i>
                                <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Pesanan'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRACKING SECTION -->
    <section id="tracking" class="py-20 bg-gradient-to-b from-slate-50 to-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-[13px] font-bold text-blue-600 uppercase tracking-widest mb-3">Lacak Pesanan</p>
                <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">Cek Status Cucian <span class="text-blue-600">Real-time</span></h2>
                <p class="text-slate-500 mt-3 text-[15px] font-medium">Masukkan nomor HP atau kode order untuk melihat status.</p>
            </div>
            
            <div class="bg-white rounded-3xl p-4 shadow-2xl shadow-slate-200/50 border border-slate-100 flex">
                <input type="text" x-model="trackingQuery" @keydown.enter="goToTracking()" placeholder="Nomor HP atau Kode Order (mis: ORD-xxxxx)" class="flex-1 px-6 py-4 text-[15px] font-medium text-slate-700 border-0 focus:ring-0 bg-transparent placeholder-slate-400">
                <button @click="goToTracking()" class="px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2 text-[14px]">
                    <i class="ph ph-magnifying-glass text-lg"></i> Lacak
                </button>
            </div>
            
            <!-- Process Steps -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6">
                @php
                    $steps = [
                        ['icon' => 'ph-basket', 'title' => 'Diterima', 'desc' => 'Pakaian masuk & dicek'],
                        ['icon' => 'ph-drop', 'title' => 'Dicuci', 'desc' => 'Proses pencucian'],
                        ['icon' => 'ph-sun', 'title' => 'Dikeringkan', 'desc' => 'Proses pengeringan'],
                        ['icon' => 'ph-check-circle', 'title' => 'Selesai', 'desc' => 'Siap diambil'],
                    ];
                @endphp
                @foreach($steps as $i => $s)
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center mx-auto mb-3 border border-blue-100">
                        <i class="ph-fill {{ $s['icon'] }} text-blue-500 text-2xl"></i>
                    </div>
                    <p class="text-[14px] font-bold text-slate-700">{{ $s['title'] }}</p>
                    <p class="text-[12px] text-slate-400 font-medium">{{ $s['desc'] }}</p>
                    @if($i < 3)
                    <div class="hidden md:block absolute"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CONTACT / FOOTER -->
    <section id="kontak" class="py-20 hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full bg-blue-500/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-purple-500/10 blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid md:grid-cols-3 gap-12">
                <!-- Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                            <i class="ph-fill ph-t-shirt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white">{{ $settings['store_name'] ?? 'LaundryPro' }}</h3>
                    </div>
                    <p class="text-blue-200/70 font-medium max-w-md mb-8 leading-relaxed">Layanan laundry profesional dengan teknologi modern. Kami berkomitmen memberikan hasil terbaik untuk setiap pelanggan.</p>
                    
                    <div class="grid sm:grid-cols-2 gap-4">
                        @if(!empty($settings['store_address']))
                        <div class="glass rounded-2xl p-5 flex items-start gap-4">
                            <i class="ph-fill ph-map-pin text-blue-300 text-2xl shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-[12px] font-bold text-blue-300 uppercase tracking-wider mb-1">Alamat</p>
                                <p class="text-white/80 text-[14px] font-medium">{{ $settings['store_address'] }}</p>
                            </div>
                        </div>
                        @endif
                        @if(!empty($settings['store_phone']))
                        <div class="glass rounded-2xl p-5 flex items-start gap-4">
                            <i class="ph-fill ph-phone text-blue-300 text-2xl shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-[12px] font-bold text-blue-300 uppercase tracking-wider mb-1">Telepon</p>
                                <p class="text-white/80 text-[14px] font-medium">{{ $settings['store_phone'] }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-[14px] font-bold text-blue-300 uppercase tracking-wider mb-6">Navigasi Cepat</h4>
                    <div class="space-y-3">
                        <a href="#hero" class="block text-white/70 hover:text-white text-[14px] font-medium transition-colors">Beranda</a>
                        <a href="#layanan" class="block text-white/70 hover:text-white text-[14px] font-medium transition-colors">Layanan & Harga</a>
                        <a href="#order" class="block text-white/70 hover:text-white text-[14px] font-medium transition-colors">Order Online</a>
                        <a href="#tracking" class="block text-white/70 hover:text-white text-[14px] font-medium transition-colors">Lacak Pesanan</a>
                        <a href="{{ route('login') }}" class="block text-white/70 hover:text-white text-[14px] font-medium transition-colors">Login Karyawan</a>
                    </div>
                </div>
            </div>
            
            <div class="section-divider mt-12 mb-6 opacity-20"></div>
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-blue-200/50 text-[13px] font-medium">&copy; {{ date('Y') }} {{ $settings['store_name'] ?? 'LaundryPro' }}. All rights reserved.</p>
                <p class="text-blue-200/50 text-[12px] font-medium">Powered by LaundryPro Management System</p>
            </div>
        </div>
    </section>

    <script>
    function landingApp() {
        return {
            scrolled: false,
            step: 1,
            orderItems: [],
            customerName: '',
            customerPhone: '',
            customerAddress: '',
            orderNotes: '',
            paymentMethod: 'Bayar di Tempat',
            copiedAccount: '',
            isSubmitting: false,
            orderSuccess: false,
            successOrderCode: '',
            trackingQuery: '',
            
            addToOrder(serviceId, name, price, unit) {
                // Check if already in cart
                const existing = this.orderItems.find(i => i.service_id === serviceId);
                if (existing) {
                    existing.quantity += (unit === 'kg' ? 1 : 1);
                    // Scroll to order section
                    document.getElementById('order').scrollIntoView({ behavior: 'smooth' });
                    return;
                }
                
                this.orderItems.push({
                    service_id: serviceId,
                    name: name,
                    price: price,
                    unit: unit,
                    quantity: unit === 'kg' ? 1 : 1,
                });
                
                // Scroll to order section
                document.getElementById('order').scrollIntoView({ behavior: 'smooth' });
            },
            
            decrementItem(idx) {
                const item = this.orderItems[idx];
                const step = item.unit === 'kg' ? 0.5 : 1;
                if (item.quantity - step <= 0) {
                    this.orderItems.splice(idx, 1);
                } else {
                    item.quantity -= step;
                }
            },
            
            getTotal() {
                return this.orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },
            
            goToConfirmation() {
                if (!this.customerName.trim()) {
                    alert('Silakan isi nama lengkap Anda.');
                    return;
                }
                if (!this.customerPhone.trim()) {
                    alert('Silakan isi nomor WhatsApp Anda.');
                    return;
                }
                this.step = 3;
            },
            
            async submitOrder() {
                this.isSubmitting = true;
                
                try {
                    const payload = {
                        customer_name: this.customerName,
                        customer_phone: this.customerPhone,
                        customer_address: this.customerAddress,
                        notes: this.orderNotes,
                        payment_method: this.paymentMethod,
                        items: this.orderItems.map(i => ({
                            service_id: i.service_id,
                            quantity: i.quantity,
                        })),
                    };
                    
                    const res = await fetch('/order-online', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });
                    
                    const data = await res.json();
                    
                    if (data.success) {
                        this.successOrderCode = data.order_code;
                        this.orderSuccess = true;
                    } else if (res.status === 429) {
                        alert('⚠️ Anda sudah mencapai batas maksimal order hari ini (3 order/hari). Silakan coba lagi besok atau hubungi kami langsung.');
                    } else if (res.status === 422 && data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        alert('⚠️ ' + firstError);
                    } else {
                        alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
                } finally {
                    this.isSubmitting = false;
                }
            },
            
            copyToClipboard(text, label) {
                navigator.clipboard.writeText(text).then(() => {
                    this.copiedAccount = label;
                    setTimeout(() => this.copiedAccount = '', 2000);
                });
            },

            resetOrder() {
                this.step = 1;
                this.orderItems = [];
                this.customerName = '';
                this.customerPhone = '';
                this.customerAddress = '';
                this.orderNotes = '';
                this.paymentMethod = 'Bayar di Tempat';
                this.copiedAccount = '';
            },
            
            goToTracking() {
                if (this.trackingQuery.trim()) {
                    window.location.href = '/track?q=' + encodeURIComponent(this.trackingQuery.trim());
                }
            },
            
            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }
        }
    }
    </script>
</body>
</html>
