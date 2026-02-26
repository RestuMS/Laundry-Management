<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran - {{ $order->order_code }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: { primary: '#5B8DEF' }
                }
            }
        }
    </script>

    <style>
        body { background-color: #f8fafc; color: #334155; }
        
        /* Thermal Receipt Dimensions & Styling */
        .thermal-receipt {
            max-width: 320px; /* ~80mm thermal paper */
            margin: 2rem auto;
            background: #ffffff;
            padding: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        
        .divider {
            border-top: 1.5px dashed #cbd5e1;
            margin: 1rem 0;
            width: 100%;
        }

        /* Print Override */
        @media print {
            body { background: transparent; padding: 0; margin: 0; }
            .thermal-receipt {
                max-width: 80mm;
                margin: 0;
                padding: 5mm;
                box-shadow: none;
                border: none;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="antialiased">

    <!-- Screen Controls (Hidden on Print) -->
    <div class="no-print max-w-sm mx-auto mt-8 mb-4 flex flex-col gap-3 px-4">
        <div class="flex gap-3">
            <button onclick="window.print()" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 transition-all text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Struk
            </button>
            <a href="{{ route('order.index') }}" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-all text-center text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
        
        @php
            $waPhone = $order->customer_phone ? preg_replace('/^08/', '+628', $order->customer_phone) : null;
            $storeName = $globalSettings['store_name'] ?? 'LaundryPro';
            $totalHarga = number_format($order->total_price - ($order->discount ?? 0) + ($order->tax ?? 0), 0, ',', '.');
            $trackingLink = url('/track?q=' . $order->order_code);
            $whatsappMsg = "Halo Kak *{$order->customer_name}*,%0A%0ATerima kasih telah mencuci di *{$storeName}*.%0ABerikut adalah nota digital pesanan Anda:%0A%0A🧾 *NO TRX:* {$order->order_code}%0A👕 *LAYANAN:* {$order->service_name}%0A💰 *TOTAL:* Rp {$totalHarga}%0A💳 *STATUS BAYAR:* {$order->payment_status}%0A📦 *STATUS BARANG:* {$order->status}%0A%0AKakak bisa memantau cucian Kakak secara realtime melalui link resmi berikut:%0A{$trackingLink}%0A%0ATerima kasih! 🙏";
            $waLink = $waPhone ? "https://wa.me/{$waPhone}?text={$whatsappMsg}" : "javascript:alert('Gagal! Nomor HP pelanggan tidak tersedia di data transaksi ini!');";
        @endphp
        
        <a href="{{ $waLink }}" target="_blank" class="w-full bg-[#25D366] hover:bg-[#128C7E] text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2.5 transition-all text-center shadow-lg shadow-green-500/30 mt-1">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-3.825 3.113-6.937 6.937-6.937 3.825 0 6.938 3.112 6.938 6.937 0 3.825-3.113 6.938-6.939 6.938z"/></svg>
            Kirim Nota ke WhatsApp Pelanggan
        </a>
    </div>

    <!-- Thermal Paper Container -->
    <div class="thermal-receipt font-mono text-[13px] leading-tight">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="flex justify-center mb-2">
                <img src="{{ asset('images/icon.png') }}" class="h-10 grayscale opacity-80" alt="Logo">
            </div>
            <h1 class="font-bold text-lg tracking-wide uppercase mt-1">{{ $globalSettings['store_name'] ?? 'Laundry Pro' }}</h1>
            <p class="text-[11px] text-slate-500 mt-1">{!! nl2br(e($globalSettings['store_address'] ?? 'Jl. Sudirman No. 45, Jakarta')) !!}<br>Telp: {{ $globalSettings['store_phone'] ?? '0812-3456-7890' }}</p>
        </div>

        <div class="divider"></div>

        <!-- Trx Detail -->
        <div class="space-y-1.5 mb-2">
            <div class="flex justify-between">
                <span class="text-slate-500">No. Trx</span>
                <span class="font-bold uppercase">{{ $order->order_code }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Tgl Masuk</span>
                <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
            </div>
            @if($order->estimated_finish)
            <div class="flex justify-between">
                <span class="text-slate-500">Est. Selesai</span>
                <span class="font-bold">{{ \Carbon\Carbon::parse($order->estimated_finish)->format('d/m/y H:i') }}</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span class="text-slate-500">Kasir</span>
                <span>Admin Kasir</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Customer Detail -->
        <div class="space-y-1.5 mb-2">
            <div class="flex justify-between">
                <span class="text-slate-500">Pelanggan</span>
                <span class="font-bold truncate max-w-[150px] text-right">{{ $order->customer_name }}</span>
            </div>
            @if($order->customer_phone)
            <div class="flex justify-between">
                <span class="text-slate-500">HP / WA</span>
                <span>{{ $order->customer_phone }}</span>
            </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Purchase Items -->
        <div class="mb-2 space-y-2">
            <div>
                <div class="font-bold mb-1">{{ $order->service_name }}</div>
                <div class="flex justify-between text-slate-500">
                    <span>
                        {{ $order->weight ?? 1 }} {{ str_contains(strtolower($order->service_name), 'sepatu') ? 'Psg' : (str_contains(strtolower($order->service_name), 'satuan') ? 'Pcs' : 'Kg') }}
                        x {{ number_format($order->total_price / max(1, $order->weight ?? 1), 0, ',', '.') }}
                    </span>
                    <span class="text-slate-800 font-semibold">{{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Totals -->
        <div class="space-y-1.5">
            @if($order->discount > 0)
            <div class="flex justify-between">
                <span class="text-slate-500">Diskon</span>
                <span>- {{ number_format($order->discount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order->tax > 0)
            <div class="flex justify-between">
                <span class="text-slate-500">Pajak</span>
                <span>+ {{ number_format($order->tax, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-[15px] pt-1">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total_price - ($order->discount ?? 0) + ($order->tax ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-1">
                <span>Status</span>
                <span class="font-bold {{ $order->payment_status == 'Lunas' ? 'text-black' : 'text-black border border-black border-dashed px-1' }}">{{ strtoupper($order->payment_status) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Footer / Notes -->
        <div class="text-center mt-4">
            <div class="flex justify-center mb-2">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(url('/track?q=' . $order->order_code)) }}" alt="QR Code Tracking" class="w-16 h-16 opacity-80" />
            </div>
            <p class="font-bold mb-1">Cek Status Laundry (Scan QR)</p>
            <p class="text-[10px] text-slate-500 px-2 leading-relaxed">
                {{ $globalSettings['receipt_footer'] ?? 'Struk ini adalah bukti sah. Barang yang tidak diambil dalam 30 hari di luar tanggung jawab kami.' }}
            </p>
            <p class="text-[10px] font-bold mt-2 font-sans tracking-widest text-slate-400">--- POTONG DI SINI ---</p>
        </div>

    </div>

</body>
</html>
