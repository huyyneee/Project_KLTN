<?php include __DIR__ . '/layouts/header.php'; ?>

<?php
// Định nghĩa địa chỉ mặc định
$defaultAddress = null;
if (!empty($addresses)) {
    foreach ($addresses as $addr) {
        if (!empty($addr['is_default'])) {
            $defaultAddress = $addr;
            break;
        }
    }
    if (!$defaultAddress) {
        $defaultAddress = $addresses[0];
    }
}
?>

<!-- Modal chọn địa chỉ -->
<div id="addressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-hidden h-full w-full hidden flex items-center justify-center" style="z-index: 9999;">
    <div class="p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Chọn địa chỉ nhận hàng</h3>
            <div class="space-y-3">
                <?php foreach ($addresses as $addr): ?>
                    <?php
                    $maskedPhone = $addr['phone'] ?? '';
                    if ($maskedPhone !== '' && strlen($maskedPhone) >= 7) {
                        $maskedPhone = substr($maskedPhone, 0, 4) . '****' . substr($maskedPhone, -3);
                    }
                    $fullAddress = trim(
                        ($addr['street'] ?? '') . ', ' .
                        ($addr['ward'] ?? '') . ', ' .
                        ($addr['district'] ?? '') . ', ' .
                        (($addr['city'] ?? '') ?: ($addr['province'] ?? ''))
                    );
                    $isSelected = ($addr['id'] == $defaultAddress['id']);
                    ?>
                    <label class="flex items-start space-x-3 border border-gray-200 rounded-md p-3 cursor-pointer hover:border-green-600 transition">
                        <input type="radio" name="selected_address" value="<?= $addr['id'] ?>" <?= $isSelected ? 'checked' : '' ?> class="mt-1 text-green-600 focus:ring-green-600">
                        <div>
                            <p class="font-semibold text-gray-800">
                                <?= htmlspecialchars($addr['receiver_name']); ?> | <?= htmlspecialchars($maskedPhone); ?>
                            </p>
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($fullAddress); ?></p>
                            <?php if (!empty($addr['is_default'])): ?>
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mt-1">Mặc định</span>
                            <?php endif; ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeAddressModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Hủy</button>
                <button type="button" onclick="updateAddress()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Cập nhật</button>
            </div>
        </div>
    </div>
</div>

