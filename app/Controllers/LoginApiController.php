<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;

class LoginApiController extends ApiController
{
    /**
     * POST /api/login - API login endpoint
     */
    public function login()
    {
        try {
            if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
                session_start();
            }

            $data = $this->getJsonInput();
            
            if (!$data) {
                $this->sendError('Invalid JSON data', 400);
            }

            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (!$email || !$password) {
                $this->sendError('Email and password are required', 400);
            }

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT * FROM accounts WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $account = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$account) {
                $this->sendError('Account not found', 404);
            }

            // Check password (support both MD5 and bcrypt)
            $isValidPassword = false;
            
            // Try bcrypt first
            if (password_verify($password, $account['password'])) {
                $isValidPassword = true;
            }
            // Fallback to MD5 for legacy accounts
            elseif (md5($password) === $account['password']) {
                $isValidPassword = true;
            }
            
            if (!$isValidPassword) {
                $this->sendError('Invalid password', 401);
            }

            // Check status
            if (($account['status'] ?? 'active') !== 'active') {
                $this->sendError('Account is not active', 403);
            }

            // Set session
            $_SESSION['account_id'] = $account['id'];
            $_SESSION['email'] = $account['email'];
            $_SESSION['role'] = $account['role'] ?? 'user';

            $this->sendResponse([
                'account_id' => $account['id'],
                'email' => $account['email'],
                'role' => $account['role'] ?? 'user'
            ], 'Login successful');

        } catch (\Exception $e) {
            $this->sendError('Login failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/logout - API logout endpoint
     */
    public function logout()
    {
        try {
            if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
                session_start();
            }

            // Clear session
            session_destroy();
            $_SESSION = [];

            $this->sendResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            $this->sendError('Logout failed: ' . $e->getMessage(), 500);
        }
    }
}
