<?php
/**
 * Konfigurasi aplikasi.
 * Ubah koordinat di sini setelah Anda mengambil titik yang akurat
 * (tips: buka /?pick=1 lalu klik titik di peta untuk melihat koordinatnya).
 */
return [
    'app_name'  => 'Denah Digital Desa Tapos',
    'village'   => 'Desa Tapos',
    'district'  => 'Kec. Tigaraksa, Kabupaten Tangerang',

    /**
     * URL publik website (dipakai untuk isi QR Code).
     * Kosongkan ('') = otomatis mengikuti alamat yang sedang dibuka.
     * Isi saat website sudah online, contoh: 'https://denah.desatapos.id/'
     * atau saat tes di HP satu WiFi, contoh: 'http://192.168.1.10/denah-digital/'
     */
    'base_url'  => '',

    // Pusat peta wilayah dan lokasi fasilitas yang terdata di Desa Tapos.
    'map' => [
        'lat'      => -6.29217,
        'lng'      => 106.47741,
        'zoom'     => 15,
        'min_zoom' => 14,
        'max_zoom' => 19,
    ],

    'credit' => 'Kontribusi KKN Kelompok 28 untuk Desa Tapos',
];