<form action="/checkout/placeOrder" method="POST">

    <div class="max-w-7xl mx-auto py-6 sm:py-8 md:py-10 px-4 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">

                <!-- ĐỊA CHỈ NHẬN HÀNG -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Địa chỉ nhận hàng</h2>

                    <?php if (!empty($addresses)): ?>
                        <?php
                        // Mask số điện thoại
                        $maskedPhone = $defaultAddress['phone'] ?? '';
                        if ($maskedPhone !== '' && strlen($maskedPhone) >= 7) {
                            $maskedPhone = substr($maskedPhone, 0, 4) . '****' . substr($maskedPhone, -3);
                        }

                        // Ghép địa chỉ đầy đủ
                        $fullAddress = trim(
                            ($defaultAddress['street'] ?? '') . ', ' .
                                ($defaultAddress['ward'] ?? '') . ', ' .
                                ($defaultAddress['district'] ?? '') . ', ' .
                                (($defaultAddress['city'] ?? '') ?: ($defaultAddress['province'] ?? ''))
                        );
                        ?>
                        <div class="flex justify-between items-start bg-gray-50 rounded-md p-4">
                            <div>
                                <p class="font-semibold text-gray-800">
                                    <?= htmlspecialchars($defaultAddress['receiver_name']); ?> | <?= htmlspecialchars($maskedPhone); ?>
                                </p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($fullAddress); ?></p>
                            </div>
                            <a href="#" onclick="showAddressModal(); return false;" class="text-green-700 hover:text-green-800 font-medium text-sm">Thay đổi</a>
                        </div>
                        <!-- ✅ Hidden input để gửi id địa chỉ -->
                        <input type="hidden" name="address_id" value="<?= $defaultAddress['id']; ?>">
                    <?php else: ?>
                        <div class="text-center py-4 text-gray-600">
                            Bạn chưa có địa chỉ nhận hàng nào.
                            <br>
                            <a href="/account/address" class="text-green-700 hover:text-green-800 font-medium text-sm">Thêm địa chỉ mới</a>
                        </div>
                    <?php endif; ?>
                </section>


                <!-- HÌNH THỨC THANH TOÁN -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Hình thức thanh toán</h2>

                    <div class="flex space-x-4">
                        <label class="flex items-center space-x-3 border border-gray-200 rounded-md p-3 cursor-pointer hover:border-green-600 transition flex-1">
                            <input type="radio" name="payment_method" value="cod" checked class="text-green-600 focus:ring-green-600">
                            <img src="/assets/images/shipcod.png" alt="COD" class="w-6 h-6">
                            <span class="text-gray-800 text-sm">Thanh toán khi nhận hàng (COD)</span>
                        </label>
                        <label class="flex items-center space-x-3 border border-gray-200 rounded-md p-3 cursor-pointer hover:border-green-600 transition flex-1">
                            <input type="radio" name="payment_method" value="vnpay" class="text-green-600 focus:ring-green-600">
                            <img src="/assets/images/vnpay.png" alt="VNPay" class="w-6 h-6">
                            <span class="text-gray-800 text-sm">Thanh toán trực tuyến VNPay</span>
                        </label>
                    </div>
                </section>


                <!-- DANH SÁCH SẢN PHẨM -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Sản phẩm</h2>

                    <div class="divide-y divide-gray-100">
                        <?php if (!empty($cartItems)): ?>
                            <?php
                            $limit = 2;
                            $count = 0;
                            ?>
                            <?php foreach ($cartItems as $item):
                                $subtotal = $item['price'] * $item['quantity'];
                                $count++;
                            ?>
                                <div class="flex items-center justify-between py-4 cart-item <?= $count > $limit ? 'hidden' : '' ?>">
                                    <div class="flex items-center space-x-4">
                                        <img src="<?= htmlspecialchars($item['image'] ?? '/uploads/products/placeholder.svg') ?>"
                                            alt="<?= htmlspecialchars($item['productname']) ?>"
                                            class="w-16 h-16 rounded-md object-cover border">
                                        <div>
                                            <p class="font-medium text-gray-800"><?= htmlspecialchars($item['productname']) ?></p>
                                            <p class="text-sm text-gray-500">
                                                <?= $item['quantity'] ?> x <?= number_format($item['price'], 0, ',', '.') ?>₫
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right text-red-600 font-semibold">
                                        <?= number_format($subtotal, 0, ',', '.') ?>₫
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (count($cartItems) > $limit): ?>
                                <button id="toggle-items" type="button" class="mt-2 text-sm text-green-700 hover:underline">
                                    Xem thêm <?= count($cartItems) - $limit ?> sản phẩm
                                </button>
                            <?php endif; ?>

                        <?php else: ?>
                            <p class="text-gray-500 text-sm">Chưa có sản phẩm trong giỏ.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- ĐƠN HÀNG TỔNG QUAN -->
             
            <aside class="md:col-span-1 bg-white rounded-lg shadow-sm p-6 h-fit self-start">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex justify-between items-center">
                    Đơn hàng
                    <a href="/cart" class="text-green-700 text-sm hover:underline">Thay đổi</a>
                </h2>

                <?php
                $totalQuantity = 0;
                $subtotalTotal = 0;
                if (!empty($cartItems)):
                    foreach ($cartItems as $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $subtotalTotal += $subtotal;
                        $totalQuantity += $item['quantity'];
                    endforeach;
                endif;
                ?>

                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <span>Tạm tính (<?= $totalQuantity ?> sản phẩm)</span>
                        <span><?= number_format($subtotalTotal, 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Giảm giá</span>
                        <span>0₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span class="text-green-600 font-medium">Miễn phí</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between font-semibold text-gray-900">
                        <span>Thành tiền (Đã VAT)</span>
                        <span class="text-red-600 font-bold"><?= number_format($subtotalTotal, 0, ',', '.') ?>₫</span>
                    </div>
                </div>

                <!--Gửi tổng tiền -->
                <input type="hidden" name="total_amount" value="<?= $subtotalTotal ?>">

                <button type="submit" class="mt-6 w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-md transition">
                    Đặt hàng
                </button>
            </aside>
        </div>
    </div>
</form>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('toggle-items');
        if (!btn) return;

        const hiddenItems = document.querySelectorAll('.cart-item.hidden');
        const limit = hiddenItems.length;

        btn.addEventListener('click', () => {
            const isHidden = hiddenItems[0].classList.contains('hidden');

            hiddenItems.forEach(el => {
                el.classList.toggle('hidden');
            });

            btn.textContent = isHidden ?
                'Thu gọn' :
                `Xem thêm ${limit} sản phẩm`;
        });
    });
    // Xác nhận trước khi đặt hàng
    document.querySelector('form[action="/checkout/placeOrder"]').addEventListener('submit', function(e) {
        const confirmOrder = confirm("Đơn hàng của bạn sẽ được xử lý ngay sau khi xác nhận. Tiếp tục?");
        if (!confirmOrder) {
            e.preventDefault(); // Dừng form nếu bấm Cancel
        }
    });

    function showAddressModal() {
        document.getElementById('addressModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddressModal() {
        document.getElementById('addressModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function updateAddress() {
        const selectedAddress = document.querySelector('input[name="selected_address"]:checked');
        if (!selectedAddress) {
            alert('Vui lòng chọn một địa chỉ.');
            return;
        }

        // Cập nhật địa chỉ trên giao diện
        const addressId = selectedAddress.value;
        const addressLabel = selectedAddress.closest('label');
        const receiverName = addressLabel.querySelector('p.font-semibold').textContent;
        const phone = receiverName.split(' | ')[1];
        const fullAddress = addressLabel.querySelector('p.text-sm').textContent;

        document.querySelector('input[name="address_id"]').value = addressId;
        document.querySelector('.flex.justify-between.items-start p.font-semibold').textContent = receiverName;
        document.querySelector('.flex.justify-between.items-start p.text-sm').textContent = fullAddress;

        closeAddressModal();
    }
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>