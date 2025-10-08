<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

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

        // find account by email or phone (we only have email in accounts table)
        $db = (new \App\Core\Database())->getConnection();
        $stmt = $db->prepare('SELECT * FROM accounts WHERE email = :id LIMIT 1');
        $stmt->execute([':id' => $identity]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            // account not found -> client should show modelok with ĐĂNG KÝ / THOÁT
            echo json_encode(['ok' => false, 'reason' => 'not_found', 'message' => 'ACCOUNT_NOT_FOUND']);
            return;
        }

        // check status
        $status = $row['status'] ?? 'active';
        if ($status !== 'active') {
            echo json_encode(['ok' => false, 'reason' => 'status', 'status' => $status, 'message' => 'YOUR ACCOUNT HAS BEEN ' . $status]);
            return;
        }

        // password check (accounts stored using md5 in register flow)
        $hashed = md5($password);
        if (!hash_equals($row['password'], $hashed)) {
            echo json_encode(['ok' => false, 'reason' => 'bad_password', 'message' => 'please check password']);
            return;
        }

    // login success: set session and return OK
    $_SESSION['account_id'] = $row['id'];
    $_SESSION['account_email'] = $row['email'];
    // also set cookies so users stay logged in for 7 days
    $expire = time() + 7 * 24 * 60 * 60; // 7 days
    // store expiry as a separate cookie so server can validate even if client clock changed
    setcookie('account_id', $row['id'], $expire, '/', '', false, true);
    setcookie('account_email', $row['email'], $expire, '/', '', false, true);
    setcookie('account_expires', (string)$expire, $expire, '/', '', false, true);
        // update last_login
        try {
            $u = $db->prepare('UPDATE accounts SET last_login = :ts WHERE id = :id');
            $u->execute([':ts' => date('Y-m-d H:i:s'), ':id' => $row['id']]);
        } catch (\Throwable $e) {}

        // determine safe return target
        $return = '/';
        if (!empty($_GET['return'])) {
            $r = $_GET['return'];
            // only allow internal paths
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
    // clear session and cookies, then redirect to home
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) session_start();
    // unset session keys
    unset($_SESSION['account_id']);
    unset($_SESSION['account_email']);
    // clear cookies by setting past expiry
    setcookie('account_id', '', time() - 3600, '/', '', false, true);
    setcookie('account_email', '', time() - 3600, '/', '', false, true);
    setcookie('account_expires', '', time() - 3600, '/', '', false, true);
        // redirect to home
        header('Location: /');
        return;
    }
}
