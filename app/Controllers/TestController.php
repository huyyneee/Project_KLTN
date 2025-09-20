<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;

class TestController extends Controller
{
    public function index()
    {
        $accounts = (new Account())->findAll();
        $this->render('test', ['accounts' => $accounts]);
    }
}
