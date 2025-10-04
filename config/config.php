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

return [
    'database' => [
        'host' => getenv('DB_HOST') ?: '159.65.2.46',
        'db_name' => getenv('DB_NAME') ?: 'hasaki',
        'username' => getenv('DB_USER') ?: 'kaiser',
        'password' => getenv('DB_PASS') ?: 'r!8R%OMm@=H{cVH6LZpqV]nye1G',
    ],
    'mail' => [
        // cấu hình email cửa hàng: thay đổi theo môi trường của bạn
        'from' => getenv('MAIL_FROM') ?: 'no-reply@xuanhiep.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Cửa Hàng Mỹ Phẩm Xuân Hiệp',
        // SMTP settings (can be provided via .env)
        'smtp' => [
            'host' => getenv('MAIL_SMTP_HOST') ?: 'smtp.gmail.com',
            'port' => (int) (getenv('MAIL_SMTP_PORT') ?: 587),
            'username' => trim((string) (getenv('MAIL_SMTP_USER') ?: 'your.email@gmail.com')),
            'password' => trim((string) (getenv('MAIL_SMTP_PASS') ?: '')),
            'encryption' => getenv('MAIL_SMTP_ENCRYPTION') ?: 'tls',
            'auth' => filter_var(getenv('MAIL_SMTP_AUTH') ?: '1', FILTER_VALIDATE_BOOLEAN),
        ]
    ],
];
