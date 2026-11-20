<?php
class Facility extends Model
{
    public static function all(): array
    {
        return self::load('facilities.json');
    }

    public static function lastUpdatedAt(): int
    {
        $timestamp = filemtime(BASE_PATH . '/data/facilities.json');
        if ($timestamp === false) {
            throw new RuntimeException('Tidak dapat membaca waktu pembaruan data fasilitas.');
        }

        return $timestamp;
    }

    public static function categories(): array
    {
        return [
            'keamanan'     => ['label' => 'Keamanan',     'color' => '#ef4444', 'icon' => '🛡️'],
            'ibadah'       => ['label' => 'Ibadah',       'color' => '#059669', 'icon' => '🕌'],
            'pemerintahan' => ['label' => 'Pemerintahan', 'color' => '#2563eb', 'icon' => '🏛️'],
            'kesehatan'    => ['label' => 'Kesehatan',    'color' => '#db2777', 'icon' => '➕'],
            'umum'         => ['label' => 'Fasilitas Umum','color' => '#d97706', 'icon' => '⚽'],
        ];
    }
}
