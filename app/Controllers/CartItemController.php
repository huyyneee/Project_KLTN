<?php
namespace App\Controllers;

use App\Models\CartItemModel;
use App\Core\Controller;

class CartItemController extends Controller
{
    public function index()
    {
    $cartItemModel = new CartItemModel();
    $cartItems = $cartItemModel->findAll();
        $this->render('cart_items/index', ['cartItems' => $cartItems]);
    }
}
