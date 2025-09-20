<?php
namespace App\Controllers;

use App\Models\Cart;
use App\Core\Controller;

class CartController extends Controller
{
    public function index()
    {
        $carts = (new Cart())->findAll();
        $this->render('carts/index', ['carts' => $carts]);
    }
}
