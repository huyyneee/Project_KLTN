<?php
namespace App\Controllers;

use App\Models\Account;
use App\Core\Controller;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = (new Account())->findAll();
        $this->render('accounts/index', ['accounts' => $accounts]);
    }
}
