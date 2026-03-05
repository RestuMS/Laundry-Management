# 🧺 LaundryPro - Sistem Manajemen Laundry

Aplikasi manajemen laundry profesional berbasis web untuk mengelola seluruh operasional bisnis laundry mulai dari penerimaan order, pelacakan status, manajemen keuangan, hingga notifikasi otomatis ke pelanggan.

## ✨ Fitur Utama

### 👥 Multi-Role Authentication
- **Admin** – Akses penuh: dashboard, order, layanan, keuangan, laporan, karyawan, pengaturan
- **Kasir** – Fokus operasional: terima order, scan pickup, kelola pelanggan
- **Owner** – Monitoring bisnis: omset, laporan, pengeluaran, inventaris

### 📦 Manajemen Order
- CRUD order dengan multi-layanan per order
- 7 tahapan status: Diterima → Dicuci → Dikeringkan → Disetrika → Quality Control → Selesai → Diambil
- Status history (jejak perubahan) dengan timestamp dan nama pengubah
- Estimasi waktu selesai
- QR Code per order untuk scan pickup

### 💰 Pembayaran & Keuangan
- **Pembayaran Parsial (DP/Cicilan)** 
– Catat pembayaran bertahap, auto-update status
- Manajemen pengeluaran harian
- Laporan keuangan dengan filter tanggal
- Export laporan ke PDF & Excel (CSV)
- Dashboard omset real-time

### 📊 Dashboard & Laporan
- **Admin Dashboard**: Total order, order diproses, omset hari ini, chart pendapatan mingguan, top layanan
- **Owner Dashboard**: Omset bulanan + pertumbuhan, pelanggan loyal, ranking layanan, grafik performa
- **Kasir Dashboard**: Order hari ini, quick search, scan QR pickup

### 🔔 Notifikasi
- **WhatsApp Otomatis** via Fonnte API – dikirim saat status order berubah
- **Notifikasi In-App** (Bell Icon) – real-time dengan polling
- Reminder otomatis via scheduling

### 🖨️ Cetak Struk
- Format thermal printer 58mm & 80mm
- Informasi toko dinamis dari pengaturan
- Riwayat pembayaran DP tercetak di struk
- QR Code pada struk
- Auto-print saat halaman dibuka

### 🔒 Keamanan
- Register publik dinonaktifkan (hanya Admin bisa tambah user)
- Logout menggunakan POST (CSRF protected)
- Rate limiting pada login (5 percobaan per menit)
- Activity Log/Audit Trail (semua aksi tercatat)
- Soft Delete pada semua model utama
- Role-based middleware

### 📱 Fitur Lainnya
- Tracking order publik via QR Code / nomor resi
- Manajemen pelanggan dengan statistik
- Manajemen inventaris (bahan baku) dengan auto-deduct
- CRUD layanan
- Manajemen karyawan
- Pengaturan toko (nama, alamat, telepon, logo, footer struk)
- Password reset via email
- Responsive design (mobile-first)

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 10 |
| PHP | >= 8.1 |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Charts | Chart.js |
| Icons | Phosphor Icons |
| QR Code | chillerlan/php-qrcode |
| WA Gateway | Fonnte API |
| Build Tool | Vite |

---

## 📋 Persyaratan Sistem

- PHP >= 8.1
- MySQL >= 5.7 / MariaDB >= 10.3
- Composer >= 2.x
- Node.js >= 16.x
- NPM >= 8.x

---

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone <repository-url> laundry-app
cd laundry-app
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=db_laundry
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Database
Buat database MySQL dengan nama `db_laundry` (atau sesuai konfigurasi `.env`).

### 5. Jalankan Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Frontend Assets
```bash
npm run build
```

### 7. Jalankan Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

---

## 🔑 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@laundry.com | password123 |
| Kasir | kasir@laundry.com | password123 |
| Owner | owner@laundry.com | password123 |

> ⚠️ **Penting:** Segera ubah password default setelah login pertama kali!

---

## ⚙️ Konfigurasi WhatsApp (Opsional)

Untuk mengaktifkan notifikasi WhatsApp otomatis:

1. Daftar di [Fonnte.com](https://fonnte.com)
2. Dapatkan API Token
3. Masukkan token di menu **Pengaturan** → **Fonnte Token**

---

## 📁 Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/     # Semua controller
│   ├── Middleware/       # Auth & Role middleware
│   └── Requests/        # Form validation
├── Models/              # Eloquent models
├── Observers/           # Order observer (auto WA + log)
└── Services/            # WA notification service

resources/views/
├── auth/                # Login, forgot password
├── dashboard/           # Semua halaman dashboard
├── layouts/             # Layout utama
└── tracking/            # Tracking publik

database/
├── migrations/          # Schema database
└── seeders/             # Data awal (user default)
```

---

## 🆘 Troubleshooting

### Login tidak bisa
- Pastikan sudah menjalankan `php artisan db:seed`
- Periksa konfigurasi database di `.env`

### Struk tidak tercetak
- Pastikan browser mengizinkan popup
- Cek pengaturan printer (58mm atau 80mm)

### WhatsApp tidak terkirim
- Pastikan Fonnte Token sudah diisi di Pengaturan
- Periksa format nomor HP pelanggan (diawali 08xxx)

---

## 📄 Lisensi

Hak cipta dilindungi. Software ini dilisensikan untuk penggunaan komersial oleh pemilik lisensi.
