<?php
namespace App\Controllers;

use App\Models\CartModel;
use App\Core\Controller;

class CartController extends Controller
{
    private $cartModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
    }

    public function index()
    {
        $this->requireAuth();
        $carts = $this->cartModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['carts' => $carts]);
            return;
        }

        $this->render('carts/index', ['carts' => $carts]);
    }
}
