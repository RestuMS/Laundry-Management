<?php

namespace App\Services;

use App\Models\Order;
use App\Models\NotificationLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappNotificationService
{
    /**
     * Status emoji and description mapping
     */
    private const STATUS_MAP = [
        'Diterima' => [
            'emoji' => '📦',
            'desc' => 'telah kami terima dan sedang mengantre untuk diproses.',
            'action' => 'Pesanan akan segera diproses oleh tim kami.',
        ],
        'Dicuci' => [
            'emoji' => '🫧',
            'desc' => 'sedang dalam proses *Pencucian*.',
            'action' => 'Pakaian sedang dicuci dengan detergen premium kami.',
        ],
        'Dikeringkan' => [
            'emoji' => '☀️',
            'desc' => 'sedang dalam tahap *Pengeringan*.',
            'action' => 'Pakaian sedang dikeringkan dengan suhu optimal.',
        ],
        'Disetrika' => [
            'emoji' => '👔',
            'desc' => 'sedang dalam proses *Penyetrikaan*.',
            'action' => 'Pakaian sedang disetrika hingga rapi sempurna.',
        ],
        'Quality Control' => [
            'emoji' => '🔍',
            'desc' => 'sedang masuk tahap *Quality Control* (Pengecekan akhir).',
            'action' => 'Tim QC kami memastikan semua pakaian bersih & rapi.',
        ],
        'Selesai' => [
            'emoji' => '🎉',
            'desc' => '*sudah Selesai* dan siap diambil sekarang!',
            'action' => 'Silakan ambil cucian Anda di loket pada jam operasional.',
        ],
        'Diambil' => [
            'emoji' => '🙏',
            'desc' => 'telah *Diambil*.',
            'action' => 'Terima kasih telah mempercayakan cucian Anda kepada kami!',
        ],
    ];

    /**
     * Kirim notifikasi WhatsApp update status otomatis.
     */
    public function sendTrackingUpdate(Order $order, ?string $oldStatus = null)
    {
        if (!$order->customer_phone) return false;

        $phone = $this->formatPhoneNumber($order->customer_phone);
        $trackingUrl = url('/track?q=' . $order->order_code);
        $storeName = Setting::where('key', 'store_name')->value('value') ?? 'LaundryPro';

        $statusInfo = self::STATUS_MAP[$order->status] ?? [
            'emoji' => '📋',
            'desc' => "saat ini berstatus: *{$order->status}*.",
            'action' => '',
        ];

        // Build rich message
        $message = "{$statusInfo['emoji']} *UPDATE STATUS LAUNDRY*\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "Halo kak *{$order->customer_name}*,\n\n";

        // Status change info
        if ($oldStatus) {
            $message .= "Status pesanan Anda berubah:\n";
            $message .= "▫️ Sebelumnya: _{$oldStatus}_\n";
            $message .= "▪️ Saat ini: *{$order->status}*\n\n";
        }

        $message .= "Pesanan laundry Anda {$statusInfo['desc']}\n";
        
        if ($statusInfo['action']) {
            $message .= "💡 _{$statusInfo['action']}_\n";
        }

        // Add order details
        $message .= "\n📋 *DETAIL PESANAN:*\n";
        $message .= "• Kode: *{$order->order_code}*\n";
        
        if ($order->items && $order->items->count() > 0) {
            $serviceNames = $order->items->pluck('service_name')->implode(', ');
            $message .= "• Layanan: {$serviceNames}\n";
        }

        // Show estimated time
        if ($order->estimated_finish && !in_array($order->status, ['Selesai', 'Diambil'])) {
            $estimatedRemaining = $order->estimated_remaining;
            $finishDate = $order->estimated_finish->translatedFormat('d M Y, H:i');
            $message .= "• Estimasi selesai: *{$finishDate}*\n";
            if ($estimatedRemaining) {
                $message .= "• Sisa waktu: ⏳ _{$estimatedRemaining}_\n";
            }
        }

        // Show progress bar emoji
        $progressBar = $this->buildProgressBar($order->status);
        $message .= "\n📊 *PROGRESS:*\n{$progressBar}\n";

        // Tracking link
        if ($order->status !== 'Diambil') {
            $message .= "\n🔗 Pantau cucian secara *Real-Time*:\n{$trackingUrl}\n";
        }

        $message .= "\n━━━━━━━━━━━━━━━━━━\n";
        $message .= "Salam wangi, *{$storeName}* ✨";

        // Send and log
        $sent = $this->dispatchToFonnte($phone, $message);

        // Log the notification
        $this->logNotification($order, $phone, 'status_update', $order->status, $message, $sent);

        return $sent;
    }

    /**
     * Build a visual text-based progress bar
     */
    private function buildProgressBar(string $currentStatus): string
    {
        $statuses = Order::STATUSES;
        $currentIndex = array_search($currentStatus, $statuses);
        if ($currentIndex === false) $currentIndex = 0;

        $result = '';
        foreach ($statuses as $index => $status) {
            if ($index <= $currentIndex) {
                $result .= "✅ {$status}\n";
            } elseif ($index === $currentIndex + 1) {
                $result .= "🔄 {$status} _(selanjutnya)_\n";
            } else {
                $result .= "⬜ {$status}\n";
            }
        }

        return $result;
    }

    /**
     * Kirim pesan tagihan invoice
     */
    public function sendInvoice(Order $order)
    {
        if (!$order->customer_phone) return false;

        $phone = $this->formatPhoneNumber($order->customer_phone);
        $storeName = Setting::where('key', 'store_name')->value('value') ?? 'LaundryPro';
        $totalHarga = number_format($order->total_price - ($order->discount ?? 0) + ($order->tax ?? 0), 0, ',', '.');
        $trackingUrl = url('/track?q=' . $order->order_code);

        $message = "🧾 *INVOICE LAUNDRY*\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "Halo kak *{$order->customer_name}*,\n\n";
        $message .= "Terima kasih telah mencuci di *{$storeName}*.\n";
        $message .= "Berikut ringkasan pesanan Anda:\n\n";
        $message .= "📌 *NO TRX:* {$order->order_code}\n";
        $message .= "👕 *LAYANAN YANG DIPESAN:*\n";
        foreach ($order->items as $item) {
            $subtotal = number_format($item->subtotal, 0, ',', '.');
            $message .= "  • {$item->service_name} ({$item->qty} {$item->unit}) — Rp {$subtotal}\n";
        }
        $message .= "\n💰 *TOTAL:* Rp {$totalHarga}\n";
        $message .= "💳 *STATUS BAYAR:* {$order->payment_status}\n";
        $message .= "📦 *STATUS BARANG:* {$order->status}\n\n";

        if ($order->payment_status !== 'Lunas') {
            $message .= "⚠️ _Mohon segera lakukan pelunasan saat pengambilan atau melalui scan QRIS toko kami._\n\n";
        }

        $message .= "🔗 Pantau cucian kakak secara realtime:\n{$trackingUrl}\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━\n";
        $message .= "Terima kasih! 🙏 *{$storeName}*";

        $sent = $this->dispatchToFonnte($phone, $message);

        // Log the notification
        $this->logNotification($order, $phone, 'invoice', $order->status, $message, $sent);

        return $sent;
    }

    /**
     * Log notification to database
     */
    private function logNotification(Order $order, string $phone, string $type, string $statusTrigger, string $message, bool $sent): void
    {
        try {
            NotificationLog::create([
                'order_id' => $order->id,
                'channel' => 'whatsapp',
                'phone' => $phone,
                'type' => $type,
                'status_trigger' => $statusTrigger,
                'message' => $message,
                'delivery_status' => $sent ? 'sent' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log notification: " . $e->getMessage());
        }
    }

    private function dispatchToFonnte($phone, $message)
    {
        $token = env('FONNTE_TOKEN', Setting::where('key', 'fonnte_token')->value('value'));

        if (empty($token)) {
            Log::info("Fonnte Token is missing, falling back to local log.\n[WA to {$phone}] =\n{$message}");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info("Fonnte API Sent successfully to {$phone}.");
                return true;
            } else {
                Log::error("Fonnte API failed: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Fonnte API Exception: " . $e->getMessage());
            return false;
        }
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
