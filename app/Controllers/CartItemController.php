<?php
namespace App\Controllers;

use App\Models\CartItem;
use App\Core\Controller;

class CartItemController extends Controller
{
    public function index()
    {
        $cartItems = (new CartItem())->findAll();
        $this->render('cart_items/index', ['cartItems' => $cartItems]);
    }
}
