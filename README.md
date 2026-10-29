# FinShield

Web-app edukasi literasi keuangan: simulasi algoritma judol (nilai harapan & house edge), kalkulator bunga pinjol, dan perencana alokasi uang. Dibangun dengan **Laravel 11**, **Blade**, **Tailwind CSS 3**, **Alpine.js**, dan **Chart.js**.

Seluruh angka, rumus, tabel, dan grafik berubah langsung saat input diubah, tanpa reload halaman.

## Isi paket

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
pasang.sh            (skrip salin untuk Linux / Git Bash)
```

## Kebutuhan

| Alat | Versi minimal |
|---|---|
| PHP | 8.2 (disarankan 8.3) |
| Composer | 2.x |
| Node.js | 18 (disarankan 20 LTS) |

Aplikasi ini **tidak memerlukan database**.

---

# A. Ubuntu (22.04 / 24.04)

## 1. Instal PHP dan ekstensinya

Ubuntu 24.04:

```bash
sudo apt update
sudo apt install -y php php-cli php-mbstring php-xml php-curl php-zip php-sqlite3 php-bcmath php-intl unzip curl git
php -v
```

Ubuntu 22.04 (bawaannya PHP 8.1, jadi tambahkan PPA):

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-sqlite3 php8.3-bcmath php8.3-intl unzip curl git
php -v
```

## 2. Instal Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
composer --version
```

## 3. Instal Node.js 20

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

## 4. Buat project Laravel

```bash
cd ~
composer create-project laravel/laravel finshield "^11.0"
cd finshield
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' .env
```

## 5. Instal paket frontend

```bash
npm install
npm uninstall @tailwindcss/vite 2>/dev/null || true
npm install -D tailwindcss@3 postcss autoprefixer
npm install alpinejs chart.js
```

## 6. Salin file FinShield

Ekstrak `finshield-kode.zip`, masuk ke foldernya, lalu:

```bash
bash pasang.sh ~/finshield
```

## 7. Jalankan

Buka dua terminal di `~/finshield`:

```bash
# Terminal 1
npm run dev

# Terminal 2
php artisan serve
```

Buka **http://127.0.0.1:8000**

---

# B. Windows 10 / 11

Ada dua jalur. **Jalur 1 (Laragon)** paling mudah karena PHP, Composer, dan Node.js sudah termasuk.

## Jalur 1: Laragon (disarankan)

1. Unduh **Laragon Full** dari https://laragon.org/download lalu instal (pilih lokasi default `C:\laragon`).
2. Buka Laragon dan klik **Start All**.
3. Klik tombol **Terminal** di Laragon, lalu cek versinya:

   ```powershell
   php -v
   composer --version
   node -v
   ```

   Pastikan PHP 8.2 atau lebih baru dan Node 18 atau lebih baru. Jika lebih lama, perbarui lewat menu Laragon (**Menu > PHP / Node.js > Versi**).

4. Buat project:

   ```powershell
   cd C:\laragon\www
   composer create-project laravel/laravel finshield "^11.0"
   cd finshield
   ```

5. Atur session dan cache ke file. Buka `.env` dengan Notepad/VS Code, ubah dua baris ini:

   ```
   SESSION_DRIVER=file
   CACHE_STORE=file
   ```

   Atau lewat PowerShell:

   ```powershell
   (Get-Content .env) -replace '^SESSION_DRIVER=.*','SESSION_DRIVER=file' -replace '^CACHE_STORE=.*','CACHE_STORE=file' | Set-Content .env
   ```

6. Instal paket frontend:

   ```powershell
   npm install
   npm install -D tailwindcss@3 postcss autoprefixer
   npm install alpinejs chart.js
   ```

   Bila project Anda sudah memuat `@tailwindcss/vite` (Tailwind v4), jalankan `npm uninstall @tailwindcss/vite` sebelum langkah di atas.

7. Salin file FinShield. Ekstrak `finshield-kode.zip` (misalnya ke `C:\Users\NamaAnda\Downloads\finshield-kode`), lalu di PowerShell:

   ```powershell
   $src = "C:\Users\NamaAnda\Downloads\finshield-kode"
   $dst = "C:\laragon\www\finshield"

   Copy-Item "$src\routes\web.php" "$dst\routes\web.php" -Force
   Copy-Item "$src\app\Http\Controllers\FinShieldController.php" "$dst\app\Http\Controllers\" -Force
   New-Item -ItemType Directory -Force "$dst\resources\views\layouts","$dst\resources\views\components" | Out-Null
   Copy-Item "$src\resources\views\layouts\app.blade.php" "$dst\resources\views\layouts\" -Force
   Copy-Item "$src\resources\views\components\icon.blade.php" "$dst\resources\views\components\" -Force
   Copy-Item "$src\resources\views\*.blade.php" "$dst\resources\views\" -Force
   Copy-Item "$src\resources\css\app.css" "$dst\resources\css\" -Force
   Copy-Item "$src\resources\js\app.js" "$dst\resources\js\" -Force
   Copy-Item "$src\tailwind.config.js","$src\postcss.config.js","$src\vite.config.js" "$dst\" -Force
   ```

   Ganti `NamaAnda` dengan nama pengguna Windows Anda. Jika Anda memakai Git Bash, cukup `bash pasang.sh /c/laragon/www/finshield`.

8. Jalankan. Buka dua jendela terminal di `C:\laragon\www\finshield`:

   ```powershell
   # Terminal 1
   npm run dev

   # Terminal 2
   php artisan serve
   ```

   Buka **http://127.0.0.1:8000**

## Jalur 2: Instal manual dengan winget

Buka PowerShell sebagai Administrator:

```powershell
winget install PHP.PHP.8.3
winget install Composer.Composer
winget install OpenJS.NodeJS.LTS
```

Tutup dan buka kembali PowerShell, lalu cek `php -v`, `composer --version`, `node -v`.

Aktifkan ekstensi PHP yang dibutuhkan: cari file `php.ini` (`php --ini`). Jika belum ada, salin `php.ini-development` menjadi `php.ini`. Lalu hapus tanda `;` di depan baris berikut:

```
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_sqlite
extension=zip
extension=intl
```

Setelah itu lanjutkan dari **Jalur 1 langkah 4**.

---

## Build untuk produksi (Ubuntu & Windows)

```bash
npm run build
php artisan route:cache
php artisan view:cache
php artisan serve
```

## Pemecahan masalah

| Gejala | Solusi |
|---|---|
| Halaman tampil polos tanpa gaya | Pastikan `npm run dev` masih berjalan, atau jalankan `npm run build` |
| `Vite manifest not found` | Jalankan `npm run build` sekali |
| `could not find driver` | Pastikan `.env` memakai `SESSION_DRIVER=file`, dan ekstensi `pdo_sqlite` aktif |
| `composer` / `php` tidak dikenali (Windows) | Tutup dan buka ulang terminal, atau pakai Terminal bawaan Laragon |
| Port 8000 terpakai | `php artisan serve --port=8001` |
| Setelah mengubah file Blade tidak berubah | `php artisan view:clear` |
| Tailwind v4 bentrok | `npm uninstall @tailwindcss/vite`, lalu `npm install -D tailwindcss@3 postcss autoprefixer` |

## Catatan

FinShield adalah alat edukasi. Simulasi dan kalkulator memakai asumsi sederhana, bukan tarif resmi atau nasihat keuangan personal. Untuk cek legalitas pinjaman dan pengaduan, hubungi Kontak OJK 157.