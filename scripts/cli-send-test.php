<?php
// CLI helper to test send_mail() and print debug output.
// Usage: php scripts/cli-send-test.php recipient@example.com

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Helpers.php';

$to = $argv[1] ?? null;
if (!$to) {
    // try to use configured SMTP user as recipient
    $config = require __DIR__ . '/../config/config.php';
    $to = $config['mail']['smtp']['username'] ?? null;
}
if (!$to) {
    echo "Usage: php scripts/cli-send-test.php recipient@example.com\n";
    exit(1);
}

$subject = 'CLI Test Mail from Project_KLTN';
$body = '<b>CLI test mail</b>\nIf you see this, SMTP works.';

$result = send_mail($to, $subject, $body);
$output = ['ok' => (bool) $result];
if ($result) {
    $output['message'] = 'Mail sent';
} else {
    $output['message'] = 'Mail failed';
    $output['debug'] = $GLOBALS['LAST_MAIL_ERROR'] ?? null;
}

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

if (!$result) {
    echo "\nIf auth failed, check that MAIL_SMTP_USER and MAIL_SMTP_PASS (App Password) are correct in your .env.\n";
}
