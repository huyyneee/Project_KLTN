<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\CartModel;
use App\Models\CartItemModel;

class CartController extends Controller
{
    private $cartModel;
    private $cartItemModel;
    private $userModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->userModel = new UserModel();
    }

    // Hiển thị trang giỏ hàng
    public function index()
    {
        $this->requireAuth();

        $accountId = $_SESSION['account_id'] ?? null;
        if (!$accountId) {
            $this->redirect('/login');
            return;
        }
        $user = $this->userModel->findByAccountId($accountId);
        if (!$user) {
            $this->render('cart', ['cart' => null, 'items' => []]);
            return;
        }

        $userId = $user['id'];

        $cart = $this->cartModel->getCartByUser($userId);
        $items = $cart ? $this->cartItemModel->getItemsByCart($cart['id']) : [];

        $this->render('cart', [
            'cart'  => $cart,
            'items' => $items
        ]);
    }

    // Thêm sản phẩm vào giỏ
    public function add()
    {
        $accountId = $_SESSION['account_id'] ?? null;
        $productId = $_POST['product_id'] ?? null;
        $quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $price     = isset($_POST['price']) ? (float)$_POST['price'] : 0;

        if (!$productId || $quantity <= 0) {
            http_response_code(400);
            return $this->json(['error' => 'Dữ liệu không hợp lệ']);
        }

        // Nếu chưa login
        if (!$accountId) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['cart_pending'] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $price
            ];

            return $this->json([
                'login_required' => true,
                'redirect' => '/login?return=/cart'
            ]);
        }

        $user = $this->userModel->findByAccountId($accountId);
        if (!$user) {
            http_response_code(400);
            return $this->json(['error' => 'User chưa tồn tại trong bảng users']);
        }
        $userId = $user['id'];

        $cart = $this->cartModel->getCartByUser($userId);
        $cartId = $cart ? $cart['id'] : $this->cartModel->createCart($userId);

        $existingItem = $this->cartItemModel->getItemByCartAndProduct($cartId, $productId);

        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            $this->cartItemModel->updateQuantity($existingItem['id'], $newQuantity);
        } else {
            $this->cartItemModel->addItem($cartId, $productId, $quantity, $price);
        }

        return $this->json(['success' => true]);
    }
    // Xoá sản phẩm trong giỏ
    public function remove()
    {
        $this->requireAuthAjax();

        $itemId = $_POST['item_id'] ?? null;
        if (!$itemId) {
            http_response_code(400);
            return $this->json(['error' => 'Thiếu dữ liệu']);
        }

        $this->cartItemModel->deleteItem($itemId);
        return $this->json(['success' => true]);
    }

    // Cập nhật số lượng sản phẩm
    public function updateQuantity()
    {
        $this->requireAuthAjax();

        $itemId   = $_POST['item_id'] ?? null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        if (!$itemId || $quantity <= 0) {
            http_response_code(400);
            return $this->json(['error' => 'Thiếu dữ liệu']);
        }

        $this->cartItemModel->updateQuantity($itemId, $quantity);
        return $this->json(['success' => true]);
    }

    // Đếm tổng sản phẩm trong giỏ
    public function count()
    {
        $accountId = $_SESSION['account_id'] ?? null;
        if (!$accountId) {
            return $this->json(['count' => 0]);
        }

        $user = $this->userModel->findByAccountId($accountId);
        if (!$user) {
            return $this->json(['count' => 0]);
        }

        $userId = $user['id'];
        $cart = $this->cartModel->getCartByUser($userId);
        if (!$cart) {
            return $this->json(['count' => 0]);
        }

        $total = $this->cartItemModel->getTotalQuantity($cart['id']);
        $display = $total > 99 ? 99 : $total;

        return $this->json(['count' => $display]);
    }
}
