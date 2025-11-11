<?php include __DIR__ . '/layouts/header.php'; ?>

<?php
// Xác định địa chỉ mặc định
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
<style>
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 5px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        opacity: 0;
        pointer-events: none;
        transform: translateY(-20px);
        transition: all 0.4s ease;
        font-weight: 500;
    }

    .toast.show {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    .toast.success {
        background-color: #16a34a;
    }

    .toast.error {
        background-color: #dc2626;
    }

    .toast svg {
        width: 20px;
        height: 20px;
    }
</style>
<form action="/checkout/placeOrder" method="POST">
    <div class="max-w-7xl mx-auto py-6 sm:py-8 md:py-10 px-4 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- CỘT TRÁI -->
            <div class="md:col-span-2 space-y-6">

                <!-- ĐỊA CHỈ NHẬN HÀNG -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        Địa chỉ nhận hàng
                    </h2>

                    <?php if (!empty($addresses)): ?>
                        <?php
                        $maskedPhone = $defaultAddress['phone'] ?? '';
                        if ($maskedPhone !== '' && strlen($maskedPhone) >= 7) {
                            $maskedPhone = substr($maskedPhone, 0, 4) . '****' . substr($maskedPhone, -3);
                        }

                        $fullAddress = trim(
                            ($defaultAddress['street'] ?? '') . ', ' .
                                ($defaultAddress['ward'] ?? '') . ', ' .
                                ($defaultAddress['district'] ?? '') . ', ' .
                                (($defaultAddress['city'] ?? '') ?: ($defaultAddress['province'] ?? ''))
                        );
                        ?>

                        <!-- Địa chỉ đang chọn -->
                        <div id="selected-address-container" class="flex justify-between items-start bg-green-50 border border-green-500 rounded-md p-4 transition-all duration-300">
                            <div id="selected-address">
                                <p class="font-semibold text-gray-800">
                                    <?= htmlspecialchars($defaultAddress['receiver_name']); ?> | <?= htmlspecialchars($maskedPhone); ?>
                                </p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($fullAddress); ?></p>
                            </div>
                            <a href="#" onclick="toggleAddressList(); return false;" class="text-green-700 hover:text-green-800 font-medium text-sm">
                                Thay đổi
                            </a>
                        </div>

                        <input type="hidden" id="address_id" name="address_id" value="<?= $defaultAddress['id']; ?>">

                        <!-- Danh sách địa chỉ -->
                        <div id="address-list" class="hidden mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3 shadow-sm">
                            <p class="text-green-700 font-semibold border-b border-gray-200 pb-2">
                                Thay đổi địa chỉ
                            </p>

                            <?php foreach ($addresses as $addr): ?>
                                <?php
                                $addrFull = trim(($addr['street'] ?? '') . ', ' . ($addr['ward'] ?? '') . ', ' . ($addr['district'] ?? '') . ', ' . (($addr['city'] ?? '') ?: ($addr['province'] ?? '')));
                                $masked = $addr['phone'];
                                if (strlen($masked) >= 7) {
                                    $masked = substr($masked, 0, 4) . '****' . substr($masked, -3);
                                }
                                $isCurrent = $addr['id'] == $defaultAddress['id'];
                                ?>
                                <div class="border <?= $isCurrent ? 'border-green-400 bg-green-50' : 'border-gray-200 bg-white hover:border-green-500 hover:bg-green-50' ?> rounded-md p-3 transition cursor-pointer"
                                    onclick="selectAddress(<?= htmlspecialchars(json_encode($addr), ENT_QUOTES, 'UTF-8'); ?>)">
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($addr['receiver_name']); ?> | <?= htmlspecialchars($masked); ?></p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($addrFull); ?></p>
                                    <?php if ($isCurrent): ?>
                                        <span class="inline-block mt-1 text-xs text-green-700 font-medium">Mặc định</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

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
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        Hình thức thanh toán
                    </h2>

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
                            foreach ($cartItems as $item):
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
                                            <p class="text-sm text-gray-500"><?= $item['quantity'] ?> x <?= number_format($item['price'], 0, ',', '.') ?>₫</p>
                                        </div>
                                    </div>
                                    <div class="text-right text-red-600 font-semibold"><?= number_format($subtotal, 0, ',', '.') ?>₫</div>
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

            <!-- CỘT PHẢI -->
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
                    <div class="flex justify-between"><span>Tạm tính (<?= $totalQuantity ?> sản phẩm)</span><span><?= number_format($subtotalTotal, 0, ',', '.') ?>₫</span></div>
                    <div class="flex justify-between"><span>Giảm giá</span><span>0₫</span></div>
                    <div class="flex justify-between"><span>Phí vận chuyển</span><span class="text-green-600 font-medium">Miễn phí</span></div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between font-semibold text-gray-900">
                        <span>Thành tiền (Đã VAT)</span><span class="text-red-600 font-bold"><?= number_format($subtotalTotal, 0, ',', '.') ?>₫</span>
                    </div>
                </div>

                <input type="hidden" name="total_amount" value="<?= $subtotalTotal ?>">

                <button type="submit" class="mt-6 w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-md transition">
                    Đặt hàng
                </button>
            </aside>
        </div>
    </div>
</form>
<div id="toast" class="toast"></div>
<!-- message -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('toast');

        const showToast = (msg, type = 'info') => {
            toast.innerHTML = `
            ${type==='success' ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>' :
            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'}
            ${msg}
             `;
            toast.className = 'toast show ' + type;
            setTimeout(() => toast.className = 'toast', 3000);
        };
        const headerHeight = document.querySelector('header')?.offsetHeight || 0;
        toast.style.top = (headerHeight + 20) + 'px';
        // Hiển thị message từ session PHP
        <?php if (!empty($_SESSION['success'])): ?>
            showToast("<?= $_SESSION['success'] ?>", 'success');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            showToast("<?= $_SESSION['error'] ?>", 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('toggle-items');
        if (btn) {
            const hiddenItems = document.querySelectorAll('.cart-item.hidden');
            btn.addEventListener('click', () => {
                const isHidden = hiddenItems[0].classList.contains('hidden');
                hiddenItems.forEach(el => el.classList.toggle('hidden'));
                btn.textContent = isHidden ? 'Thu gọn' : `Xem thêm ${hiddenItems.length} sản phẩm`;
            });
        }

        const form = document.querySelector('form[action="/checkout/placeOrder"]');
        form.addEventListener('submit', e => {
            if (!confirm("Xác nhận đặt hàng?\n\nVui lòng kiểm tra kỹ thông tin trước khi xác nhận.")) {
                e.preventDefault();
            }
        });
    });

    function toggleAddressList() {
        const list = document.getElementById('address-list');
        const container = document.getElementById('selected-address-container');
        const isHidden = list.classList.contains('hidden');

        list.classList.toggle('hidden');
        // Làm mờ khối địa chỉ hiện tại khi danh sách mở
        if (isHidden) {
            container.classList.remove('bg-green-50', 'border-green-500');
            container.classList.add('bg-gray-50', 'border-gray-200');
        } else {
            container.classList.remove('bg-gray-50', 'border-gray-200');
            container.classList.add('bg-green-50', 'border-green-500');
        }
    }

    function selectAddress(address) {
        const selected = document.getElementById('selected-address');
        const maskedPhone = address.phone.length >= 7 ?
            address.phone.slice(0, 4) + '****' + address.phone.slice(-3) :
            address.phone;
        const fullAddr = [address.street, address.ward, address.district, address.city || address.province]
            .filter(Boolean).join(', ');
        selected.innerHTML = `
        <p class="font-semibold text-gray-800">${address.receiver_name} | ${maskedPhone}</p>
        <p class="text-sm text-gray-600">${fullAddr}</p>`;

        document.getElementById('address_id').value = address.id;

        // Đóng danh sách và làm nổi lại khối chính
        const list = document.getElementById('address-list');
        list.classList.add('hidden');
        const container = document.getElementById('selected-address-container');
        container.classList.remove('bg-gray-50', 'border-gray-200');
        container.classList.add('bg-green-50', 'border-green-500');
    }
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>