<?php
// Helpers: PHPMailer-based send_mail using SMTP (SendGrid example)
// Requires: composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Attempt to load composer autoload (optional)
@include_once __DIR__ . '/../vendor/autoload.php';

// Last mail error for debugging. Use only in development.
global $LAST_MAIL_ERROR;
$LAST_MAIL_ERROR = null;

function send_mail($to, $subject, $body)
{
	$config = require __DIR__ . '/../config/config.php';
	$from = $config['mail']['from'] ?? 'no-reply@example.com';
	$fromName = $config['mail']['from_name'] ?? 'No Reply';
	$smtp = $config['mail']['smtp'] ?? null;

	// If PHPMailer is not installed, fail fast with a helpful log
	if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
		$msg = 'Mail error: PHPMailer not installed. Run "composer require phpmailer/phpmailer"';
		error_log($msg);
		$GLOBALS['LAST_MAIL_ERROR'] = $msg;
		return false;
	}

	$mail = new PHPMailer(true);
	try {
		$env = getenv('APP_ENV') ?: 'production';
		$debugOutput = '';
		if ($env === 'development') {
			// capture debug output from PHPMailer
			$mail->SMTPDebug = 2; // verbose
			$mail->Debugoutput = function ($str, $level) use (&$debugOutput) {
				$debugOutput .= "[debug:$level] " . trim($str) . "\n";
			};
		}
		if ($smtp) {
			$mail->isSMTP();
			$mail->Host = $smtp['host'];
			$mail->SMTPAuth = $smtp['auth'] ?? true;
			$mail->Username = $smtp['username'];
			$mail->Password = $smtp['password'];
			$mail->SMTPSecure = $smtp['encryption'] ?? 'tls';
			$mail->Port = $smtp['port'] ?? 587;
		}

		$mail->setFrom($from, $fromName);
		$mail->addAddress($to);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody = strip_tags($body);
		$mail->send();
		return true;
	} catch (\PHPMailer\PHPMailer\Exception $e) {
		$msg = '';
		// Prefer captured debug output in development
		if (!empty($debugOutput)) {
			$msg = "Mail debug output:\n" . $debugOutput;
		} else {
			$msg = 'Mail error: ' . ($mail->ErrorInfo ?? $e->getMessage());
		}
		error_log($msg);
		// Expose last error for debugging (only stored, not printed here)
		$GLOBALS['LAST_MAIL_ERROR'] = $msg;
		return false;
	}
}

function generate_verification_code()
{
	return str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
}

