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
            // Lưu đơn hàng tạm vào session
            $_SESSION['pending_order'] = [
                'user_id' => $user['id'],
                'address_id' => $addressId,
                'total' => $total,
                'cart_id' => $cart['id'],
                'order_code' => $orderCode,
                'shipping_address' => $shippingAddress
            ];

            // ===== Cấu hình VNPay =====
            date_default_timezone_set('Asia/Ho_Chi_Minh');

            $vnp_TmnCode = "KXMSLKF7";
            $vnp_HashSecret = "J4G1LA2VT83R0Y9PRCHZ610R5JA3204E";
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = "http://localhost:8000/checkout/vnpayReturn";

            $vnp_TxnRef = $orderCode;
            $vnp_OrderInfo = "Thanh toán đơn hàng #" . $orderCode;
            $vnp_Amount = $total * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => "billpayment",
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            ];

            ksort($inputData);
            $query = "";
            $hashdata = "";
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            header('Location: ' . $vnp_Url);
            exit;
            //  Thanh toán khi nhận hàng
        } elseif ($paymentMethod === 'cod') {
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
            $_SESSION['last_order_id'] = $orderId;
            header("Location: /receipt");
            exit;
        } else {
            $_SESSION['error'] = "Phương thức thanh toán không hợp lệ.";
            header("Location: /checkout");
            exit;
        }
    }
    public function vnpayReturn()
    {
        $vnp_HashSecret = "J4G1LA2VT83R0Y9PRCHZ610R5JA3204E";

        // Lọc dữ liệu từ VNPay trả về
        $inputData = [];
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            $hashData .= ($i ? '&' : '') . urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Xác thực chữ ký
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                // Thanh toán thành công
                $pending = $_SESSION['pending_order'] ?? null;

                if ($pending) {
                    // Lưu đơn hàng
                    $orderId = $this->orderModel->insertOrder([
                        'user_id' => $pending['user_id'],
                        'order_code' => $pending['order_code'],
                        'status' => 'paid',
                        'total_amount' => $pending['total'],
                        'shipping_address' => $pending['shipping_address'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    // Lưu chi tiết sản phẩm
                    $cartItems = $this->cartItemModel->getItemsByCart($pending['cart_id']);
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
                    $this->cartModel->clearCart($pending['user_id']);
                    unset($_SESSION['pending_order']);

                    // Lưu lại orderId để dùng ở trang receipt
                    $_SESSION['last_order_id'] = $orderId;
                }

                // Chuyển hướng sang trang biên lai
                header('Location: /receipt');
                exit;
            } else {
                // Thanh toán thất bại
                $_SESSION['error'] = "Thanh toán thất bại";
                header("Location: /cart");
            }
        } else {
            echo "<h3 style='color:orange;text-align:center;'>Chữ ký VNPay không hợp lệ!</h3>";
        }
    }

    public function receipt()
    {
        $orderId = $_SESSION['last_order_id'] ?? null;

        if (!$orderId) {
            header('Location: /');
            exit;
        }

        $order = $this->orderModel->findById($orderId);
        $items = $this->orderItemModel->getItemsByOrder($orderId);

        $this->render('/receipt', [
            'order' => $order,
            'items' => $items
        ]);
    }
}
