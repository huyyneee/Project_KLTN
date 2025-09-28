<?php
namespace App\Controllers;

use App\Models\AccountModel;
use App\Core\Controller;

class AccountController extends Controller
{
    public function index()
    {
    $accountModel = new AccountModel();
    $accounts = $accountModel->findAll();
        $this->render('accounts/index', ['accounts' => $accounts]);
    }
}
