<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';

use App\Core\Database;
use Exception;

class AuthController extends ApiController
{
    private $jwtSecret = 'your-secret-key-change-this-in-production';

    /**
     * POST /api/auth/login - Đăng nhập
     */
    public function login()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['email']) || !isset($input['password'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Email và mật khẩu là bắt buộc'
                ]);
                return;
            }

            $db = (new Database())->getConnection();

            // Tìm user với email
            $sql = "SELECT a.*, u.full_name 
                    FROM accounts a 
                    LEFT JOIN users u ON a.id = u.account_id 
                    WHERE a.email = :email";
            $stmt = $db->prepare($sql);
            $stmt->execute([':email' => $input['email']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ]);
                return;
            }

            // Kiểm tra mật khẩu
            if (md5($input['password']) !== $user['password']) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ]);
                return;
            }

            // Kiểm tra trạng thái tài khoản
            if ($user['status'] !== 'active') {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tài khoản đã bị khóa'
                ]);
                return;
            }

            // Tạo JWT token
            $token = $this->generateJWT([
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'] ?? 'user',
                'full_name' => $user['full_name']
            ]);

            // Cập nhật last_login
            $updateSql = "UPDATE accounts SET last_login = NOW() WHERE id = :id";
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->execute([':id' => $user['id']]);

            echo json_encode([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => (int) $user['id'],
                        'email' => $user['email'],
                        'full_name' => $user['full_name'],
                        'role' => $user['role'] ?? 'user',
                        'account_status' => $user['status']
                    ]
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * POST /api/auth/logout - Đăng xuất
     */
    public function logout()
    {
        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'message' => 'Đăng xuất thành công',
            'data' => null
        ]);
    }

    /**
     * GET /api/auth/me - Lấy thông tin user hiện tại
     */
    public function me()
    {
        header('Content-Type: application/json');

        try {
            $token = $this->getBearerToken();

            if (!$token) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Token không được cung cấp'
                ]);
                return;
            }

            $payload = $this->validateJWT($token);

            if (!$payload) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Token không hợp lệ'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Lấy thông tin user thành công',
                'data' => [
                    'id' => $payload['id'],
                    'email' => $payload['email'],
                    'full_name' => $payload['full_name'],
                    'role' => $payload['role'],
                    'account_status' => 'active'
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Tạo JWT token
     */
    private function generateJWT($payload)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['iat'] = time();
        $payload['exp'] = time() + (24 * 60 * 60); // 24 hours
        $payload = json_encode($payload);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $this->jwtSecret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    /**
     * Validate JWT token
     */
    private function validateJWT($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        list($base64Header, $base64Payload, $base64Signature) = $parts;

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $this->jwtSecret, true);
        $base64SignatureCheck = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if ($base64Signature !== $base64SignatureCheck) {
            return false;
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64Payload)), true);

        if (!$payload || $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Lấy Bearer token từ header
     */
    private function getBearerToken()
    {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
