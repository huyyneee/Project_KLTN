<?php
if (!function_exists('config')) {
    function config($key = null) {
        static $config = null;
        if ($config === null) {
            $config = include __DIR__ . '/../config/config.php';
        }
        if ($key === null) return $config;
        $keys = explode('.', $key);
        $value = $config;
        foreach ($keys as $k) {
            if (!isset($value[$k])) return null;
            $value = $value[$k];
        }
        return $value;
    }
}
