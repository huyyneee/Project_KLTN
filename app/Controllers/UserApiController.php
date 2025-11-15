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
            $users = $this->userModel->findAll();

            // Format dữ liệu để trả về
            $formattedUsers = array_map(function ($user) {
                return [
                    'id' => (int) $user['id'],
                    'account_id' => (int) $user['account_id'],
                    'full_name' => $user['full_name'],
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

            // Format dữ liệu để trả về
            $formattedUser = [
                'id' => (int) $user['id'],
                'account_id' => (int) $user['account_id'],
                'full_name' => $user['full_name'],
                'phone' => $user['phone'],
                'address' => $user['address'],
                'birthday' => $user['birthday'],
                'gender' => $user['gender'],
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

            // Sử dụng SQL LIKE để tìm kiếm
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT * FROM users 
                WHERE full_name LIKE :query 
                OR phone LIKE :query 
                ORDER BY created_at DESC
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format dữ liệu để trả về
            $formattedUsers = array_map(function ($user) {
                return [
                    'id' => (int) $user['id'],
                    'account_id' => (int) $user['account_id'],
                    'full_name' => $user['full_name'],
                    'phone' => $user['phone'],
                    'address' => $user['address'],
                    'birthday' => $user['birthday'],
                    'gender' => $user['gender'],
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

            // Đếm tổng số bản ghi
            $countStmt = $db->query("SELECT COUNT(*) as total FROM users");
            $totalRecords = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Lấy dữ liệu với phân trang
            $stmt = $db->prepare("
                SELECT * FROM users 
                ORDER BY created_at DESC 
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
                    'full_name' => $user['full_name'],
                    'phone' => $user['phone'],
                    'address' => $user['address'],
                    'birthday' => $user['birthday'],
                    'gender' => $user['gender'],
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
}
