<?php

/** @var array $orders */
/** @var array|null $user */
$userEmail = $account['email'] ?? '';
$userName = $account['full_name'] ?? $account['full_name'] ?? '';
?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-7xl mx-auto py-6 sm:py-8 md:py-10 px-4 bg-gray-50">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6">
        <!-- Sidebar trái -->
        <aside class="md:col-span-3 bg-white rounded-lg shadow-sm p-4 md:p-5">
            <?php include __DIR__ . '/layouts/navigation.php'; ?>
        </aside>
        <!-- Nội dung chính -->
        <main class="md:col-span-9 bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <h1 class="text-xl font-semibold text-gray-800">Chi tiết đơn hàng</h1>
                <button
                    onclick="window.history.back()"
                    class="flex items-center gap-1 text-gray-600 text-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Quay lại</span>
                </button>
            </div>
            <!-- Thông tin đơn -->
            <div class="flex justify-between flex-wrap">
                <div class="space-y-1 text-sm text-gray-700">
                    <p><span class="font-medium">Mã đơn hàng:</span> #<?= htmlspecialchars($order['order_code']) ?></p>
                    <p><span class="font-medium">Ngày đặt:</span> <?= date('d/m/Y, H:i', strtotime($order['created_at'])) ?></p>
                    <div class="mt-3">
                        <p class="font-medium mb-1">Địa chỉ nhận hàng</p>
                        <p>
                            <?= htmlspecialchars($order['receiver_name'] ?? $user['full_name'] ?? '') ?> -
                            <?= htmlspecialchars(
                                isset($order['receiver_phone'])
                                    ? substr($order['receiver_phone'], 0, 3)
                                    . str_repeat('*', strlen($order['receiver_phone']) - 6)
                                    . substr($order['receiver_phone'], -3)
                                    : ''
                            ) ?>
                        </p>
                        <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                    </div>
                </div>
                <!-- Hình thức thanh toán -->
                <div class="text-sm text-gray-700 w-full sm:w-1/3 mt-4 sm:mt-0">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-medium">Hình thức thanh toán</span>
                            <span class="text-green-700 font-semibold">
                                <?= htmlspecialchars(
                                    $order['payment_method'] === 'cod'
                                        ? 'Thanh toán khi nhận hàng'
                                        : ($order['payment_method'] === 'vnpay'
                                            ? 'Thanh toán qua VNPAY'
                                            : ucfirst($order['payment_method'] ?? '')
                                        )
                                ) ?>
                            </span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 space-y-1 text-[13px]">
                            <div class="flex justify-between py-0.5">
                                <span>Tạm tính (<?= count($items) ?> sản phẩm)</span>
                                <span><?= number_format($subtotal) ?> ₫</span>
                            </div>
                            <div class="flex justify-between py-0.5">
                                <span>Phí vận chuyển</span>
                                <span>0 ₫</span>
                            </div>
                            <div class="flex justify-between font-semibold text-[15px] text-gray-800">
                                <span>Thành tiền (Đã VAT)</span>
                                <span class="text-orange-500"><?= number_format($subtotal) ?> ₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex justify-between items-center text-sm text-gray-700 p-4 border-b bg-gray-50">
                        <div class="flex items-center gap-2">
                            <?php if ($order['status'] === 'pending'): ?>
                                <span class="text-blue-600 text-sm border border-blue-400 bg-blue-50 px-2.5 py-1 rounded font-semibold">
                                    Chờ xác nhận
                                </span>
                            <?php elseif ($order['status'] === 'paid'): ?>
                                <span class="text-purple-600 text-sm border border-purple-400 bg-purple-50 px-2.5 py-1 rounded font-semibold">
                                    Đã xác nhận
                                </span>
                            <?php elseif ($order['status'] === 'shipped'): ?>
                                <span class="text-yellow-400 text-sm border border-yellow-400 bg-yellow-50 px-2.5 py-1 rounded font-semibold">
                                    Đang giao hàng
                                </span>
                            <?php elseif ($order['status'] === 'completed'): ?>
                                <span class="text-green-500 text-sm border border-green-400 bg-green-50 px-2.5 py-1 rounded font-semibold">
                                    Hoàn tất
                                </span>
                            <?php elseif ($order['status'] === 'cancelled'): ?>
                                <span class="text-red-600 text-sm border border-red-400 bg-red-50 px-2.5 py-1 rounded font-semibold">
                                    Đã hủy
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Danh sách sản phẩm -->
                    <div class="divide-y divide-gray-200">
                        <?php if (!empty($items)): ?>
                            <?php
                            $visibleLimit = 2; // Hiển thị 2 sản phẩm đầu
                            $totalItems = count($items);
                            ?>
                            <?php foreach ($items as $index => $item): ?>
                                <div class="product-item <?= $index >= $visibleLimit ? 'hidden extra-item' : '' ?> 
                                    flex items-start gap-4 p-4 hover:bg-gray-50 transition">
                                    <img src="<?= htmlspecialchars($item['image_url'] ?? '/public/images/no-image.jpg') ?>"
                                        alt="<?= htmlspecialchars($item['product_name']) ?>"
                                        class="w-20 h-20 object-cover rounded-md border flex-shrink-0 shadow-sm" />
                                    <div class="flex-1">
                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($item['product_name']) ?></p>
                                        <p class="text-gray-500 text-sm mt-1">
                                            <?= $item['quantity'] ?> × <?= number_format($item['price']) ?> đ
                                        </p>
                                        <p class="text-sm text-gray-500 mt-0.5">
                                            <?= htmlspecialchars($item['netweightpercarton'] ?? '') ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-900 font-semibold">
                                            <?= number_format($item['quantity'] * $item['price']) ?> đ
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($totalItems > $visibleLimit): ?>
                                <div class="text-center py-3 bg-gray-50 border-t">
                                    <button id="toggleItemsBtn" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Xem thêm (<?= $totalItems - $visibleLimit ?>)
                                    </button>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500 py-6">
                                Không có sản phẩm trong đơn hàng này.
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($order['status'] === 'pending' || $order['status'] === 'paid'): ?>
                        <div class="flex justify-end p-4 border-t bg-gray-50">
                            <button
                                class="cancel-order-btn px-5 py-2 text-sm font-medium text-red-600 border border-red-500 rounded-lg 
                            hover:bg-red-50 hover:border-red-600 hover:text-red-700 
                            transition-colors duration-200 ease-in-out"
                                data-id="<?= $order['id'] ?>">
                                Hủy đơn hàng
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById("toggleItemsBtn");
        if (btn) {
            btn.addEventListener("click", function() {
                const extraItems = document.querySelectorAll(".extra-item");
                const isHidden = extraItems[0].classList.contains("hidden");
                extraItems.forEach(item => item.classList.toggle("hidden"));
                btn.textContent = isHidden ? "Thu gọn" : "Xem thêm (<?= $totalItems - $visibleLimit ?>)";
            });
        }
        // Hủy đơn hàng
        document.querySelectorAll('.cancel-order-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.dataset.id;
                if (!confirm('Bạn có chắc muốn hủy đơn hàng này không?')) return;

                fetch('/account/cancel-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'order_id=' + encodeURIComponent(orderId)
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) location.reload();
                    })
                    .catch(() => alert('Lỗi hệ thống, vui lòng thử lại sau.'));
            });
        });
    });
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>