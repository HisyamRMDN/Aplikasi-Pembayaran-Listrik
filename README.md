<div align="center">
  <img src="public/image/mock-up.png" alt="Tampilan VoltPay Portfolio" width="100%" />

  # VOLTPAY — Sistem Pembayaran Listrik Pascabayar

  [![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
  [![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3%20%7C%208.4-blue.svg)](https://php.net)
  [![Midtrans](https://img.shields.io/badge/Payment-Midtrans%20Snap-blue)](https://midtrans.com)
  [![Tests](https://img.shields.io/badge/Tests-Passed%20(25%20tests)-green.svg)](tests)

  <p>Aplikasi web pengelolaan listrik pascabayar terintegrasi dengan gateway pembayaran online Midtrans Snap (Sandbox), manajemen pelanggan, pencatatan kWh, dan rekapitulasi laporan otomatis.</p>
</div>

---

## 🚀 Fitur Utama

### Untuk Admin & Petugas
- **Dashboard Admin:** Ringkasan statistik pendapatan bulanan, status tagihan, dan visualisasi data.
- **Manajemen Pelanggan & Tarif:** Pengelolaan tarif per kWh, daya listrik, serta pendaftaran pelanggan.
- **Pencatatan Meter Listrik:** Input meter awal dan akhir dengan validasi otomatis agar tidak kurang dari bulan sebelumnya.
- **Pembuatan Tagihan Otomatis:** Perhitungan tagihan secara real-time berdasarkan pemakaian kWh dikalikan tarif golongan.
- **Manajemen Pembayaran & Kasir:** Penerimaan pembayaran tunai di loket dengan cetak struk fisik/PDF.
- **Laporan & Export:** Cetak laporan rekapitulasi bulanan dan ekspor data dalam format CSV.

### Untuk Pelanggan (Portal Mandiri)
- **Ringkasan Akun:** Informasi daya listrik, nomor KWH, dan tagihan berjalan.
- **Riwayat Penggunaan & Tagihan:** Riwayat bulanan lengkap dengan status pelunasan.
- **Pembayaran Online (Midtrans Snap):** Pembayaran mandiri via Virtual Account, QRIS, maupun E-Wallet langsung dari portal.
- **Cetak Struk Mandiri:** Pelanggan bisa mengunduh atau mencetak struk pelunasan resmi secara online.

---

## 💳 Integrasi Midtrans Snap

Aplikasi ini menggunakan **Midtrans Snap API** untuk metode pembayaran online mandiri:
- **Reuse Snap Token:** Token Snap disimpan di Cache berdasarkan ID Tagihan dan nominal denda berjalan. Jika pengguna membuka modal pembayaran berulang kali, token yang sama digunakan kembali (mengurangi beban API).
- **Biaya Admin Terpusat:** Diatur melalui `config('midtrans.admin_fee')` (default Rp 2.500).
- **Penanganan Status Non-Settlement:** Webhook memproses status `settlement` dan `capture` (accept). Status `expire`, `cancel`, dan `deny` otomatis membersihkan cache token agar pelanggan bisa membuat token baru.
- **Idempotency & DB Lock:** Menggunakan `lockForUpdate()` dalam transaksi database untuk mencegah pembayaran ganda.

---

## 🛠️ Cara Instalasi Lokal

### 1. Clone Repository & Install Dependensi
```bash
git clone [https://github.com/HisyamRMDN/Aplikasi-Pembayaran-Listrik.git](https://github.com/HisyamRMDN/Aplikasi-Pembayaran-Listrik.git)
cd Aplikasi-Pembayaran-Listrik
composer install
npm install && npm run build
