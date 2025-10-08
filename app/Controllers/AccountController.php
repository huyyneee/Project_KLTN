<?php
namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UserModel;
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
}
