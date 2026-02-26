<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - PDF</title>
    <!-- Use Tailwind via CDN for quick styling in print mode -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; background: white; }
            .print-container { box-shadow: none !important; margin: 0 !important; padding: 20px !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 text-slate-800">

    <div class="max-w-4xl mx-auto bg-white p-12 rounded-2xl shadow-xl print-container">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-slate-200 pb-8 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg">
                    LW
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Washup Laundry</h1>
                    <p class="text-slate-500 font-medium">Laporan Rekapitulasi Keuangan</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold text-slate-700 uppercase tracking-widest">Periode</h2>
                <p class="text-xl font-black text-blue-600">{{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-6 mb-10">
            <div class="bg-blue-50 border border-blue-100 p-5 rounded-xl">
                <p class="text-sm font-bold text-slate-500 uppercase">Pemasukan Kotor</p>
                <p class="text-2xl font-black text-blue-600 mt-1">Rp {{ number_format($omset, 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-50 border border-red-100 p-5 rounded-xl">
                <p class="text-sm font-bold text-slate-500 uppercase">Pengeluaran</p>
                <p class="text-2xl font-black text-red-600 mt-1">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 p-5 rounded-xl">
                <p class="text-sm font-bold text-slate-500 uppercase">Laba Bersih</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($laba, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="mb-10">
            <h3 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">Rincian Transaksi Pemasukan</h3>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase font-bold text-xs tracking-wider">
                        <th class="p-4 rounded-tl-lg">Waktu</th>
                        <th class="p-4">Layanan</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4 text-right rounded-tr-lg">Nominal</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-slate-700 divide-y divide-slate-100">
                    @forelse($orders as $o)
                    <tr>
                        <td class="p-4">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-4"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">{{ $o->service_name }}</span></td>
                        <td class="p-4">{{ $o->customer_name }}</td>
                        <td class="p-4 text-right font-bold text-slate-800">Rp {{ number_format($o->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-400 italic">Tidak ada transaksi pemasukan berstatus Lunas pada bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Expenses Table -->
        <div class="mb-10">
            <h3 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">Rincian Biaya Pengeluaran</h3>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase font-bold text-xs tracking-wider">
                        <th class="p-4 rounded-tl-lg">Tanggal</th>
                        <th class="p-4">Nama Pengeluaran</th>
                        <th class="p-4">Catatan Tambahan</th>
                        <th class="p-4 text-right rounded-tr-lg">Nominal Terpotong</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-slate-700 divide-y divide-slate-100">
                    @forelse($expenses as $exp)
                    <tr>
                        <td class="p-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                        <td class="p-4 font-bold text-slate-800">{{ $exp->name }}</td>
                        <td class="p-4 text-slate-500">{{ $exp->note ?? '-' }}</td>
                        <td class="p-4 text-right font-bold text-red-500">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-400 italic">Tidak ada data pengeluaran pada bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer / Signature -->
        <div class="mt-16 pt-8 border-t border-slate-200 flex justify-end">
            <div class="text-center w-64">
                <p class="text-sm font-medium text-slate-500 mb-16">{{ date('d F Y') }}</p>
                <p class="text-sm border-t border-slate-300 pt-2 font-bold text-slate-800">Pemilik / Keuangan</p>
            </div>
        </div>

    </div>

    <!-- Action Buttons -->
    <div class="fixed bottom-8 right-8 flex gap-4 no-print">
        <button onclick="window.close()" class="bg-slate-700 hover:bg-slate-800 text-white px-6 py-3 rounded-full font-bold shadow-lg transition-transform hover:-translate-y-1">Tutup</button>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-blue-500/30 flex items-center gap-2 transition-transform hover:-translate-y-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print / Simpan PDF
        </button>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 800);
        }
    </script>
</body>
</html>
