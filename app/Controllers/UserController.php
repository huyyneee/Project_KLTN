<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = (new User())->findAll();
        $this->render('users/index', ['users' => $users]);
    }
}
