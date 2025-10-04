<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AccountModel;

class RegisterController extends Controller
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
    }

    public function index()
    {
        // generate captcha server-side for initial render
        $captcha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 4);
        $this->render('register', ['captcha' => $captcha]);
    }

    public function store()
    {
        // basic server-side validation
        session_start();
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        }
        // password rules: min 8, upper, lower, number, special
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $password) || strlen($password) > 32) {
            $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.';
        }

        // require agreement
        $agree = isset($_POST['agree']) ? $_POST['agree'] : '';
        if (empty($agree)) {
            $errors[] = 'Bạn phải đồng ý với điều khoản.';
        }

        // check duplicate email using Database helper
        $db = (new \App\Core\Database())->getConnection();
        $stmt = $db->prepare('SELECT id FROM accounts WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email đã được sử dụng.';
        }

        if (!empty($errors)) {
            // re-render with errors and old input and a fresh captcha
            $captcha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 4);
            $this->render('register', ['errors' => $errors, 'old' => ['email'=>$email, 'full_name'=>$full_name], 'captcha' => $captcha]);
            return;
        }

        // Verification handling: if verification code not provided or doesn't match session, send code and ask user to enter it
        $verification = isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '';
        $sessionCode = $_SESSION['verif_codes'][$email]['code'] ?? null;
        $sessionTs = $_SESSION['verif_codes'][$email]['ts'] ?? null;
        $codeValid = false;
        if ($sessionCode && $sessionTs && (time() - $sessionTs) <= 600) {
            if ($verification !== '' && $verification === $sessionCode) {
                $codeValid = true;
            }
        }

        if (!$codeValid) {
            // generate and send code, then re-render form requesting verification
            $code = \generate_verification_code();
            $_SESSION['verif_codes'][$email] = ['code' => $code, 'ts' => time()];
            $subject = 'Mã xác thực đăng ký - Cửa Hàng Mỹ Phẩm Xuân Hiệp';
            $body = "Mã xác thực của bạn là: <strong>{$code}</strong>. Mã có hiệu lực 10 phút.";
            \send_mail($email, $subject, $body);
            $message = 'Mã xác thực đã được gửi tới email của bạn. Vui lòng kiểm tra và nhập mã để hoàn tất đăng ký.';
            $captcha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 4);
            $this->render('register', ['errors' => [], 'message' => $message, 'old' => ['email'=>$email, 'full_name'=>$full_name], 'captcha' => $captcha]);
            return;
        }

        // save account (password hashed with md5 as requested)
        $now = date('Y-m-d H:i:s');
        $data = [
            'email' => $email,
            'password' => md5($password),
            'full_name' => $full_name,
            'created_at' => $now,
            'updated_at' => $now,
            'role' => 'user',
            'status' => 1
        ];

        $id = $this->accountModel->create($data);
        if ($id) {
            // registration success -> clear session code and redirect to login
            unset($_SESSION['verif_codes'][$email]);
            $this->redirect('/login');
        } else {
            $errors[] = 'Lỗi hệ thống, vui lòng thử lại.';
            $this->render('register', ['errors' => $errors, 'old' => ['email'=>$email, 'full_name'=>$full_name]]);
        }
    }

    // API endpoint: send verification code to email (AJAX)
    public function sendCode()
    {
        session_start();
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Email không hợp lệ']);
            return;
        }
        $code = \generate_verification_code();
        // store code in session keyed by email (expires on next requests or you can add ttl)
        $_SESSION['verif_codes'][$email] = ['code' => $code, 'ts' => time()];

        $subject = 'Mã xác thực đăng ký - Cửa Hàng Mỹ Phẩm Xuân Hiệp';
        $body = "Mã xác thực của bạn là: <strong>{$code}</strong>. Mã có hiệu lực 10 phút.";
        $sent = \send_mail($email, $subject, $body);
        if ($sent) {
            echo json_encode(['ok' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Không thể gửi email (kiểm tra cấu hình mail)']);
        }
    }
}
