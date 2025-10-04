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
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $birth_year = isset($_POST['birth_year']) ? $_POST['birth_year'] : '';
    $birth_month = isset($_POST['birth_month']) ? $_POST['birth_month'] : '';
    $birth_day = isset($_POST['birth_day']) ? $_POST['birth_day'] : '';

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        } else {
            // require gmail.com domain
            if (strtolower(substr($email, -10)) !== '@gmail.com') {
                $errors[] = 'Email phải có định dạng @gmail.com.';
            }
        }
        // require full name
        if (empty($full_name)) {
            $errors[] = 'Vui lòng điền họ tên.';
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
            // If email already exists, render the register view and instruct client to show modelok modal
            $captcha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 4);
            $old = [
                'email' => $email,
                'full_name' => $full_name,
                'gender' => $gender,
                'birth_year' => $birth_year,
                'birth_month' => $birth_month,
                'birth_day' => $birth_day
            ];
            $this->render('register', [
                'errors' => [],
                'old' => $old,
                'captcha' => $captcha,
                'show_modelok' => true,
                'modelok_message' => 'TÀI KHOẢN ĐÃ ĐƯỢC TẠO, VUI LÒNG ĐĂNG NHẬP HOẶC SỬ DỤNG EMAIL KHÁC'
            ]);
            return;
        }

        // age check: require >= 18 if birth fields provided
        if ($birth_year && $birth_month && $birth_day) {
            $birthTs = strtotime(sprintf('%04d-%02d-%02d', (int)$birth_year, (int)$birth_month, (int)$birth_day));
            if ($birthTs === false) {
                $errors[] = 'Ngày sinh không hợp lệ.';
            } else {
                $age = (int) floor((time() - $birthTs) / (365.25*24*60*60));
                if ($age < 18) {
                    $errors[] = 'Người dùng phải đủ 18 tuổi trở lên để đăng ký.';
                }
            }
        }

        if (!empty($errors)) {
            // re-render with errors and old input and a fresh captcha
            $captcha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 4);
            $old = [
                'email' => $email,
                'full_name' => $full_name,
                'gender' => $gender,
                'birth_year' => $birth_year,
                'birth_month' => $birth_month,
                'birth_day' => $birth_day
            ];
            $this->render('register', ['errors' => $errors, 'old' => $old, 'captcha' => $captcha]);
            return;
        }

        // Verification handling: if verification code not provided or doesn't match session, send code and ask user to enter it
        $verification = isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '';
        $sessionCode = $_SESSION['verif_codes'][$email]['code'] ?? null;
        $sessionTs = $_SESSION['verif_codes'][$email]['ts'] ?? null;
        $codeValid = false;
        if ($sessionCode && $sessionTs && (time() - $sessionTs) <= 600) {
            if ($verification !== '' && strcasecmp($verification, $sessionCode) === 0) {
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
            $message = 'Vui lòng nhập mã xác thực. Chúng tôi đã gửi mã tới email của bạn.';
            $captcha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 4);
            $old = [
                'email' => $email,
                'full_name' => $full_name,
                'gender' => $gender,
                'birth_year' => $birth_year,
                'birth_month' => $birth_month,
                'birth_day' => $birth_day
            ];
            // indicate view to focus/show verification message
            $this->render('register', ['errors' => [], 'message' => $message, 'require_verification' => true, 'old' => $old, 'captcha' => $captcha]);
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
            // registration success -> clear session code
            unset($_SESSION['verif_codes'][$email]);
            // create users profile row linked to account
            try {
                $userModel = new \App\Models\UserModel();
                $birthday = null;
                if ($birth_year && $birth_month && $birth_day) {
                    $birthday = sprintf('%04d-%02d-%02d', (int)$birth_year, (int)$birth_month, (int)$birth_day);
                }
                $userData = [
                    'account_id' => $id,
                    'full_name' => $full_name,
                    'phone' => '',
                    'address' => '',
                    'birthday' => $birthday,
                    'gender' => $gender,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                $userModel->create($userData);
            } catch (\Throwable $e) {
                // log but continue
                error_log('Failed to create users row: ' . $e->getMessage());
            }

            // instead of immediate redirect, render view with success flag so client can show dialog and redirect after 15s
            $this->render('register', ['errors' => [], 'old' => [], 'captcha' => '', 'created_account' => true]);
            return;
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
        // check if email already exists
        $db = (new \App\Core\Database())->getConnection();
        $stmt = $db->prepare('SELECT id FROM accounts WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'Email đã được sử dụng']);
            return;
        }

        $code = \generate_verification_code();
        // store code in session keyed by email
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

    // AJAX: check email exists
    public function checkEmail()
    {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Email không hợp lệ']);
            return;
        }
        $db = (new \App\Core\Database())->getConnection();
        $stmt = $db->prepare('SELECT id FROM accounts WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            echo json_encode(['ok' => false, 'exists' => true]);
        } else {
            echo json_encode(['ok' => true, 'exists' => false]);
        }
    }
}
