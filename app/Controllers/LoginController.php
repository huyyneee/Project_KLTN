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
}
