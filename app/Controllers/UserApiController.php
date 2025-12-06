<?php
namespace App\Controllers;

require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Core/Database.php';

use App\Models\UserModel;
use App\Core\Database;

class UserApiController extends ApiController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Lấy danh sách tất cả khách hàng
     * GET /api/users
     */
    public function index()
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Lấy danh sách users với địa chỉ mặc định - chỉ lấy khách hàng (role = 'user')
            $stmt = $db->prepare("
                SELECT 
                    u.id,
                    u.account_id,
                    u.full_name,
                    u.birthday,
                    u.gender,
                    u.avatar,
                    u.created_at,
                    u.updated_at,
                    a.email,
                    a.status as account_status,
                    addr.phone as default_address_phone,
                    CONCAT(
                        COALESCE(addr.street, ''), 
                        ', ',
                        COALESCE(addr.ward, ''), 
                        ', ',
                        COALESCE(addr.district, ''), 
                        ', ',
                        COALESCE(addr.city, ''), 
                        ', ',
                        COALESCE(addr.province, '')
                    ) as default_address
                FROM users u
                LEFT JOIN accounts a ON u.account_id = a.id
                LEFT JOIN addresses addr ON u.id = addr.user_id AND addr.is_default = 1
                WHERE a.role = 'user' OR a.role IS NULL
                ORDER BY u.created_at DESC
            ");
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format dữ liệu để trả về
            $formattedUsers = array_map(function ($user) {
                return [
                    'id' => (int) $user['id'],
                    'account_id' => (int) $user['account_id'],
                    'name' => $user['full_name'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'] ?? '',
                    'phone' => $user['default_address_phone'] ?? $user['phone'] ?? '',
                    'address' => !empty($user['default_address']) ? trim($user['default_address'], ', ') : ($user['address'] ?? ''),
                    'default_address_phone' => $user['default_address_phone'] ?? null,
                    'default_address' => !empty($user['default_address']) ? trim($user['default_address'], ', ') : null,
                    'status' => $user['account_status'] ?? 'active',
                    'account_status' => $user['account_status'] ?? 'active',
                    'birthday' => $user['birthday'],
                    'gender' => $user['gender'],
                    'created_at' => $user['created_at'],
                    'updated_at' => $user['updated_at']
                ];
            }, $users);

            $this->sendResponse($formattedUsers, 'Danh sách khách hàng đã được tải thành công');
        } catch (\Exception $e) {
            $this->sendError('Lỗi khi tải danh sách khách hàng: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lấy thông tin chi tiết một khách hàng
     * GET /api/users/{id}
     */
    public function show($id)
    {
        try {
            $user = $this->userModel->find($id);

            if (!$user) {
                $this->sendError('Không tìm thấy khách hàng với ID: ' . $id, 404);
                return;
            }

            // Lấy thông tin account và địa chỉ mặc định
            $db = Database::getInstance()->getConnection();

            // Lấy status từ account
            $accountStmt = $db->prepare("SELECT status FROM accounts WHERE id = :account_id LIMIT 1");
            $accountStmt->execute([':account_id' => $user['account_id']]);
            $account = $accountStmt->fetch(\PDO::FETCH_ASSOC);

            // Lấy địa chỉ mặc định
            $addrStmt = $db->prepare("
                SELECT 
                    phone as default_address_phone,
                    CONCAT(
                        COALESCE(street, ''), 
                        ', ',
                        COALESCE(ward, ''), 
                        ', ',
                        COALESCE(district, ''), 
                        ', ',
                        COALESCE(city, ''), 
                        ', ',
                        COALESCE(province, '')
                    ) as default_address
                FROM addresses
                WHERE user_id = :user_id AND is_default = 1
                LIMIT 1
            ");
            $addrStmt->execute([':user_id' => $id]);
            $defaultAddress = $addrStmt->fetch(\PDO::FETCH_ASSOC);

            // Format dữ liệu để trả về
            $formattedUser = [
                'id' => (int) $user['id'],
                'account_id' => (int) $user['account_id'],
                'name' => $user['full_name'],
                'full_name' => $user['full_name'],
                'phone' => $defaultAddress['default_address_phone'] ?? $user['phone'] ?? '',
                'address' => !empty($defaultAddress['default_address']) ? trim($defaultAddress['default_address'], ', ') : ($user['address'] ?? ''),
                'default_address_phone' => $defaultAddress['default_address_phone'] ?? null,
                'default_address' => !empty($defaultAddress['default_address']) ? trim($defaultAddress['default_address'], ', ') : null,
                'status' => $account['status'] ?? 'active',
                'account_status' => $account['status'] ?? 'active',
                'birthday' => $user['birthday'],
                'gender' => $user['gender'],
                'avatar' => $user['avatar'] ?? null,
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at']
            ];

            $this->sendResponse($formattedUser, 'Thông tin khách hàng đã được tải thành công');
        } catch (\Exception $e) {
            $this->sendError('Lỗi khi tải thông tin khách hàng: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Tìm kiếm khách hàng theo tên hoặc số điện thoại
     * GET /api/users/search?q={query}
     */
    public function search()
    {
        try {
            $query = $_GET['q'] ?? '';

            if (empty($query)) {
                $this->sendError('Vui lòng nhập từ khóa tìm kiếm', 400);
                return;
            }

            // Sử dụng SQL LIKE để tìm kiếm với địa chỉ mặc định
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT 
                    u.id,
                    u.account_id,
                    u.full_name,
                    u.birthday,
                    u.gender,
                    u.avatar,
                    u.created_at,
                    u.updated_at,
                    a.email,
                    a.status as account_status,
                    addr.phone as default_address_phone,
                    CONCAT(
                        COALESCE(addr.street, ''), 
                        ', ',
                        COALESCE(addr.ward, ''), 
                        ', ',
                        COALESCE(addr.district, ''), 
                        ', ',
                        COALESCE(addr.city, ''), 
                        ', ',
                        COALESCE(addr.province, '')
                    ) as default_address
                FROM users u
                LEFT JOIN accounts a ON u.account_id = a.id
                LEFT JOIN addresses addr ON u.id = addr.user_id AND addr.is_default = 1
                WHERE u.full_name LIKE :query 
                OR addr.phone LIKE :query
                ORDER BY u.created_at DESC
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format dữ liệu để trả về
            $formattedUsers = array_map(function ($user) {
                return [
                    'id' => (int) $user['id'],
                    'account_id' => (int) $user['account_id'],
                    'name' => $user['full_name'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'] ?? '',
                    'phone' => $user['default_address_phone'] ?? $user['phone'] ?? '',
                    'address' => !empty($user['default_address']) ? trim($user['default_address'], ', ') : ($user['address'] ?? ''),
                    'default_address_phone' => $user['default_address_phone'] ?? null,
                    'default_address' => !empty($user['default_address']) ? trim($user['default_address'], ', ') : null,
                    'status' => $user['account_status'] ?? 'active',
                    'account_status' => $user['account_status'] ?? 'active',
                    'birthday' => $user['birthday'],
                    'gender' => $user['gender'],
                    'avatar' => $user['avatar'] ?? null,
                    'created_at' => $user['created_at'],
                    'updated_at' => $user['updated_at']
                ];
            }, $users);

            $this->sendResponse($formattedUsers, 'Kết quả tìm kiếm khách hàng');
        } catch (\Exception $e) {
            $this->sendError('Lỗi khi tìm kiếm khách hàng: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lấy danh sách khách hàng với phân trang
     * GET /api/users/paginated?page={page}&limit={limit}
     */
    public function paginated()
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $limit = (int) ($_GET['limit'] ?? 10);

            // Validate parameters
            if ($page < 1)
                $page = 1;
            if ($limit < 1 || $limit > 100)
                $limit = 10;

            $offset = ($page - 1) * $limit;

            $db = Database::getInstance()->getConnection();

            // Đếm tổng số bản ghi - chỉ đếm khách hàng (role = 'user')
            $countStmt = $db->query("
                SELECT COUNT(*) as total 
                FROM users u
                LEFT JOIN accounts a ON u.account_id = a.id
                WHERE a.role = 'user' OR a.role IS NULL
            ");
            $totalRecords = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Lấy dữ liệu với phân trang và địa chỉ mặc định - chỉ lấy khách hàng (role = 'user')
            $stmt = $db->prepare("
                SELECT 
                    u.id,
                    u.account_id,
                    u.full_name,
                    u.birthday,
                    u.gender,
                    u.avatar,
                    u.created_at,
                    u.updated_at,
                    a.email,
                    a.status as account_status,
                    addr.phone as default_address_phone,
                    CONCAT(
                        COALESCE(addr.street, ''), 
                        ', ',
                        COALESCE(addr.ward, ''), 
                        ', ',
                        COALESCE(addr.district, ''), 
                        ', ',
                        COALESCE(addr.city, ''), 
                        ', ',
                        COALESCE(addr.province, '')
                    ) as default_address
                FROM users u
                LEFT JOIN accounts a ON u.account_id = a.id
                LEFT JOIN addresses addr ON u.id = addr.user_id AND addr.is_default = 1
                WHERE a.role = 'user' OR a.role IS NULL
                ORDER BY u.created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format dữ liệu để trả về
            $formattedUsers = array_map(function ($user) {
                return [
                    'id' => (int) $user['id'],
                    'account_id' => (int) $user['account_id'],
                    'name' => $user['full_name'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'] ?? '',
                    'phone' => $user['default_address_phone'] ?? $user['phone'] ?? '',
                    'address' => !empty($user['default_address']) ? trim($user['default_address'], ', ') : ($user['address'] ?? ''),
                    'default_address_phone' => $user['default_address_phone'] ?? null,
                    'default_address' => !empty($user['default_address']) ? trim($user['default_address'], ', ') : null,
                    'status' => $user['account_status'] ?? 'active',
                    'account_status' => $user['account_status'] ?? 'active',
                    'birthday' => $user['birthday'],
                    'gender' => $user['gender'],
                    'avatar' => $user['avatar'] ?? null,
                    'created_at' => $user['created_at'],
                    'updated_at' => $user['updated_at']
                ];
            }, $users);

            $totalPages = ceil($totalRecords / $limit);

            $responseData = [
                'users' => $formattedUsers,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_records' => (int) $totalRecords,
                    'limit' => $limit,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ];

            $this->sendResponse($responseData, 'Danh sách khách hàng với phân trang');
        } catch (\Exception $e) {
            $this->sendError('Lỗi khi tải danh sách khách hàng: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/users/{id}/ban - Cấm tài khoản người dùng
     */
    public function ban($id)
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Lấy account_id từ user_id
            $userStmt = $db->prepare("SELECT account_id FROM users WHERE id = :id LIMIT 1");
            $userStmt->execute([':id' => $id]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                $this->sendError('Không tìm thấy người dùng với ID: ' . $id, 404);
                return;
            }

            // Cập nhật status thành 'banned'
            $updateStmt = $db->prepare("UPDATE accounts SET status = 'banned' WHERE id = :account_id");
            $updateStmt->execute([':account_id' => $user['account_id']]);

            $this->sendResponse(null, 'Tài khoản đã bị cấm thành công');
        } catch (\Exception $e) {
            $this->sendError('Lỗi khi cấm tài khoản: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/users/{id}/unban - Bỏ cấm tài khoản người dùng
     */
    public function unban($id)
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Lấy account_id từ user_id
            $userStmt = $db->prepare("SELECT account_id FROM users WHERE id = :id LIMIT 1");
            $userStmt->execute([':id' => $id]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                $this->sendError('Không tìm thấy người dùng với ID: ' . $id, 404);
                return;
            }

            // Cập nhật status thành 'active'
            $updateStmt = $db->prepare("UPDATE accounts SET status = 'active' WHERE id = :account_id");
            $updateStmt->execute([':account_id' => $user['account_id']]);

            $this->sendResponse(null, 'Tài khoản đã được bỏ cấm thành công');
        } catch (\Exception $e) {
            $this->sendError('Lỗi khi bỏ cấm tài khoản: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/users - Tạo khách hàng mới
     */
    public function store()
    {
        try {
            $input = $this->getInput();

            // Validate input
            if (empty($input['full_name'])) {
                $this->sendError('Họ tên là bắt buộc', 400);
                return;
            }

            if (empty($input['email'])) {
                $this->sendError('Email là bắt buộc', 400);
                return;
            }

            // Validate email format
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $this->sendError('Email không hợp lệ', 400);
                return;
            }

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // Kiểm tra email đã tồn tại chưa
            $checkStmt = $db->prepare("SELECT id FROM accounts WHERE email = :email LIMIT 1");
            $checkStmt->execute([':email' => $input['email']]);
            if ($checkStmt->fetch()) {
                $db->rollBack();
                $this->sendError('Email đã tồn tại trong hệ thống', 400);
                return;
            }

            // Hash password với md5 (theo hệ thống hiện tại)
            // Password validation được thực hiện ở client
            $hashedPassword = md5($input['password'] ?? '123456'); // Default password nếu không có

            // Tạo account với role = 'user'
            $accountData = [
                'email' => $input['email'],
                'password' => $hashedPassword,
                'role' => 'user',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $accountSql = "INSERT INTO accounts (email, password, role, status, created_at) VALUES (:email, :password, :role, :status, :created_at)";
            $accountStmt = $db->prepare($accountSql);
            $accountStmt->execute($accountData);
            $accountId = $db->lastInsertId();

            // Xử lý avatar (nếu có) - chỉ lưu URL
            $avatar = null;
            if (!empty($input['avatar'])) {
                // Chỉ lưu URL, không lưu base64
                if (strpos($input['avatar'], 'data:image') === 0) {
                    // Nếu là base64, bỏ qua (nên upload trước)
                    $avatar = null;
                } else {
                    // Nếu là URL, lưu trực tiếp
                    $avatar = $input['avatar'];
                }
            }

            // Tạo user profile
            $now = date('Y-m-d H:i:s');
            $userData = [
                'account_id' => $accountId,
                'full_name' => $input['full_name'],
                'birthday' => !empty($input['birthday']) ? $input['birthday'] : null,
                'gender' => $input['gender'] ?? '',
                'avatar' => $avatar,
                'created_at' => $now,
                'updated_at' => $now
            ];

            $userSql = "INSERT INTO users (account_id, full_name, birthday, gender, avatar, created_at, updated_at) 
                        VALUES (:account_id, :full_name, :birthday, :gender, :avatar, :created_at, :updated_at)";
            $userStmt = $db->prepare($userSql);
            $userStmt->execute($userData);
            $userId = $db->lastInsertId();

            $db->commit();

            $this->sendResponse([
                'id' => (int) $userId,
                'account_id' => (int) $accountId,
                'email' => $input['email'],
                'full_name' => $input['full_name']
            ], 'Tạo khách hàng thành công');

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            $this->sendError('Lỗi khi tạo khách hàng: ' . $e->getMessage(), 500);
        }
    }

    /**
     * PUT /api/users/{id} - Cập nhật khách hàng
     */
    public function update($id)
    {
        try {
            $input = $this->getInput();

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // Kiểm tra user có tồn tại không
            $userStmt = $db->prepare("SELECT account_id FROM users WHERE id = :id LIMIT 1");
            $userStmt->execute([':id' => $id]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                $db->rollBack();
                $this->sendError('Không tìm thấy khách hàng với ID: ' . $id, 404);
                return;
            }

            // Cập nhật user profile
            $now = date('Y-m-d H:i:s');
            $updateFields = [];
            $updateValues = [':id' => $id];

            if (isset($input['full_name'])) {
                $updateFields[] = 'full_name = :full_name';
                $updateValues[':full_name'] = $input['full_name'];
            }

            if (isset($input['birthday'])) {
                $updateFields[] = 'birthday = :birthday';
                $updateValues[':birthday'] = $input['birthday'];
            }

            if (isset($input['gender'])) {
                $updateFields[] = 'gender = :gender';
                $updateValues[':gender'] = $input['gender'];
            }

            if (isset($input['avatar'])) {
                $updateFields[] = 'avatar = :avatar';
                $updateValues[':avatar'] = $input['avatar'];
            }

            $updateFields[] = 'updated_at = :updated_at';
            $updateValues[':updated_at'] = $now;

            if (count($updateFields) > 1) {
                $updateSql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute($updateValues);
            }

            // Cập nhật email nếu có
            if (isset($input['email'])) {
                // Kiểm tra email đã tồn tại chưa (trừ chính account này)
                $checkStmt = $db->prepare("SELECT id FROM accounts WHERE email = :email AND id != :account_id LIMIT 1");
                $checkStmt->execute([
                    ':email' => $input['email'],
                    ':account_id' => $user['account_id']
                ]);
                if ($checkStmt->fetch()) {
                    $db->rollBack();
                    $this->sendError('Email đã tồn tại trong hệ thống', 400);
                    return;
                }

                $emailUpdateSql = "UPDATE accounts SET email = :email WHERE id = :account_id";
                $emailUpdateStmt = $db->prepare($emailUpdateSql);
                $emailUpdateStmt->execute([
                    ':email' => $input['email'],
                    ':account_id' => $user['account_id']
                ]);
            }

            $db->commit();

            $this->sendResponse(null, 'Cập nhật khách hàng thành công');

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            $this->sendError('Lỗi khi cập nhật khách hàng: ' . $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/users/{id} - Xóa khách hàng (soft delete)
     */
    public function destroy($id)
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Lấy account_id từ user_id
            $userStmt = $db->prepare("SELECT account_id FROM users WHERE id = :id LIMIT 1");
            $userStmt->execute([':id' => $id]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                $this->sendError('Không tìm thấy khách hàng với ID: ' . $id, 404);
                return;
            }

            // Soft delete: Cập nhật status thành 'deleted' hoặc xóa account
            // Tùy vào yêu cầu, có thể soft delete account hoặc hard delete
            // Ở đây tôi sẽ soft delete bằng cách cập nhật status
            $updateStmt = $db->prepare("UPDATE accounts SET status = 'deleted' WHERE id = :account_id");
            $updateStmt->execute([':account_id' => $user['account_id']]);

            $this->sendResponse(null, 'Xóa khách hàng thành công');
        } catch (\Exception $e) {
            $this->sendError('Lỗi khi xóa khách hàng: ' . $e->getMessage(), 500);
        }
    }
}
