<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->order_code }}</title>
    <style>
        /* Thermal Receipt Strict Formatting */
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap');

        body {
            font-family: 'JetBrains Mono', monospace;
            background-color: #e2e8f0;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .receipt {
            background-color: #fff;
            width: 80mm; /* Standard 80mm thermal width */
            padding: 5mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            color: #000;
            font-size: 13px; /* Optimized for legibility on thermal */
            line-height: 1.3;
        }

        /* Elements */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .flex { display: flex; justify-content: space-between; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .divider-solid {
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        /* Screen Controls */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-family: Arial, sans-serif;
            z-index: 50;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.1s;
        }
        .btn:hover { transform: translateY(-2px); }

        .btn-print { background-color: #2563eb; }
        .btn-close { background-color: #ef4444; }

        /* Print Override */
        @media print {
            @page {
                margin: 0;
                size: 80mm auto; /* Roll paper continuous */
            }
            body {
                background-color: transparent;
                padding: 0;
                margin: 0;
                display: block;
            }
            .receipt {
                width: 100%;
                max-width: 80mm;
                margin: 0;
                padding: 4mm 2mm; /* Give thermal margin */
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; color: #000; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak (Enter)
        </button>
        <button class="btn btn-close" onclick="window.close()">Tutup Layar</button>
    </div>

    <!-- Thermal Paper Area -->
    <div class="receipt">
        
        <!-- Header -->
        <div class="text-center font-bold uppercase mb-2" style="font-size: 16px;">
            WASHUP LAUNDRY
        </div>
        <div class="text-center" style="font-size: 11px;">
            Jl. Teknologi Bersih No. 99<br>
            Telp: 0812-3456-7890<br>
            www.washup-laundry.com
        </div>

        <div class="divider"></div>

        <!-- Meta Data -->
        <div class="flex">
            <span>Nota:</span>
            <span class="font-bold">{{ $order->order_code }}</span>
        </div>
        <div class="flex mt-1">
            <span>Tgl:</span>
            <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
        </div>
        <div class="flex mt-1">
            <span>Kasir:</span>
            <span class="uppercase">Admin</span>
        </div>

        <div class="divider"></div>

        <!-- Customer Card -->
        <div class="flex">
            <span>Cust:</span>
            <span class="font-bold uppercase">{{ substr($order->customer_name, 0, 15) }}</span>
        </div>
        @if($order->customer_phone)
        <div class="flex mt-1">
            <span>Hp/WA:</span>
            <span>{{ $order->customer_phone }}</span>
        </div>
        @endif

        <div class="divider-solid"></div>

        <!-- Bought Items -->
        <div class="mt-2 text-left font-bold uppercase" style="font-size: 12px;">
            {{ $order->service_name }}
        </div>
        <div class="flex mt-1">
            @php
                $qty = $order->weight ?? 1;
                $unit = str_contains(strtolower($order->service_name), 'sepatu') ? 'Psg' : (str_contains(strtolower($order->service_name), 'satuan') ? 'Pcs' : 'Kg');
                $pricePerUnit = $order->total_price / max(1, $qty);
            @endphp
            <span>{{ $qty }} {{ $unit }} x {{ number_format($pricePerUnit, 0, ',', '.') }}</span>
            <span class="font-bold">{{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        
        @if($order->package_detail)
        <div class="mt-1" style="font-size: 10px; font-style: italic;">
            Ket: {{ $order->package_detail }}
        </div>
        @endif

        <div class="divider-solid mt-2"></div>

        <!-- Calculation -->
        @if($order->discount > 0)
        <div class="flex mt-1">
            <span>Diskon:</span>
            <span>-{{ number_format($order->discount, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($order->tax > 0)
        <div class="flex mt-1">
            <span>Pajak:</span>
            <span>+{{ number_format($order->tax, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="flex mt-2 font-bold" style="font-size: 15px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($order->total_price - ($order->discount ?? 0) + ($order->tax ?? 0), 0, ',', '.') }}</span>
        </div>

        <div class="flex mt-2">
            <span>Bayar:</span>
            <span class="uppercase font-bold">{{ $order->payment_method ?? 'TUNAI' }}</span>
        </div>
        <div class="flex mt-1 mb-2">
            <span>Status:</span>
            <span class="uppercase font-bold" style="{{ $order->payment_status == 'Lunas' ? '' : 'border: 1px dotted #000; padding: 1px 4px;' }}">
                {{ $order->payment_status }}
            </span>
        </div>

        <div class="divider"></div>

        <!-- Pick up estimate -->
        @if($order->estimated_finish)
        <div class="mt-2 text-center" style="border: 1px solid #000; padding: 4px; border-radius: 4px;">
            <div style="font-size: 10px;">Estimasi Selesai:</div>
            <div class="font-bold mt-1">{{ \Carbon\Carbon::parse($order->estimated_finish)->format('d/m/Y H:i') }}</div>
        </div>
        @endif

        <!-- T&C -->
        <div class="text-center mt-4" style="font-size: 10px;">
            TERIMA KASIH<br>
            Harap simpan struk ini sebagai bukti.<br>
            Complain max 1x24 jam. Barang tak diambil &gt; 30 hari akan didonasikan.
        </div>
        
        <div class="text-center mt-4 font-bold" style="font-size: 10px; border-top: 1px dashed #000; padding-top: 12px; margin-bottom: 20px;">
            - - - - POTONG DI SINI - - - -
        </div>
    </div>

    <!-- Auto Print Script -->
    <script>
        // Bind enter key or let it print directly 
        window.addEventListener('keydown', function(e) {
            if(e.key === 'Enter') {
                window.print();
            }
        });
        
        // Auto show dialog after fully loaded
        setTimeout(() => {
            window.print();
        }, 500);
    </script>
</body>
</html>
