<?php
/**
 * Entry point untuk Vercel (runtime vercel-php).
 * Aplikasi berjalan di root domain, jadi base path-nya kosong.
 */
define('APP_BASE_PATH', '');

require dirname(__DIR__) . '/core/bootstrap.php';
