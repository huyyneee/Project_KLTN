<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\AccountModel;

class EmployeeApiController extends Controller
{
    private $userModel;
    private $accountModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->accountModel = new AccountModel();
    }

    /**
     * GET /api/employees - Lấy danh sách nhân viên
     */
    public function index()
    {
        header('Content-Type: application/json');

        try {
            $db = (new \App\Core\Database())->getConnection();

            // Lấy danh sách users với thông tin account
            $sql = "SELECT u.*, a.email, a.role, a.status as account_status, a.created_at as account_created 
                    FROM users u 
                    LEFT JOIN accounts a ON u.account_id = a.id
                    WHERE a.role = 'employee'
                    ORDER BY u.created_at DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $employees,
                'message' => 'Danh sách nhân viên'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách nhân viên: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * GET /api/employees/{id} - Lấy thông tin nhân viên
     */
    public function show($id)
    {
        header('Content-Type: application/json');

        try {
            $db = (new \App\Core\Database())->getConnection();

            $sql = "SELECT u.*, a.email, a.role, a.status as account_status, a.created_at as account_created 
                    FROM users u 
                    LEFT JOIN accounts a ON u.account_id = a.id 
                    WHERE u.id = :id";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $employee = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$employee) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy nhân viên'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $employee,
                'message' => 'Thông tin nhân viên'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin nhân viên: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * POST /api/employees - Tạo nhân viên mới
     */
    public function store()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            // Validate input
            if (empty($input['email']) || empty($input['password']) || empty($input['full_name'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Email, password và họ tên là bắt buộc'
                ]);
                return;
            }

            $db = (new \App\Core\Database())->getConnection();
            $db->beginTransaction();

            // Tạo account trước
            $accountData = [
                'email' => $input['email'],
                'password' => md5($input['password']), // Sử dụng md5 như hệ thống hiện tại
                'role' => $input['role'] ?? 'employee',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $accountSql = "INSERT INTO accounts (email, password, role, status, created_at) VALUES (:email, :password, :role, :status, :created_at)";
            $accountStmt = $db->prepare($accountSql);
            $accountStmt->execute($accountData);
            $accountId = $db->lastInsertId();

            // Tạo user profile
            $userData = [
                'account_id' => $accountId,
                'full_name' => $input['full_name'],
                'phone' => $input['phone'] ?? '',
                'address' => $input['address'] ?? '',
                'birthday' => $input['birthday'] ?? null,
                'gender' => $input['gender'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userSql = "INSERT INTO users (account_id, full_name, phone, address, birthday, gender, created_at, updated_at) 
                        VALUES (:account_id, :full_name, :phone, :address, :birthday, :gender, :created_at, :updated_at)";
            $userStmt = $db->prepare($userSql);
            $userStmt->execute($userData);
            $userId = $db->lastInsertId();

            $db->commit();

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $userId,
                    'account_id' => $accountId,
                    'email' => $input['email'],
                    'full_name' => $input['full_name']
                ],
                'message' => 'Tạo nhân viên thành công'
            ]);

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollback();
            }
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi tạo nhân viên: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * PUT /api/employees/{id} - Cập nhật nhân viên
     */
    public function update($id)
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $db = (new \App\Core\Database())->getConnection();

            // Kiểm tra user có tồn tại không
            $checkSql = "SELECT u.*, a.email FROM users u LEFT JOIN accounts a ON u.account_id = a.id WHERE u.id = :id";
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            $existingUser = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$existingUser) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy nhân viên'
                ]);
                return;
            }

            $db->beginTransaction();

            // Cập nhật thông tin user
            $userData = [
                'full_name' => $input['full_name'] ?? $existingUser['full_name'],
                'phone' => $input['phone'] ?? $existingUser['phone'],
                'address' => $input['address'] ?? $existingUser['address'],
                'birthday' => $input['birthday'] ?? $existingUser['birthday'],
                'gender' => $input['gender'] ?? $existingUser['gender'],
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id
            ];

            $userSql = "UPDATE users SET full_name = :full_name, phone = :phone, address = :address, 
                        birthday = :birthday, gender = :gender, updated_at = :updated_at WHERE id = :id";
            $userStmt = $db->prepare($userSql);
            $userStmt->execute($userData);

            // Cập nhật email nếu có
            if (!empty($input['email']) && $input['email'] !== $existingUser['email']) {
                $accountSql = "UPDATE accounts SET email = :email WHERE id = :account_id";
                $accountStmt = $db->prepare($accountSql);
                $accountStmt->execute([
                    'email' => $input['email'],
                    'account_id' => $existingUser['account_id']
                ]);
            }

            // Cập nhật password nếu có
            if (!empty($input['password'])) {
                $passwordSql = "UPDATE accounts SET password = :password WHERE id = :account_id";
                $passwordStmt = $db->prepare($passwordSql);
                $passwordStmt->execute([
                    'password' => md5($input['password']),
                    'account_id' => $existingUser['account_id']
                ]);
            }

            // Cập nhật role nếu có
            if (!empty($input['role'])) {
                $roleSql = "UPDATE accounts SET role = :role WHERE id = :account_id";
                $roleStmt = $db->prepare($roleSql);
                $roleStmt->execute([
                    'role' => $input['role'],
                    'account_id' => $existingUser['account_id']
                ]);
            }

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật nhân viên thành công'
            ]);

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollback();
            }
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi cập nhật nhân viên: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * DELETE /api/employees/{id} - Xóa nhân viên
     */
    public function delete($id)
    {
        header('Content-Type: application/json');

        try {
            $db = (new \App\Core\Database())->getConnection();

            // Kiểm tra user có tồn tại không
            $checkSql = "SELECT account_id FROM users WHERE id = :id";
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            $user = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy nhân viên'
                ]);
                return;
            }

            $db->beginTransaction();

            // Xóa user
            $deleteUserSql = "DELETE FROM users WHERE id = :id";
            $deleteUserStmt = $db->prepare($deleteUserSql);
            $deleteUserStmt->execute([':id' => $id]);

            // Xóa account
            $deleteAccountSql = "DELETE FROM accounts WHERE id = :account_id";
            $deleteAccountStmt = $db->prepare($deleteAccountSql);
            $deleteAccountStmt->execute([':account_id' => $user['account_id']]);

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Xóa nhân viên thành công'
            ]);

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollback();
            }
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi xóa nhân viên: ' . $e->getMessage()
            ]);
        }
    }
}
