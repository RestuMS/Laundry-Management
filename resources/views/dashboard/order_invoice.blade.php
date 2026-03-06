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
            width: 58mm; /* 58mm thermal width (most common in Indonesia) */
            padding: 4mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            color: #000;
            font-size: 11px;
            line-height: 1.3;
        }

        /* For 80mm printer, change to .receipt-80 */
        .receipt-80 {
            width: 80mm;
            padding: 5mm;
            font-size: 13px;
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
            margin: 6px 0;
        }

        .divider-solid {
            border-top: 1px solid #000;
            margin: 4px 0;
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
        .btn-80mm { background-color: #7c3aed; }
        .btn-close { background-color: #ef4444; }

        /* Print Sizes */
        .size-selector {
            position: fixed;
            top: 20px;
            left: 20px;
            font-family: Arial, sans-serif;
            z-index: 50;
            background: white;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .size-selector label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .size-selector input[type="radio"] {
            width: 18px; height: 18px;
        }

        /* Payment history section */
        .payment-history {
            font-size: 10px;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        /* Print Override */
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                background-color: transparent;
                padding: 0;
                margin: 0;
                display: block;
            }
            .receipt {
                width: 100%;
                margin: 0;
                padding: 2mm 1.5mm;
                box-shadow: none;
            }
            .no-print, .size-selector {
                display: none !important;
            }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; color: #000; }
        }
    </style>
</head>
<body>

    @php
        $settings = \App\Models\Setting::pluck('value', 'key')->all();
        $storeName = $settings['store_name'] ?? 'LaundryPro';
        $storeAddress = $settings['store_address'] ?? 'Jl. Sudirman No.123';
        $storePhone = $settings['store_phone'] ?? '081234567890';
        $receiptFooter = $settings['receipt_footer'] ?? 'Terima kasih telah menggunakan jasa kami.';
        
        $payments = $order->payments()->latest()->get();
        $totalPaid = $order->total_paid;
        $grandTotal = $order->grand_total;
        $remaining = $order->remaining_balance;
    @endphp

    <!-- Paper Size Selector -->
    <div class="size-selector no-print">
        <p style="font-weight: bold; margin-bottom: 8px; font-size: 13px;">📐 Ukuran Kertas Thermal:</p>
        <label>
            <input type="radio" name="paper" value="58" checked onclick="setPaperSize('58')">
            58mm (Printer Kasir Kecil)
        </label>
        <label>
            <input type="radio" name="paper" value="80" onclick="setPaperSize('80')">
            80mm (Printer Kasir Besar)
        </label>
        
        <div style="margin-top: 10px; font-size: 11px; color: #4b5563; background: #f3f4f6; padding: 6px; border-radius: 6px;">
            💡 <b>Tips HP Android:</b> Nyalakan Bluetooth, 'Pairing' dengan printer, klik Cetak, lalu pilih printer Anda dari daftar PDF/Printer sistem.
        </div>
    </div>

    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Struk (Enter)
        </button>
        <button class="btn btn-close" onclick="window.close()">Tutup Layar</button>
    </div>

    <!-- Thermal Paper Area -->
    <div class="receipt" id="receipt">
        
        <!-- Store Header (Dynamic from Settings) -->
        <div class="text-center font-bold uppercase mb-2" style="font-size: 14px;">
            {{ strtoupper($storeName) }}
        </div>
        <div class="text-center" style="font-size: 9px;">
            {{ $storeAddress }}<br>
            Telp: {{ $storePhone }}
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
            <span class="uppercase">{{ auth()->user()->name ?? 'ADMIN' }}</span>
        </div>

        <div class="divider"></div>

        <!-- Customer Card -->
        <div class="flex">
            <span>Cust:</span>
            <span class="font-bold uppercase">{{ substr($order->customer_name, 0, 18) }}</span>
        </div>
        @if($order->customer_phone)
        <div class="flex mt-1">
            <span>Hp/WA:</span>
            <span>{{ $order->customer_phone }}</span>
        </div>
        @endif

        <div class="divider-solid"></div>

        <!-- Bought Items -->
        <div class="mt-2 text-left">
            @foreach($order->items as $item)
                <div class="font-bold uppercase mt-2 mb-1" style="font-size: 10px; line-height: 1.1;">
                    {{ $item->service_name }}
                </div>
                <div class="flex" style="font-size: 10px;">
                    <span>{{ $item->qty }} {{ $item->unit }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span class="font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
        
        @if($order->package_detail)
        <div class="mt-1" style="font-size: 9px; font-style: italic;">
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

        <div class="flex mt-2 font-bold" style="font-size: 13px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
        </div>

        <!-- Payment History -->
        @if($payments->count() > 0)
        <div class="divider mt-2"></div>
        <div class="payment-history">
            <div class="font-bold mb-1" style="font-size: 10px;">RIWAYAT PEMBAYARAN:</div>
            @foreach($payments as $payment)
            <div class="payment-row">
                <span>{{ $payment->created_at->format('d/m H:i') }} ({{ $payment->payment_method }})</span>
                <span>{{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="divider-solid mt-1"></div>
            <div class="flex font-bold mt-1">
                <span>Dibayar:</span>
                <span>Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
            </div>
            @if($remaining > 0)
            <div class="flex font-bold" style="border: 1px dotted #000; padding: 2px 4px; margin-top: 4px;">
                <span>SISA:</span>
                <span>Rp {{ number_format($remaining, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        @else
        <div class="flex mt-2">
            <span>Bayar:</span>
            <span class="uppercase font-bold">{{ $order->payment_method ?? 'TUNAI' }}</span>
        </div>
        @endif
        
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
            <div style="font-size: 9px;">Estimasi Selesai:</div>
            <div class="font-bold mt-1" style="font-size: 11px;">{{ \Carbon\Carbon::parse($order->estimated_finish)->format('d/m/Y H:i') }}</div>
        </div>
        @endif

        <!-- QR Code Barcode untuk Scanner Kasir -->
        <div class="mt-4 mb-2" style="display: flex; justify-content: center;">
            <div style="border: 1px dashed #ccc; border-radius: 4px; padding: 2px;">
                <img src="{{ route('qrcode.generate', ['data' => $order->order_code]) }}" 
                     alt="QR Code Order" 
                     style="display: block; width: 90px; height: 90px;"
                     onload="window.qrLoaded = true; checkPrint();" />
            </div>
        </div>
        <div class="text-center font-bold" style="font-size: 11px; margin-bottom: 6px;">
            {{ $order->order_code }}
        </div>

        <!-- T&C -->
        <div class="text-center mt-4" style="font-size: 9px;">
            {{ $receiptFooter }}<br>
            Complain max 1x24 jam.<br>
            Barang tak diambil &gt; 30 hari akan didonasikan.
        </div>
        
        <div class="text-center mt-4 font-bold" style="font-size: 9px; border-top: 1px dashed #000; padding-top: 10px; margin-bottom: 16px;">
            - - - POTONG DI SINI - - -
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function setPaperSize(size) {
            const receipt = document.getElementById('receipt');
            if (size === '80') {
                receipt.classList.add('receipt-80');
            } else {
                receipt.classList.remove('receipt-80');
            }
        }

        // Bind enter key
        window.addEventListener('keydown', function(e) {
            if(e.key === 'Enter') {
                window.print();
            }
        });

        // Wait for QR image loading
        window.qrLoaded = false;
        function checkPrint() {
            if (window.qrLoaded) {
                setTimeout(() => window.print(), 300);
            }
        }
        
        // Fallback auto show dialog
        setTimeout(() => {
            if (!window.qrLoaded) window.print();
        }, 1500);
    </script>
</body>
</html>
