<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UserModel;
use App\Models\AddressModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Core\Controller;

class AccountController extends Controller
{
    private $accountModel;
    private $orderModel;
    private $addressModel;
    private $orderItemModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->orderModel = new OrderModel();
        $this->addressModel = new AddressModel();
        $this->orderItemModel = new OrderItemModel();
    }

    public function index()
    {
        $this->requireAuth();

        $accountId = $_SESSION['account_id'] ?? null;
        $account = null;
        $user = null;
        $addresses = [];
        if ($accountId) {
            $account = $this->accountModel->find($accountId);
            $userModel = new UserModel();
            $user = $userModel->findByAccountId($accountId);
            if ($user && !empty($user['id'])) {
                $addrModel = new AddressModel();
                $addresses = $addrModel->findByUserId((int)$user['id']);
            }
        }

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['account' => $account, 'user' => $user]);
            return;
        }

        $this->render('account/index', [
            'account' => $account,
            'user' => $user,
            'addresses' => $addresses,
            'hasAddresses' => !empty($addresses)
        ]);
    }

    // Hiển thị form chỉnh sửa thông tin tài khoản
    public function edit()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;
        $account = null;
        $user = null;
        if ($accountId) {
            $account = $this->accountModel->find($accountId);
            $userModel = new UserModel();
            $user = $userModel->findByAccountId($accountId);
        }
        $this->render('account/edit', ['account' => $account, 'user' => $user]);
    }

    // Xử lý cập nhật thông tin tài khoản
    public function update()
    {
        $this->requireAuth();

        $accountId = $_SESSION['account_id'] ?? null;
        if (!$accountId) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để thực hiện thao tác này';
            header('Location: /login');
            exit;
        }

        // Kiểm tra dữ liệu đầu vào
        $fullName = trim($_POST['full_name'] ?? '');
        $gender = $_POST['gender'] ?? 'other';
        $birthDay = $_POST['birth_day'] ?? '';
        $birthMonth = $_POST['birth_month'] ?? '';
        $birthYear = $_POST['birth_year'] ?? '';

        $errors = [];

        if (empty($fullName)) {
            $errors[] = 'Họ tên không được để trống';
        }

        if (!in_array($gender, ['male', 'female', 'other'])) {
            $errors[] = 'Giới tính không hợp lệ';
        }

        $birthday = null;
        if (!empty($birthDay) && !empty($birthMonth) && !empty($birthYear)) {
            if (checkdate((int)$birthMonth, (int)$birthDay, (int)$birthYear)) {
                $birthday = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);
            } else {
                $errors[] = 'Ngày sinh không hợp lệ';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /account/edit');
            exit;
        }

        // Cập nhật thông tin người dùng
        $userModel = new UserModel();
        $userData = [
            'full_name' => $fullName,
            'gender' => $gender
        ];

        if ($birthday !== null) {
            $userData['birthday'] = $birthday;
        }

        $success = $userModel->updateByAccountId($accountId, $userData);

        if ($success) {
            $_SESSION['success'] = 'Cập nhật thông tin thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thông tin';
        }

        header('Location: /account/edit');
        exit;
    }

    // Hiển thị sổ địa chỉ nhận hàng
    public function address()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;
        $account = null;
        $user = null;
        $addresses = [];
        if ($accountId) {
            $account = $this->accountModel->find($accountId);
            $userModel = new UserModel();
            $user = $userModel->findByAccountId($accountId);
            if ($user && !empty($user['id'])) {
                $addrModel = new AddressModel();
                $addresses = $addrModel->findByUserId((int)$user['id']);
            }
        }
        $this->render('account/address', [
            'account' => $account,
            'user' => $user,
            'addresses' => $addresses
        ]);
    }
    // Hiển thị danh sách đơn hàng
    public function order()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;

        if (!$accountId) {
            header('Location: /login');
            exit;
        }

        $userModel = new UserModel();
        $user = $userModel->findByAccountId($accountId);

        if (!$user || empty($user['id'])) {
            $this->render('account/order', ['orders' => [], 'user' => null]);
            return;
        }

        // --- Phân trang ---
        $limit = 3; // số đơn mỗi trang
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $totalOrders = $this->orderModel->countOrders((int)$user['id']);
        $totalPages = ceil($totalOrders / $limit);

        $orders = $this->orderModel->getOrdersWithItemsPaginated((int)$user['id'], $limit, $offset);

        $this->render('account/order', [
            'orders' => $orders,
            'user' => $user,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }

    // Hiển thị chi tiết 1 đơn hàng
    public function orderDetail()
    {
        $this->requireAuth();

        $orderId = $_GET['id'] ?? null;
        if (!$orderId) {
            $_SESSION['error'] = 'Thiếu mã đơn hàng.';
            header('Location: /account/order');
            exit;
        }

        $accountId = $_SESSION['account_id'] ?? null;
        if (!$accountId) {
            header('Location: /login');
            exit;
        }

        $userModel = new UserModel();
        $user = $userModel->findByAccountId($accountId);
        if (!$user || empty($user['id'])) {
            $_SESSION['error'] = 'Không tìm thấy người dùng.';
            header('Location: /account/order');
            exit;
        }

        // Lấy đơn hàng
        $order = $this->orderModel->findById($orderId);
        if (!$order || $order['user_id'] != $user['id']) {
            $_SESSION['error'] = 'Đơn hàng không tồn tại hoặc không thuộc về bạn.';
            header('Location: /account/order');
            exit;
        }

        // Lấy chi tiết sản phẩm
        $items = $this->orderItemModel->getItemsByOrder($orderId);

        // Tính subtotal từ items
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $paymentMethod = $order['payment_method'];
        $this->render('account/order_detail', [
            'order' => $order,
            'items' => $items,
            'subtotal' => $subtotal,
            'paymentMethod' => $paymentMethod,
            'user' => $user
        ]);
    }
    // Hủy đơn hàng
    public function cancelOrder()
    {
        $this->requireAuth();
        $userId = $_SESSION['account_id'] ?? null;
        $orderId = $_POST['order_id'] ?? null;

        header('Content-Type: application/json');

        if (!$userId || !$orderId) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin hủy đơn hàng.'
            ]);
            return;
        }

        $result = $this->orderModel->cancelOrder($orderId, $userId);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Đơn hàng đã được hủy thành công.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng. Vui lòng thử lại.'
            ]);
        }
    }
}
