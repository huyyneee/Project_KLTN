<?php
// Helpers: Resend API-based send_mail
// Requires: composer require resend/resend-php

use Resend\Resend;

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
	$resendApiKey = $config['mail']['resend_api_key'] ?? null;

	// Check if Resend is available
	if (!class_exists(\Resend\Resend::class)) {
		$msg = 'Mail error: Resend PHP SDK not installed. Run "composer require resend/resend-php"';
		error_log($msg);
		$GLOBALS['LAST_MAIL_ERROR'] = $msg;
		return false;
	}

	if (empty($resendApiKey)) {
		$msg = 'Mail error: Resend API key not configured';
		error_log($msg);
		$GLOBALS['LAST_MAIL_ERROR'] = $msg;
		return false;
	}

	try {
		// Ensure strings are UTF-8
		$ensure_utf8 = function($s) {
			if ($s === null) return $s;
			if (mb_detect_encoding($s, 'UTF-8', true) === false) {
				return mb_convert_encoding($s, 'UTF-8', 'auto');
			}
			return $s;
		};
		$subject = $ensure_utf8($subject);
		$fromName = $ensure_utf8($fromName);
		$body = $ensure_utf8($body);

		// Initialize Resend client
		$resend = Resend::client($resendApiKey);

		// Send email via Resend API
		$result = $resend->emails->send([
			'from' => $fromName . ' <' . $from . '>',
			'to' => [$to],
			'subject' => $subject,
			'html' => $body,
		]);

		// Resend returns an object with 'id' on success
		// Check both object property and array key for compatibility
		$hasId = false;
		if (is_object($result)) {
			$hasId = isset($result->id) || property_exists($result, 'id');
		} elseif (is_array($result)) {
			$hasId = isset($result['id']);
		}

		if ($hasId) {
			return true;
		}

		$msg = 'Mail error: Resend API returned unexpected response';
		error_log($msg . ' - Response: ' . json_encode($result));
		$GLOBALS['LAST_MAIL_ERROR'] = $msg;
		return false;
	} catch (\Exception $e) {
		$msg = 'Mail error: ' . $e->getMessage();
		error_log($msg);
		$GLOBALS['LAST_MAIL_ERROR'] = $msg;
		return false;
	}
}

function generate_verification_code()
{
	return str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
}

/**
 * Get absolute URL for an image path
 * Converts relative paths like /uploads/... to full HTTPS URLs
 * 
 * @param string $path Relative or absolute image path
 * @return string Full URL
 */
function get_image_url($path)
{
	if (empty($path)) {
		return null;
	}

	// Normalize path
	$path = trim($path);
	$path = str_replace('\\/', '/', $path);
	$path = trim($path, "'\" \t\n\r\0\x0B");

	// If already absolute URL, return as-is
	if (preg_match('#^https?://#i', $path)) {
		return $path;
	}

	// Get base URL from config
	$config = require __DIR__ . '/../config/config.php';
	$baseUrl = $config['app']['base_url'] ?? 'https://xuanhiepbeauty.id.vn';
	
	// Ensure base URL doesn't end with slash
	$baseUrl = rtrim($baseUrl, '/');
	
	// Ensure path starts with /
	if (strpos($path, '/') !== 0) {
		$path = '/' . $path;
	}

	return $baseUrl . $path;
}

