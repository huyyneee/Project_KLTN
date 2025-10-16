<?php
namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UserModel;
use App\Models\AddressModel;
use App\Core\Controller;

class AccountController extends Controller
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
    }

    public function index()
    {
        // protect this page
        $this->requireAuth();

        $accountId = $_SESSION['account_id'] ?? null;
        $account = null;
        $user = null;
        if ($accountId) {
            $account = $this->accountModel->find($accountId);
            $userModel = new UserModel();
            $user = $userModel->findByAccountId($accountId);
        }

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['account' => $account, 'user' => $user]);
            return;
        }

        $this->render('account/index', ['account' => $account, 'user' => $user]);
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

    // moved address-related actions to AddressController


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

        // Validate input
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

        // Process birthday if all components are provided
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

        // Update user information
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
}
