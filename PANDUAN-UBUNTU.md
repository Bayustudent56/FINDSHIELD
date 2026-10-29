# FinShield: Panduan Ubuntu dari Nol

Teruji pada Ubuntu 24.04 (PHP 8.3 bawaan). Untuk Ubuntu 22.04 lihat catatan di langkah 1.

## 1. Instal PHP, ekstensi, dan alat dasar

```bash
sudo apt update
sudo apt install -y php php-cli php-mbstring php-xml php-curl php-zip php-sqlite3 php-bcmath php-intl unzip curl git
php -v    # harus PHP 8.2 atau lebih baru
```

Ubuntu 22.04 hanya membawa PHP 8.1. Tambahkan PPA dulu, lalu instal dengan paket `php8.3-*`:

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-sqlite3 php8.3-bcmath php8.3-intl unzip curl git
```

## 2. Instal Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
composer --version
```

## 3. Instal Node.js 20 LTS

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

## 4. Buat project Laravel 11

```bash
cd ~
composer create-project laravel/laravel finshield "^11.0"
cd finshield

# Aplikasi ini tidak memakai database: pakai session & cache berbasis file
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' .env
```

## 5. Instal Tailwind CSS 3, Alpine.js, dan Chart.js

```bash
npm install
npm install -D tailwindcss@3 postcss autoprefixer
npm install alpinejs chart.js
```

Jika project baru Anda sudah membawa paket `@tailwindcss/vite` (Tailwind v4), hapus dulu agar tidak bentrok dengan Tailwind 3:

```bash
npm uninstall @tailwindcss/vite
```

## 6. Salin file FinShield

Ekstrak paket ini, lalu dari foldernya jalankan:

```bash
bash pasang.sh ~/finshield
```

Script ini menimpa 13 file berikut. Bila ingin menyalin manual, letaknya sama persis:

```
routes/web.php
app/Http/Controllers/FinShieldController.php
resources/views/layouts/app.blade.php
resources/views/components/icon.blade.php
resources/views/beranda.blade.php
resources/views/edukasi-judol.blade.php
resources/views/kalkulator-pinjol.blade.php
resources/views/kelola-uang.blade.php
resources/css/app.css
resources/js/app.js
tailwind.config.js
postcss.config.js
vite.config.js
```

## 7. Jalankan (mode pengembangan)

Buka dua terminal di folder `~/finshield`:

```bash
# Terminal 1: Vite (kompilasi Tailwind + JS secara live)
npm run dev

# Terminal 2: server Laravel
php artisan serve
```

Buka http://127.0.0.1:8000

## 8. Build untuk produksi

```bash
npm run build
php artisan route:cache && php artisan view:cache
php artisan serve --host=0.0.0.0 --port=8000
```

## Pemecahan masalah

| Gejala | Solusi |
|---|---|
| Halaman tanpa gaya (polos) | Pastikan `npm run dev` berjalan, atau jalankan `npm run build` |
| `Vite manifest not found` | Jalankan `npm run build` sekali |
| Error `Unable to locate file in Vite manifest` | Pastikan `vite.config.js` sudah diganti dengan versi paket ini |
| `could not find driver` | `sudo apt install php-sqlite3` lalu ulangi, atau pastikan `.env` memakai `SESSION_DRIVER=file` |
| Port 8000 terpakai | `php artisan serve --port=8001` |
