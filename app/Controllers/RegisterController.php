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

        // require verification code (client-side generated)
        $verification = isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '';
        if (empty($verification)) {
            $errors[] = 'Vui lòng nhập mã xác thực.';
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
            // registration success -> redirect to login
            $this->redirect('/login');
        } else {
            $errors[] = 'Lỗi hệ thống, vui lòng thử lại.';
            $this->render('register', ['errors' => $errors, 'old' => ['email'=>$email, 'full_name'=>$full_name]]);
        }
    }
}
