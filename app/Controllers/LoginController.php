<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\CartModel;
use App\Models\CartItemModel;

class LoginController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // render login page (header/footer included by layout)
        $this->render('login', []);
    }

    // POST /account/login
    public function authenticate()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $identity = isset($_POST['identity']) ? trim($_POST['identity']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        header('Content-Type: application/json');

        if (!$identity || !$password) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Missing credentials']);
            return;
        }

        // find account by email
        $db = (new \App\Core\Database())->getConnection();
        $stmt = $db->prepare('SELECT * FROM accounts WHERE email = :id LIMIT 1');
        $stmt->execute([':id' => $identity]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode(['ok' => false, 'reason' => 'not_found', 'message' => 'ACCOUNT_NOT_FOUND']);
            return;
        }

        // check status
        $status = $row['status'] ?? 'active';
        if ($status !== 'active') {
            echo json_encode(['ok' => false, 'reason' => 'status', 'status' => $status, 'message' => 'YOUR ACCOUNT HAS BEEN ' . $status]);
            return;
        }

        // password check
        $hashed = md5($password);
        if (!hash_equals($row['password'], $hashed)) {
            echo json_encode(['ok' => false, 'reason' => 'bad_password', 'message' => 'please check password']);
            return;
        }

        // login success: set session and cookies
        $_SESSION['account_id'] = $row['id'];
        $_SESSION['account_email'] = $row['email'];
        $expire = time() + 7 * 24 * 60 * 60;
        setcookie('account_id', $row['id'], $expire, '/', '', false, true);
        setcookie('account_email', $row['email'], $expire, '/', '', false, true);
        setcookie('account_expires', (string)$expire, $expire, '/', '', false, true);

        // update last_login
        try {
            $u = $db->prepare('UPDATE accounts SET last_login = :ts WHERE id = :id');
            $u->execute([':ts' => date('Y-m-d H:i:s'), ':id' => $row['id']]);
        } catch (\Throwable $e) {
        }

        // === HANDLE PENDING CART ITEM ===
        $return = '/';
        if (!empty($_SESSION['cart_pending'])) {
            $pending = $_SESSION['cart_pending'];
            unset($_SESSION['cart_pending']);

            $cartModel = new CartModel();
            $cartItemModel = new CartItemModel();

            // Lấy giỏ hàng hiện tại hoặc tạo mới
            $cart = $cartModel->getCartByUser($row['id']);
            $cartId = $cart ? $cart['id'] : $cartModel->createCart($row['id']);

            // Thêm sản phẩm pending vào giỏ
            $existingItem = $cartItemModel->getItemByCartAndProduct($cartId, $pending['product_id']);
            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $pending['quantity'];
                $cartItemModel->updateQuantity($existingItem['id'], $newQuantity);
            } else {
                $cartItemModel->addItem(
                    $cartId,
                    $pending['product_id'],
                    $pending['quantity'],
                    $pending['price']
                );
            }

            $return = '/cart';
        } else if (!empty($_GET['return'])) {
            $r = $_GET['return'];
            if (is_string($r) && strlen($r) > 0 && strpos($r, '/') === 0) {
                $return = $r;
            }
        }

        echo json_encode(['ok' => true, 'redirect' => $return]);
        return;
    }

    // GET /account/logout
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) session_start();
        unset($_SESSION['account_id']);
        unset($_SESSION['account_email']);
        setcookie('account_id', '', time() - 3600, '/', '', false, true);
        setcookie('account_email', '', time() - 3600, '/', '', false, true);
        setcookie('account_expires', '', time() - 3600, '/', '', false, true);
        header('Location: /');
        return;
    }
}
