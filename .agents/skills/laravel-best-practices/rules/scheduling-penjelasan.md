# Penjelasan Task Scheduling

## Gunakan `withoutOverlapping()` untuk Tugas dengan Durasi Variabel
Tanpa pengaturan ini, tugas yang berjalan lama dapat memicu instansi kedua pada interval berikutnya, yang menyebabkan pemrosesan ganda atau kehabisan sumber daya.

## Gunakan `onOneServer()` pada Deploy Multi-Server
Tanpa ini, setiap server akan menjalankan tugas yang sama secara bersamaan. Pengaturan ini membutuhkan driver cache bersama seperti Redis, database, atau Memcached.

## Gunakan `runInBackground()` untuk Tugas Panjang yang Berjalan Bersamaan
Secara default, tugas pada tick yang sama dijalankan secara berurutan. Tugas panjang pertama akan menunda tugas berikutnya. `runInBackground()` menjalankannya sebagai proses terpisah.

## Gunakan `environments()` untuk Membatasi Eksekusi Tugas
Gunakan ini untuk mencegah tugas yang hanya boleh berjalan di produksi (misalnya billing atau pelaporan) dijalankan secara tidak sengaja pada staging.

```php
Schedule::command('billing:charge')->monthly()->environments(['production']);
```

## Gunakan `takeUntilTimeout()` untuk Pemrosesan dengan Batas Waktu
Tugas yang dijalankan setiap 15 menit dan memproses cursor tanpa batas bisa tumpang tindih dengan run berikutnya. Batasi waktu eksekusi.

## Gunakan Schedule Groups untuk Konfigurasi Bersama
Hindari mengulang `->onOneServer()->timezone('America/New_York')` di banyak tugas dengan mengelompokkan tugas bersama.

```php
Schedule::daily()
    ->onOneServer()
    ->timezone('America/New_York')
    ->group(function () {
        Schedule::command('emails:send --force');
        Schedule::command('emails:prune');
    });
```
