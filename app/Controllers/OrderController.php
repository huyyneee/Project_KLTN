<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserModel;
use App\Models\AddressModel;
use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Core\Controller;

class OrderController extends Controller
{
    private $orderModel;
    private $orderItemModel;
    private $addressModel;
    private $cartModel;
    private $cartItemModel;
    private $userModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->addressModel = new AddressModel();
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;

        if (!$accountId) {
            header('Location: /login');
            exit;
        }

        // Lấy thông tin user
        $user = $this->userModel->findByAccountId($accountId);

        // Lấy danh sách địa chỉ
        $addresses = [];
        if ($user && isset($user['id'])) {
            $addresses = $this->addressModel->findByUserId($user['id']);
        }

        // Lấy giỏ hàng
        $cart = $this->cartModel->getCartByUser($accountId);
        $cartItems = $cart ? $this->cartItemModel->getItemsByCart($cart['id']) : [];

        // Tính tổng tiền
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $this->render('checkout', [
            'cartItems' => $cartItems,
            'addresses' => $addresses,
            'user' => $user,
            'total' => $total
        ]);
    }

    public function placeOrder()
    {
        $this->requireAuth();
        $accountId = $_SESSION['account_id'] ?? null;

        if (!$accountId) {
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->findByAccountId($accountId);
        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy người dùng.";
            header("Location: /checkout");
            exit;
        }

        // Lấy giỏ hàng
        $cart = $this->cartModel->getCartByUser($accountId);
        if (!$cart) {
            $_SESSION['error'] = "Không tìm thấy giỏ hàng.";
            header("Location: /cart");
            exit;
        }

        $cartItems = $this->cartItemModel->getItemsByCart($cart['id']);
        if (empty($cartItems)) {
            $_SESSION['error'] = "Giỏ hàng trống, không thể đặt hàng.";
            header("Location: /cart");
            exit;
        }

        // Lấy dữ liệu từ form
        $addressId = $_POST['address_id'] ?? null;
        $paymentMethod = strtolower($_POST['payment_method'] ?? 'cod');

        // Lấy địa chỉ giao hàng
        $address = $addressId ? $this->addressModel->findByIdAndUser($addressId, $user['id']) : null;
        $shippingAddress = '';
        if ($address) {
            $shippingAddress = trim(($address['street'] ?? '') . ', ' .
                ($address['ward'] ?? '') . ', ' .
                ($address['district'] ?? '') . ', ' .
                ($address['city'] ?? $address['province'] ?? ''));
        }

        // Tính tổng tiền
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Sinh mã đơn hàng duy nhất
        $orderCode = 'ORD' . strtoupper(dechex(time()) . substr(uniqid(), -3));

        // ⚡ Xử lý tùy theo hình thức thanh toán
        if ($paymentMethod === 'vnpay') {
            //Lưu dữ liệu tạm để xử lý VNPay sau
            $_SESSION['pending_order'] = [
                'user_id' => $user['id'],
                'address_id' => $addressId,
                'total' => $total,
                'cart_id' => $cart['id'],
                'order_code' => $orderCode,
                'shipping_address' => $shippingAddress
            ];

            // Chuyển hướng đến trang thanh toán VNPay

            //---------API VNPay ở đây---------//


        } elseif ($paymentMethod === 'cod') {
            // 👉 Thanh toán khi nhận hàng
            $orderId = $this->orderModel->insertOrder([
                'user_id' => $user['id'],
                'order_code' => $orderCode,
                'status' => 'pending',
                'total_amount' => $total,
                'shipping_address' => $shippingAddress,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Lưu chi tiết đơn hàng
            foreach ($cartItems as $item) {
                $this->orderItemModel->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Xóa giỏ hàng
            $this->cartModel->clearCart($user['id']);

            // Chuyển hướng
            $_SESSION['success'] = "Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.";
            header("Location: /cart");
            exit;
        } else {
            //  Trường hợp không hợp lệ
            $_SESSION['error'] = "Phương thức thanh toán không hợp lệ.";
            header("Location: /checkout");
            exit;
        }
    }
}
