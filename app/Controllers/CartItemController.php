<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Core\Controller;


class CartItemController extends Controller
{
    private $cartItemModel;

    public function __construct()
    {
        $this->cartItemModel = new CartItemModel();
    }

    public function index()
    {
        $cartItems = $this->cartItemModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['cartItems' => $cartItems]);
            return;
        }

        $this->render('cart_items/index', ['cartItems' => $cartItems]);
    }
}
