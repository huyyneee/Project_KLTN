<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Core\Controller;

class UserController extends Controller
{
    public function index()
    {
    $userModel = new UserModel();
    $users = $userModel->findAll();
        $this->render('users/index', ['users' => $users]);
    }
}
