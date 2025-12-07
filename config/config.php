<?php
// config.php — reads configuration from environment variables when available.
// If vlucas/phpdotenv is installed (composer require vlucas/phpdotenv), this file
// will attempt to load a local .env automatically. Otherwise it falls back to
// the defaults below.

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
    if (class_exists(\Dotenv\Dotenv::class)) {
        try {
            \Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
        } catch (Exception $e) {
            // ignore failure to load .env
        }
    }
}

// helper to read environment which falls back to $_ENV and $_SERVER when getenv() is empty
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $val = @getenv($key);
        if ($val === false || $val === null || $val === '') {
            if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
                $val = $_ENV[$key];
            } elseif (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
                $val = $_SERVER[$key];
            } else {
                $val = $default;
            }
        }
        // normalize strings: remove CR, trim whitespace/newlines
        if (is_string($val)) {
            $val = str_replace("\r", "", $val);
            $val = trim($val);
        }
        return $val;
    }
}

return [
    'database' => [
        'host' => env('DB_HOST') ?: '159.65.2.46',
        'db_name' => env('DB_NAME') ?: 'hasaki',
        'username' => env('DB_USER') ?: 'kaiser',
        'password' => env('DB_PASS') ?: 'r!8R%OMm@=H{cVH6LZpqV]nye1G',
    ],
    'app' => [
        // Base URL for generating absolute URLs (images, assets, etc.)
        'base_url' => env('APP_BASE_URL') ?: 'https://xuanhiepbeauty.id.vn',
    ],
    'mail' => [
        // cấu hình email cửa hàng: thay đổi theo môi trường của bạn
        'from' => env('MAIL_FROM') ?: 'no-reply@xuanhiepbeauty.id.vn',
        'from_name' => env('MAIL_FROM_NAME') ?: 'Cửa Hàng Mỹ Phẩm Xuân Hiệp',
        // Resend API settings
        'resend_api_key' => env('RESEND_API_KEY') ?: 're_PKJijqiw_2J8JZL1QTQibNXY7sVfbRzPd',
        // SMTP settings (fallback, can be provided via .env)
        'smtp' => [
            'host' => env('MAIL_SMTP_HOST') ?: 'smtp.gmail.com',
            'port' => (int) (env('MAIL_SMTP_PORT') ?: 587),
            'username' => trim((string) (env('MAIL_SMTP_USER') ?: 'your.email@gmail.com')),
            'password' => trim((string) (env('MAIL_SMTP_PASS') ?: '')),
            'encryption' => env('MAIL_SMTP_ENCRYPTION') ?: 'tls',
            'auth' => filter_var(env('MAIL_SMTP_AUTH') ?: '1', FILTER_VALIDATE_BOOLEAN),
        ]
    ],
];
