<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\AccountModel;

class LoginController extends Controller
{
    private $userModel;
    private $accountModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->accountModel = new AccountModel();
    }
    public function index()
    {
        $this->render('login', []);
    }

    // POST /account/login
    public function authenticate()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent())
            session_start();
        header('Content-Type: application/json');

        $identity = trim($_POST['identity'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$identity || !$password) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Missing credentials']);
            return;
        }

        $account = $this->accountModel->findByEmail($identity);
        if (!$account) {
            echo json_encode(['ok' => false, 'reason' => 'not_found', 'message' => 'ACCOUNT_NOT_FOUND']);
            return;
        }

        $status = $account['status'] ?? 'active';
        if ($status !== 'active') {
            $message = $status === 'banned'
                ? 'Tài khoản của bạn đã bị cấm. Vui lòng liên hệ quản trị viên.'
                : 'Tài khoản của bạn không hoạt động.';
            echo json_encode(['ok' => false, 'reason' => 'status', 'status' => $status, 'message' => $message]);
            return;
        }

        if (!hash_equals($account['password'], md5($password))) {
            echo json_encode(['ok' => false, 'reason' => 'bad_password', 'message' => 'please check password']);
            return;
        }

        // login success: set session and cookies
        $_SESSION['account_id'] = $account['id'];
        $_SESSION['account_email'] = $account['email'];
        $expire = time() + 7 * 24 * 60 * 60;
        setcookie('account_id', $account['id'], $expire, '/', '', false, true);
        setcookie('account_email', $account['email'], $expire, '/', '', false, true);
        setcookie('account_expires', (string) $expire, $expire, '/', '', false, true);

        // update last_login
        $this->accountModel->updateLastLogin($account['id']);

        // Handle pending cart
        $return = '/';
        if (!empty($_SESSION['cart_pending'])) {
            $pending = $_SESSION['cart_pending'];
            unset($_SESSION['cart_pending']);

            $cartModel = new CartModel();
            $cartItemModel = new CartItemModel();

            $cart = $cartModel->getCartByUser($account['id']);
            $cartId = $cart ? $cart['id'] : $cartModel->createCart($account['id']);

            $existingItem = $cartItemModel->getItemByCartAndProduct($cartId, $pending['product_id']);
            if ($existingItem) {
                $cartItemModel->updateQuantity($existingItem['id'], $existingItem['quantity'] + $pending['quantity']);
            } else {
                $cartItemModel->addItem($cartId, $pending['product_id'], $pending['quantity'], $pending['price']);
            }
            $return = '/cart';
        } elseif (!empty($_GET['return'])) {
            $r = $_GET['return'];
            if (is_string($r) && strlen($r) > 0 && strpos($r, '/') === 0) {
                $return = $r;
            }
        }

        echo json_encode(['ok' => true, 'redirect' => $return]);
    }

    // GET /account/logout
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        unset($_SESSION['account_id'], $_SESSION['account_email']);
        setcookie('account_id', '', time() - 3600, '/', '', false, true);
        setcookie('account_email', '', time() - 3600, '/', '', false, true);
        setcookie('account_expires', '', time() - 3600, '/', '', false, true);
        header('Location: /');
    }

    // GET /account/forgot-password
    public function forgotPasswordForm()
    {
        $this->render('forgot_password', []);
    }

    // POST /account/send-reset-link
    public function sendResetLink()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        header('Content-Type: application/json');

        $email = trim($_POST['email'] ?? '');
        if (!$email) {
            echo json_encode(['ok' => false, 'message' => 'Vui lòng nhập email']);
            return;
        }

        $account = $this->accountModel->findByEmail($email);
        if (!$account) {
            echo json_encode(['ok' => false, 'message' => 'Email không tồn tại']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 300); // 5p
        $this->accountModel->setPasswordResetToken($account['id'], $token, $expires);

        $resetLink = "http://{$_SERVER['HTTP_HOST']}/login/reset-password?token=$token";
        $body = "Nhấn vào link sau để đặt lại mật khẩu: <a href=\"$resetLink\">$resetLink</a>";

        if (\send_mail($email, 'Đặt lại mật khẩu', $body)) {
            echo json_encode(['ok' => true, 'message' => 'Vui lòng kiểm tra email để đặt lại mật khẩu']);
        } else {
            echo json_encode(['ok' => false, 'message' => 'Gửi email thất bại']);
        }
    }

    // GET /account/reset-password
    public function resetPasswordForm()
    {
        $_SESSION['disable_auto_login'] = true;
        unset($_SESSION['account_id'], $_SESSION['account_email']);
        $token = $_GET['token'] ?? '';
        if (!$token) {
            echo 'Token không hợp lệ';
            return;
        }

        $account = $this->accountModel->findByResetToken($token);
        if (!$account || strtotime($account['password_reset_expires']) < time()) {
            echo 'Token đã hết hạn hoặc không hợp lệ';
            return;
        }

        $this->render('reset_password', ['token' => $token]);
    }

    // POST /account/reset-password
    public function resetPassword()
    {
        header('Content-Type: application/json');
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$token || !$password || !$confirm) {
            echo json_encode(['ok' => false, 'message' => 'Thiếu dữ liệu']);
            return;
        }

        if ($password !== $confirm) {
            echo json_encode(['ok' => false, 'message' => 'Mật khẩu không khớp']);
            return;
        }
        // Kiểm tra độ mạnh mật khẩu
        if (
            !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $password)
            || strlen($password) > 32
        ) {
            echo json_encode([
                'ok' => false,
                'message' => 'Mật khẩu phải từ 8-32 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.'
            ]);
            return;
        }
        $account = $this->accountModel->findByResetToken($token);
        if (!$account || strtotime($account['password_reset_expires']) < time()) {
            echo json_encode(['ok' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn']);
            return;
        }
        // Mã hóa mật khẩu
        $hashed = md5($password);
        $this->accountModel->updatePassword($account['id'], $hashed);

        echo json_encode(['ok' => true, 'message' => 'Đặt lại mật khẩu thành công']);
    }
}
