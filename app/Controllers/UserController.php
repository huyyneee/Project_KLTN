<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Core\Controller;

class UserController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['users' => $users]);
            return;
        }

        $this->render('users/index', ['users' => $users]);
    }
}
