<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\UserModel;
use App\Models\AddressModel;
use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Core\Controller;

class OrderController extends Controller
{
    private $orderModel;
    private $addressModel;
    private $cartModel;
    private $cartItemModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->addressModel = new AddressModel();
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
    }

    public function index()
    {
        $this->requireAuth(); // đảm bảo user đã đăng nhập
        $accountId = $_SESSION['account_id'] ?? null;

        // Lấy giỏ hàng từ DB theo user
        $cart = $this->cartModel->getCartByUser($accountId);
        $cartItems = $cart ? $this->cartItemModel->getItemsByCart($cart['id']) : [];

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Lấy user & địa chỉ
        $user = null;
        $addresses = [];
        if ($accountId) {
            $userModel = new UserModel();
            $user = $userModel->findByAccountId($accountId);
            if ($user && !empty($user['id'])) {
                $addresses = $this->addressModel->findByUserId((int)$user['id']);
            }
        }

        // Render view checkout, truyền cartItems từ DB
        $this->render('checkout', [
            'cartItems' => $cartItems,
            'total' => $total,
            'addresses' => $addresses,
            'user' => $user
        ]);
    }
}
