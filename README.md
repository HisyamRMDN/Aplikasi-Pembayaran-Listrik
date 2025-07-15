# Sistem Pengelolaan Token Listrik

Aplikasi web untuk pengelolaan sistem token listrik yang dibangun menggunakan Laravel 11. Sistem ini memungkinkan pengelolaan pelanggan, pencatatan penggunaan listrik, pembuat tagihan, dan proses pembayaran.

## Link Python dan skenario
-  https://docs.google.com/document/d/1XPdGrETaoqoKniPeNVJIWXpVTBVxVGEVQWSfK9KLsuE/edit?tab=t.0

## 🚀 Fitur Utama

### Untuk Admin

-   **Dashboard Admin** - Overview statistik sistem
-   **Manajemen Pelanggan** - CRUD data pelanggan
-   **Manajemen Penggunaan** - Pencatatan meter listrik
-   **Manajemen Tagihan** - Pembuatan dan pengelolaan tagihan
-   **Manajemen Pembayaran** - Proses pembayaran tagihan
-   **Laporan** - Berbagai laporan sistem

### Untuk Pelanggan

-   **Dashboard Pelanggan** - Informasi akun dan ringkasan
-   **Riwayat Penggunaan** - Melihat history penggunaan listrik
-   **Tagihan** - Melihat tagihan dan status pembayaran
-   **Profil** - Informasi data pelanggan

## 🛠️ Teknologi yang Digunakan

-   **Backend**: Laravel 11
-   **Frontend**: Blade Templates, Tailwind CSS
-   **Database**: MySQL
-   **Authentication**: Laravel Multi-Guard (Admin & Pelanggan)
-   **Build Tools**: Vite
-   **Package Manager**: Composer, NPM

## 📋 Persyaratan Sistem

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   MySQL >= 8.0
-   Web Server (Apache/Nginx)

## 🔧 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd tokenlistrikserkom
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Konfigurasi Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=token_listrik
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Migrasi dan Seeding Database

```bash
# Jalankan migrasi
php artisan migrate

# Jalankan seeder untuk data awal
php artisan db:seed
```

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 🗄️ Struktur Database

### Tabel Utama

1. **users** - Data admin sistem
2. **levels** - Level akses pengguna
3. **tarifs** - Tarif listrik berdasarkan daya
4. **pelanggans** - Data pelanggan
5. **penggunaans** - Record penggunaan listrik
6. **tagihans** - Data tagihan pelanggan
7. **pembayarans** - Record pembayaran

### Relasi Database

-   Pelanggan memiliki tarif (Many to One)
-   Pelanggan memiliki banyak penggunaan (One to Many)
-   Pelanggan memiliki banyak tagihan (One to Many)
-   Tagihan memiliki banyak pembayaran (One to Many)

## 👥 Akun Default

Setelah menjalankan seeder, tersedia akun default:

### Admin

-   **Username**: admin
-   **Password**: password

### Pelanggan (contoh)

-   **Username**: pelanggan1
-   **Password**: password

## 🎯 Penggunaan

### Login Admin

1. Akses `/` atau `/admin/login`
2. Masukkan kredensial admin
3. Kelola sistem melalui dashboard admin

### Login Pelanggan

1. Akses `/pelanggan/login`
2. Masukkan kredensial pelanggan
3. Lihat tagihan dan riwayat penggunaan

## 📁 Struktur Proyek

```
tokenlistrikserkom/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controllers aplikasi
│   │   └── Middleware/      # Custom middleware
│   └── Models/              # Eloquent models
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── views/               # Blade templates
│   │   ├── admin/           # Views untuk admin
│   │   ├── pelanggan/       # Views untuk pelanggan
│   │   ├── auth/            # Views autentikasi
│   │   └── layouts/         # Layout templates
│   ├── css/                 # CSS files
│   └── js/                  # JavaScript files
├── routes/
│   └── web.php              # Route definitions
└── public/                  # Public assets
```

## 🔐 Sistem Autentikasi

Aplikasi menggunakan Laravel Multi-Guard Authentication:

-   **web** guard untuk admin
-   **pelanggan** guard untuk pelanggan

### Middleware

-   `auth` - Proteksi route admin
-   `auth:pelanggan` - Proteksi route pelanggan

## 🎨 UI/UX

-   **Design System**: Tailwind CSS
-   **Icons**: Heroicons
-   **Responsive**: Mobile-first approach
-   **Theme**: Modern dan clean interface

## 🚦 Status Pembayaran

Sistem mendukung status tagihan:

-   **Belum Lunas** - Tagihan yang belum dibayar
-   **Lunas** - Tagihan yang sudah dibayar

## 📊 Laporan dan Dashboard

### Dashboard Admin

-   Total pelanggan
-   Total penggunaan
-   Total tagihan
-   Total pembayaran
-   Tagihan belum lunas

### Dashboard Pelanggan

-   Informasi akun
-   Alert tagihan belum lunas
-   Riwayat penggunaan terbaru
-   Tagihan terbaru

## 🔧 Konfigurasi Tambahan

### Mail Configuration

Untuk fitur email (jika diperlukan):

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Cache Configuration

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

## 🐛 Troubleshooting

### Masalah Umum

1. **Error 500**: Pastikan semua dependency terinstall dan konfigurasi database benar
2. **Assets tidak muncul**: Jalankan `npm run build`
3. **Login gagal**: Pastikan seeder sudah dijalankan
4. **Database error**: Periksa konfigurasi database di `.env`

### Logs

```bash
# Lihat log aplikasi
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Kontak

-   **Developer**: [Nama Developer]
-   **Email**: [email@example.com]
-   **Project Link**: [repository-url]

## 🙏 Acknowledgments

-   Laravel Framework
-   Tailwind CSS
-   Heroicons
-   Community Laravel Indonesia

---

**Catatan**: Pastikan untuk mengubah kredensial default setelah instalasi untuk keamanan yang lebih baik.
