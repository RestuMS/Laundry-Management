<?php

namespace App\Services;

use App\Models\Order;

class WhatsappNotificationService
{
    /**
     * Kirim notifikasi WhatsApp otomatis.
     * Dalam implementasi asli, bagian ini akan dihubungkan dengan API Watzap/Fonnte/Wablas dll.
     * Karena di lokal, kita cukup mecatat via logger atau return string.
     */
    public function sendTrackingUpdate(Order $order)
    {
        if (!$order->customer_phone) return false;

        $phone = $this->formatPhoneNumber($order->customer_phone);
        $trackingUrl = url('/track?q=' . $order->order_code);
        $storeName = \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'LaundryPro';

        $message = "Halo kak *{$order->customer_name}*,\n\n";
        
        switch ($order->status) {
            case 'Diterima':
                $message .= "Pesanan laundry kakak dengan kode *{$order->order_code}* telah kami terima dan sedang mengantre untuk diproses. 🧺\n"; break;
            case 'Dicuci':
                $message .= "Pesanan laundry kakak sedang dalam proses *Pencucian*. 🫧\n"; break;
            case 'Dikeringkan':
                $message .= "Pesanan laundry kakak sedang dalam tahap *Pengeringan*. ☀️\n"; break;
            case 'Disetrika':
                $message .= "Pesanan laundry kakak sedang dalam proses *Penyetrikaan*. 👔\n"; break;
            case 'Quality Control':
                $message .= "Pesanan laundry kakak sedang masuk tahap *Quality Control* (Pengecekan akhir). 🔍\n"; break;
            case 'Selesai':
                $message .= "Yuhuu! Pesanan laundry kakak *sudah Selesai* dan siap diambil sekarang juga di loket! 🎉🥳\n"; break;
            case 'Diambil':
                $message .= "Pesanan laundry kakak telah *Diambil*. Terima kasih telah mencuci pakaian di {$storeName}! Sampai jumpa kembali. 🙏\n"; break;
            default:
                $message .= "Status pesanan laundry kakak saat ini: *{$order->status}*.\n"; break;
        }

        if ($order->status !== 'Diambil') {
            $message .= "\nPantau terus pesanan kakak secara *Real-time* di link berikut:\n{$trackingUrl}\n\n";
            $message .= "Salam wangi,\n*{$storeName}*";
        }

        return $this->dispatchToFonnte($phone, $message);
    }

    /**
     * Kirim pesan tagihan invoice
     */
    public function sendInvoice(Order $order)
    {
        if (!$order->customer_phone) return false;

        $phone = $this->formatPhoneNumber($order->customer_phone);
        $storeName = \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'LaundryPro';
        $totalHarga = number_format($order->total_price - ($order->discount ?? 0) + ($order->tax ?? 0), 0, ',', '.');
        $trackingUrl = url('/track?q=' . $order->order_code);

        $message = "Halo kak *{$order->customer_name}*,\n\n";
        $message .= "Terima kasih telah mencuci di *{$storeName}*.\n";
        $message .= "Berikut ringkasan pesanan anda:\n\n";
        $message .= "🧾 *NO TRX:* {$order->order_code}\n";
        $message .= "👕 *LAYANAN:* {$order->service_name}\n";
        $message .= "⚖️ *Diterima:* " . ($order->weight ?? 1) . " (Kg/Satuan)\n";
        $message .= "💰 *TOTAL:* Rp {$totalHarga}\n";
        $message .= "💳 *STATUS BAYAR:* {$order->payment_status}\n";
        $message .= "📦 *STATUS BARANG:* {$order->status}\n\n";

        if ($order->payment_status !== 'Lunas') {
            $message .= "Mohon segera lakukan pelunasan saat pengambilan atau melalui scan QRIS toko kami.\n\n";
        }

        $message .= "Kakak bisa memantau cucian Kakak secara realtime melalui link resmi berikut:\n{$trackingUrl}\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->dispatchToFonnte($phone, $message);
    }

    private function dispatchToFonnte($phone, $message)
    {
        $token = env('FONNTE_TOKEN', \App\Models\Setting::where('key', 'fonnte_token')->value('value'));

        if (empty($token)) {
            \Illuminate\Support\Facades\Log::info("Fonnte Token is missing, falling back to local log.\n[WA to {$phone}] =\n{$message}");
            return false;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                \Illuminate\Support\Facades\Log::info("Fonnte API Sent successfully to {$phone}.");
                return true;
            } else {
                \Illuminate\Support\Facades\Log::error("Fonnte API failed: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Fonnte API Exception: " . $e->getMessage());
            return false;
        }
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // Pastikan kode negara standar (misal +62 untuk Indonesia dikonversi 62)
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
