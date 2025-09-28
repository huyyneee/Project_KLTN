<?php
namespace App\Controllers;

use App\Models\CartModel;
use App\Core\Controller;

class CartController extends Controller
{
    public function index()
    {
    $cartModel = new CartModel();
    $carts = $cartModel->findAll();
        $this->render('carts/index', ['carts' => $carts]);
    }
}
