<?php
namespace App\Controllers;

use App\Models\AccountModel;
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
        $accounts = $this->accountModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['accounts' => $accounts]);
            return;
        }

        $this->render('accounts/index', ['accounts' => $accounts]);
    }
}
