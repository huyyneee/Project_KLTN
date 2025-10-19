<?php
// prints SMTP config and getenv values to help debug env loading
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';
$smtp = $config['mail']['smtp'] ?? [];
echo "CONFIG mail.smtp.username: " . ($smtp['username'] ?? '<missing>') . PHP_EOL;
echo "CONFIG mail.smtp.password: " . (isset($smtp['password']) && $smtp['password'] !== '' ? '[SET]' : '<empty>') . PHP_EOL;
echo "getenv MAIL_SMTP_USER: " . (getenv('MAIL_SMTP_USER') ?: '<not set>') . PHP_EOL;
echo "getenv MAIL_SMTP_PASS: " . (getenv('MAIL_SMTP_PASS') ?: '<not set>') . PHP_EOL;
echo "APP_ENV: " . (getenv('APP_ENV') ?: '<not set>') . PHP_EOL;
