# Presensi SMK 3

## Clone Repository

```bash
git clone https://github.com/af133/presensi-smk-3.git
cd presensi-smk-3
```

---

## Install Dependency PHP

```bash
composer install
```

---

## Install Dependency Frontend

```bash
npm install
```

---

## Copy File Environment

Linux / macOS

```bash
cp .env.example .env
```

Windows

```bash
copy .env.example .env
```

---

## Konfigurasi Database

Edit file `.env` sesuai dengan konfigurasi database Anda.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensismk
DB_USERNAME=root
DB_PASSWORD=
```

---

## Generate Application Key

```bash
php artisan key:generate
```

---

## Import Database

Project ini telah menyediakan custom Artisan Command yang akan secara otomatis:

* Menjalankan `migrate:fresh`
* Mengimpor file SQL
* Menjalankan seluruh seeder

Cukup jalankan perintah berikut:

```bash
php artisan app:import-sql
```

---

## Jalankan Frontend

Mode development:

```bash
npm run dev
```

atau build production:

```bash
npm run build
```

---

## Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di:

```
http://127.0.0.1:8000
```

---

## Persyaratan

* PHP 8.2+
* Composer
* Node.js & NPM
* MySQL/MariaDB
* Git

---

## Catatan

Perintah `php artisan app:import-sql` merupakan custom command yang disediakan project ini untuk mempermudah proses setup database. Dengan satu perintah, database akan di-reset, data SQL akan diimpor, dan seeder akan dijalankan secara otomatis sehingga aplikasi siap digunakan.
