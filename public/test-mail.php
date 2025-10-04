<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Helpers.php';

// Quick interactive test page for sending mail via configured SMTP (Gmail example)
// Usage: open http://localhost:8000/test-mail.php in your browser after starting
// the built-in server from the project root: php -S localhost:8000 -t public public/router.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = filter_var($_POST['to'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = $_POST['subject'] ?? 'Test mail';
    $body = $_POST['body'] ?? '<b>Test mail</b>';

    if (!$to) {
        $result = ['ok' => false, 'message' => 'Invalid to address'];
    } else {
        $sent = send_mail($to, $subject, $body);
            if ($sent) {
                $result = ['ok' => true, 'message' => 'Mail sent (true)'];
            } else {
                $result = ['ok' => false, 'message' => 'Mail failed to send. Check logs and SMTP settings.'];
                $env = getenv('APP_ENV') ?: 'production';
                if ($env === 'development') {
                    $lastErr = $GLOBALS['LAST_MAIL_ERROR'] ?? null;
                    if ($lastErr) {
                        $result['debug'] = $lastErr;
                    }
                }
            }
    }

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Mail</title>
</head>
<body>
    <h1>Send test mail</h1>
    <form method="post">
        <label>To: <input type="email" name="to" required value=""></label><br><br>
        <label>Subject: <input type="text" name="subject" value="Test mail from app"></label><br><br>
        <label>Body:<br>
            <textarea name="body" rows="6" cols="60">&lt;b&gt;Hello from Project_KLTN&lt;/b&gt;</textarea>
        </label><br><br>
        <button type="submit">Send</button>
    </form>
    <p>Note: Make sure PHPMailer is installed (composer require phpmailer/phpmailer) and
    `config/config.php` SMTP credentials are configured for Gmail (email + app password).</p>
</body>
</html>