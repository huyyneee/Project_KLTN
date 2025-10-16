<?php
namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UserModel;
use App\Models\AddressModel;
use App\Core\Controller;

class AddressController extends Controller
{
    private $addressModel;
    private $accountModel;

    public function __construct()
    {
        $this->addressModel = new AddressModel();
        $this->accountModel = new AccountModel();
    }

    // GET /account/address
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
                $addresses = $this->addressModel->findByUserId((int)$user['id']);
            }
        }
        $this->render('account/address', ['account' => $account, 'user' => $user, 'addresses' => $addresses]);
    }

    // POST /account/address/add
    public function addAddress()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;
        if (!$accountId) {
            header('Location: /login');
            exit;
        }

        $user = (new UserModel())->findByAccountId($accountId);
        if (!$user || empty($user['id'])) {
            $_SESSION['error'] = 'Không tìm thấy người dùng';
            header('Location: /account/address');
            exit;
        }

        $receiverName = trim($_POST['name'] ?? '');
        $phone        = trim($_POST['phone'] ?? '');
        $province     = trim($_POST['province'] ?? '');
        $district     = trim($_POST['district'] ?? '');
        $ward         = trim($_POST['ward'] ?? '');
        $street       = trim($_POST['address'] ?? '');

        $errors = [];
        if ($receiverName === '') $errors[] = 'Vui lòng nhập tên người nhận';
        if ($phone === '' || !preg_match('/^0[0-9]{8,10}$/', $phone)) $errors[] = 'Số điện thoại không hợp lệ';
        if ($province === '') $errors[] = 'Vui lòng nhập Tỉnh/Thành phố';
        if ($district === '') $errors[] = 'Vui lòng nhập Quận/Huyện';
        if ($ward === '') $errors[] = 'Vui lòng nhập Phường/Xã';
        if ($street === '') $errors[] = 'Vui lòng nhập Địa chỉ nhận hàng';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /account/address');
            exit;
        }

        $existing = $this->addressModel->findByUserId((int)$user['id']);
        $isDefault = empty($existing) ? 1 : 0;

        $now = date('Y-m-d H:i:s');
        $this->addressModel->create([
            'user_id'      => (int)$user['id'],
            'receiver_name'=> $receiverName,
            'phone'        => $phone,
            'street'       => $street,
            'ward'         => $ward,
            'district'     => $district,
            'city'         => $province,
            'province'     => '',
            'type'         => 'home',
            'is_default'   => $isDefault,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $_SESSION['success'] = 'Đã thêm địa chỉ mới';
        header('Location: /account/address');
        exit;
    }

    // GET /account/address/edit?id=123
    public function editAddress()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$accountId || !$id) {
            header('Location: /account/address');
            exit;
        }

        $user = (new UserModel())->findByAccountId($accountId);
        if (!$user) {
            header('Location: /account/address');
            exit;
        }

        $address = $this->addressModel->findByIdAndUser($id, (int)$user['id']);
        if (!$address) {
            $_SESSION['error'] = 'Địa chỉ không tồn tại';
            header('Location: /account/address');
            exit;
        }

        $account = $this->accountModel->find($accountId);
        $addresses = $this->addressModel->findByUserId((int)$user['id']);
        $this->render('account/address', [
            'account' => $account,
            'user' => $user,
            'addresses' => $addresses,
            'editing' => $address
        ]);
    }

    // POST /account/address/update
    public function updateAddress()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$accountId || !$id) {
            header('Location: /account/address');
            exit;
        }

        $user = (new UserModel())->findByAccountId($accountId);
        if (!$user) {
            header('Location: /account/address');
            exit;
        }

        $address = $this->addressModel->findByIdAndUser($id, (int)$user['id']);
        if (!$address) {
            $_SESSION['error'] = 'Địa chỉ không tồn tại';
            header('Location: /account/address');
            exit;
        }

        $receiverName = trim($_POST['name'] ?? '');
        $phone        = trim($_POST['phone'] ?? '');
        $province     = trim($_POST['province'] ?? '');
        $district     = trim($_POST['district'] ?? '');
        $ward         = trim($_POST['ward'] ?? '');
        $street       = trim($_POST['address'] ?? '');

        $errors = [];
        if ($receiverName === '') $errors[] = 'Vui lòng nhập tên người nhận';
        if ($phone === '' || !preg_match('/^0[0-9]{8,10}$/', $phone)) $errors[] = 'Số điện thoại không hợp lệ';
        if ($province === '') $errors[] = 'Vui lòng nhập Tỉnh/Thành phố';
        if ($district === '') $errors[] = 'Vui lòng nhập Quận/Huyện';
        if ($ward === '') $errors[] = 'Vui lòng nhập Phường/Xã';
        if ($street === '') $errors[] = 'Vui lòng nhập Địa chỉ nhận hàng';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /account/address/edit?id=' . $id);
            exit;
        }

        $now = date('Y-m-d H:i:s');
        $this->addressModel->update($id, [
            'receiver_name' => $receiverName,
            'phone' => $phone,
            'street' => $street,
            'ward' => $ward,
            'district' => $district,
            'city' => $province,
            'updated_at' => $now,
        ]);

        $_SESSION['success'] = 'Đã cập nhật địa chỉ';
        header('Location: /account/address');
        exit;
    }

    // POST /account/address/delete
    public function deleteAddress()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$accountId || !$id) {
            header('Location: /account/address');
            exit;
        }

        $user = (new UserModel())->findByAccountId($accountId);
        if (!$user) {
            header('Location: /account/address');
            exit;
        }

        $address = $this->addressModel->findByIdAndUser($id, (int)$user['id']);
        if (!$address) {
            $_SESSION['error'] = 'Địa chỉ không tồn tại';
            header('Location: /account/address');
            exit;
        }

        $this->addressModel->delete($id);
        $_SESSION['success'] = 'Đã xóa địa chỉ';
        header('Location: /account/address');
        exit;
    }
}
