<?php
/**
 * Model dasar: membaca data JSON dari folder /data.
 * (Mudah diganti ke MySQL/PDO di kemudian hari.)
 */
abstract class Model
{
    protected static function load(string $file): array
    {
        $path = BASE_PATH . '/data/' . $file;
        if (!is_file($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?: [];
    }
}
