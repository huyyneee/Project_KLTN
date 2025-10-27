<?php

/** @var array $orders */
/** @var array|null $user */
$userEmail = $account['email'] ?? '';
$userName = $user['full_name'] ?? $account['full_name'] ?? '';
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
            <div class="max-w-3xl mx-auto">
                <h1 class="text-2xl font-semibold mb-6">Đơn hàng của tôi</h1>

                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="mb-8 border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow bg-white">
                            <!-- Header đơn -->
                            <div class="flex justify-between items-center bg-gray-50 px-4 py-3 border-b">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-800">
                                        Mã đơn: <?= htmlspecialchars($order['order_code']) ?>
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Trạng thái:
                                        <span class="font-medium text-blue-600">
                                            <?= ($order['status'] === 'pending') ? 'Chưa giải quyết' : htmlspecialchars($order['status']) ?>
                                        </span>
                                    </p>
                                </div>

                                <a href="/account/order_detail?id=<?= $order['id'] ?>"
                                    class="flex items-center gap-1 text-blue-600 text-sm font-medium hover:text-blue-700 transition-colors">
                                    <span>Xem chi tiết</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <!-- Danh sách sản phẩm -->
                            <div class="divide-y">
                                <?php
                                $productCount = count($order['items']);
                                $visibleItems = array_slice($order['items'], 0, 2);
                                $hiddenItems = array_slice($order['items'], 2);
                                ?>

                                <?php foreach ($visibleItems as $item): ?>
                                    <div class="flex items-start gap-4 p-4">
                                        <img src="<?= htmlspecialchars($item['product_image']) ?>"
                                            alt="<?= htmlspecialchars($item['product_name']) ?>"
                                            class="w-20 h-20 object-cover rounded-md border" />
                                        <div class="flex-1">
                                            <p class="text-gray-800 font-medium"><?= htmlspecialchars($item['product_name']) ?></p>
                                            <p class="text-gray-500 text-sm mt-1">
                                                <?= $item['quantity'] ?> × <?= number_format($item['price']) ?> đ
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-gray-900 font-semibold">
                                                <?= number_format($item['quantity'] * $item['price']) ?> đ
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if (!empty($hiddenItems)): ?>
                                    <div class="hidden more-items">
                                        <?php foreach ($hiddenItems as $item): ?>
                                            <div class="flex items-start gap-4 p-4">
                                                <img src="<?= htmlspecialchars($item['product_image']) ?>"
                                                    alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                    class="w-20 h-20 object-cover rounded-md border" />
                                                <div class="flex-1">
                                                    <p class="text-gray-800 font-medium"><?= htmlspecialchars($item['product_name']) ?></p>
                                                    <p class="text-gray-500 text-sm mt-1">
                                                        <?= $item['quantity'] ?> × <?= number_format($item['price']) ?> đ
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-gray-900 font-semibold">
                                                        <?= number_format($item['quantity'] * $item['price']) ?> đ
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="text-center py-3 bg-gray-50 border-t">
                                        <button type="button"
                                            class="toggle-more text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            Xem thêm (<?= $productCount - 2 ?> sản phẩm)
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex justify-end items-center bg-gray-50 px-4 py-3 border-t">
                                <span class="text-sm">
                                    Tổng tiền (<?= count($order['items']) ?> sản phẩm):
                                    <span class="text-orange-500 font-bold text-base">
                                        <?= number_format($order['total_amount']) ?> đ
                                    </span>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-16 bg-gray-50 rounded-2xl shadow-inner">
                        <div class="bg-green-100 text-gray-600 p-6 rounded-full mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8h13.2M7 13l.4-2M10 21a1 1 0 11-2 0 1 1 0 012 0zm8 0a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-lg font-medium mb-4">Bạn chưa có đơn hàng nào</p>
                        <a href="/"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-full text-sm font-semibold shadow-md transition-all duration-200 hover:shadow-lg mb-8">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                <?php endif; ?>

            </div>

        </main>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-more').forEach(btn => {
            const parent = btn.closest('.divide-y');
            const hiddenSection = parent.querySelector('.more-items');

            // Lưu text gốc một lần duy nhất
            const originalText = btn.textContent;

            btn.addEventListener('click', () => {
                const isHidden = hiddenSection.classList.contains('hidden');
                hiddenSection.classList.toggle('hidden');

                // Đổi text khi mở/đóng
                btn.textContent = isHidden ? 'Thu gọn' : originalText;
            });
        });
    });
</script>


<?php include __DIR__ . '/../layouts/footer.php'; ?>