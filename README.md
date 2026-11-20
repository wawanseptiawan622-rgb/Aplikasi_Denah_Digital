# Denah Digital Desa Tapos
Pemetaan lokasi dan fasilitas Desa Tapos dan sekitarnya, Kec. Tigaraksa, Kabupaten Tangerang.

## Menjalankan (PHP 8.0+)
    cd denah-digital
    php -S 0.0.0.0:8000 -t public
Buka http://localhost:8000

Agar QR Code bisa dipindai HP lain dalam satu WiFi, buka lewat IP komputer
(mis. http://192.168.1.10:8000) - isi QR otomatis mengikuti URL yang dipakai.
Untuk dicetak di mading, deploy dulu ke hosting/domain agar URL permanen.

Street View dapat dibuka langsung dari detail penanda pada peta. Panorama dimuat
dari Google Maps; koneksi internet dan ketersediaan citra di sekitar lokasi diperlukan.

Asisten Lokasi dapat mencari fasilitas terdekat dari posisi perangkat dan
mengurutkannya berdasarkan jarak garis lurus. Fitur ini meminta izin lokasi
browser, yang umumnya hanya tersedia melalui HTTPS atau localhost. Pilih
"Navigasi Google Maps" untuk melihat rute perjalanan; jarak jalan dan waktu
tempuh aktual dihitung oleh Google Maps, bukan oleh website.

## Deploy ke Vercel
Proyek ini sudah disiapkan untuk Vercel (runtime komunitas `vercel-php`).

    npm i -g vercel
    cd denah-digital
    vercel --prod

Atau push ke GitHub lalu **Add New > Project** di vercel.com (Framework Preset: Other,
kosongkan Build Command). Setelah online, isi `base_url` di config/config.php
dengan domain Vercel agar QR Code permanen.

File terkait: `vercel.json`, `api/index.php` (entry Vercel), `core/bootstrap.php` (logika bersama).

## Menyesuaikan
- Titik tengah peta : config/config.php  (bantuan: buka /?pick=1 lalu klik peta)
- Fasilitas/marker  : data/facilities.json
- Kategori & ikon   : app/models/Facility.php

## Struktur
    public/      index.php, assets/, .htaccess
    api/         index.php (entry point Vercel)
    core/        bootstrap, Router, Controller, Model
    app/         controllers, models, views
    config/      config.php
    data/        facilities.json


## by TEGAR FIKRI MAULANA

