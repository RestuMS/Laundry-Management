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
        // Pastikan phone number tersedia
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

        // TODO: Eksekusi API Watzap / Fonnte cURL request disini
        // Untuk mock automation, kita tulis ke \Log::info 
        \Illuminate\Support\Facades\Log::info("WhatsApp Automation Sent to {$phone}: \n" . $message);

        return true;
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
