<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Core\Controller;


class CartController extends Controller
{
    private $cartModel;
    private $cartItemModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
    }

    // Hiển thị trang giỏ hàng
    public function index()
    {
        $this->requireAuth();

        $userId = $_SESSION['account_id'] ?? null;
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
        $userId    = $_SESSION['account_id'] ?? null;
        $productId = $_POST['product_id'] ?? null;
        $quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $price     = isset($_POST['price']) ? (float)$_POST['price'] : 0;

        if (!$productId || $quantity <= 0) {
            http_response_code(400);
            $this->json(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        // Nếu chưa login, lưu tạm vào session và yêu cầu login
        if (!$userId) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['cart_pending'] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $price
            ];

            $this->json([
                'login_required' => true,
                'redirect' => '/login?return=/cart'
            ]);
            return;
        }

        // Lấy giỏ hàng hiện tại của user hoặc tạo mới
        $cart = $this->cartModel->getCartByUser($userId);
        $cartId = $cart ? $cart['id'] : $this->cartModel->createCart($userId);

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $existingItem = $this->cartItemModel->getItemByCartAndProduct($cartId, $productId);

        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            $this->cartItemModel->updateQuantity($existingItem['id'], $newQuantity);
        } else {
            $this->cartItemModel->addItem($cartId, $productId, $quantity, $price);
        }

        $this->json(['success' => true]);
    }


    // Xoá sản phẩm trong giỏ
    public function remove()
    {
        $this->requireAuthAjax();

        $itemId = $_POST['item_id'] ?? null;
        if (!$itemId) {
            http_response_code(400);
            $this->json(['error' => 'Thiếu dữ liệu']);
        }

        $this->cartItemModel->deleteItem($itemId);
        $this->json(['success' => true]);
    }

    // Cập nhật số lượng sản phẩm
    public function updateQuantity()
    {
        $this->requireAuthAjax();

        $itemId   = $_POST['item_id'] ?? null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        if (!$itemId || $quantity <= 0) {
            http_response_code(400);
            $this->json(['error' => 'Thiếu dữ liệu']);
        }

        $this->cartItemModel->updateQuantity($itemId, $quantity);
        $this->json(['success' => true]);
    }
    public function count()
    {
        $userId = $_SESSION['account_id'] ?? null;
        if (!$userId) {
            $this->json(['count' => 0]);
            return;
        }

        $cart = $this->cartModel->getCartByUser($userId);
        if (!$cart) {
            $this->json(['count' => 0]);
            return;
        }

        $total = $this->cartItemModel->getTotalQuantity($cart['id']);
        $display = $total > 99 ? 99 : $total;

        $this->json(['count' => $display]);
    }
}
