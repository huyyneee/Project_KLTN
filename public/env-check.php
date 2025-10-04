<?php
// Quick env check for web process. Prints SMTP config loaded by config/config.php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';
if (!isset($config['mail']['smtp'])) {
    echo "No mail.smtp config found.\n";
    exit;
}
$smtp = $config['mail']['smtp'];
echo "MAIL_SMTP_USER=" . ($smtp['username'] ?? '<missing>') . "\n";
echo "MAIL_SMTP_PASS=" . (isset($smtp['password']) && $smtp['password'] !== '' ? '[SET]' : '<empty>') . "\n";
echo "MAIL_SMTP_HOST=" . ($smtp['host'] ?? '<missing>') . "\n";
echo "MAIL_SMTP_PORT=" . ($smtp['port'] ?? '<missing>') . "\n";
echo "APP_ENV=" . (getenv('APP_ENV') ?: '<not set>') . "\n";

// show raw getenv too
echo "\nRaw getenv MAIL_SMTP_PASS: " . (getenv('MAIL_SMTP_PASS') !== false ? '[SET]' : '<not set>') . "\n";
