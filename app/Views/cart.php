<?php

/** @var array|null $cart */
/** @var array $items */
require_once __DIR__ . '/layouts/header.php';
?>

<style>
    .cart-page {
        max-width: 90%;
        width: 95%;
        margin: 30px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .cart-page h2 {
        font-size: 26px;
        margin-bottom: 25px;
        color: #222;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16px;
    }

    .cart-table th,
    .cart-table td {
        padding: 14px 10px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .cart-table th {
        background: #f9f9f9;
        font-weight: 600;
    }

    .cart-table tr:hover {
        background-color: #f5f5f5;
    }

    .product-info {
        display: flex;
        align-items: center;
    }

    .product-info img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 10px;
    }

    .quantity-input {
        width: 60px;
        padding: 4px;
        text-align: center;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .update-btn {
        background-color: #1e40af;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 5px;
        font-size: 14px;
    }

    .update-btn:hover {
        background-color: #1d4ed8;
    }

    .remove-item {
        color: #dc3545;
        cursor: pointer;
        border: none;
        background: none;
        font-weight: bold;
        font-size: 16px;
    }

    .cart-summary {
        margin-top: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .cart-summary .total {
        font-size: 20px;
        font-weight: bold;
    }

    .cart-summary button {
        background-color: #ff6600;
        color: #fff;
        border: none;
        padding: 12px 25px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }

    .cart-summary button:hover {
        background-color: #e65c00;
    }

    .continue-shopping {
        display: inline-block;
        margin-top: 20px;
        color: #333;
        text-decoration: underline;
    }

    /* Toast */
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

<div class="cart-page">
    <h2>Giỏ hàng (<span id="cart-count"><?= count($items) ?></span> sản phẩm)</h2>

    <?php if (empty($items)): ?>
        <div class="flex flex-col items-center justify-center h-[60vh] text-center space-y-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.293 2.293A1 1 0 007 17h10m-4 0a1 1 0 100 2 1 1 0 000-2zm-6 0a1 1 0 100 2 1 1 0 000-2z" />
            </svg>
            <p class="text-gray-700 text-lg">Bạn chưa chọn sản phẩm.</p>
            <a href="/trang-chu" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <?php $total = 0;
                foreach ($items as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <tr data-item-id="<?= $item['id'] ?>">
                        <td>
                            <div class="product-info">
                                <img src="<?= $item['image'] ?? '/uploads/products/placeholder.svg' ?>">
                                <div><?= htmlspecialchars($item['productname']) ?></div>
                            </div>
                        </td>
                        <td class="price" data-value="<?= $item['price'] ?>"><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                        <td>
                            <div style="display:flex; align-items:center;">
                                <input type="number" class="quantity-input" min="1" value="<?= $item['quantity'] ?>">
                                <button class="update-btn">Cập nhật</button>
                            </div>
                        </td>
                        <td class="subtotal"><?= number_format($subtotal, 0, ',', '.') ?>₫</td>
                        <td><button class="remove-item">X</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <span class="total">Tạm tính: <span id="cart-total"><?= number_format($total, 0, ',', '.') ?>₫</span> (Đã bao gồm VAT)</span>
            <button onclick="location.href='/checkout'">Tiến hành đặt hàng</button>
        </div>

        <a href="/trang-chu" class="continue-shopping">Tiếp tục mua hàng</a>
    <?php endif; ?>
</div>

<div id="toast" class="toast"></div>

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

        const cartItems = document.getElementById('cart-items');

        const updateTotal = () => {
            let total = 0;
            cartItems.querySelectorAll('tr').forEach(row => {
                const subtotal = parseInt(row.querySelector('.subtotal').innerText.replace(/\D/g, ''));
                total += subtotal || 0;
            });
            document.getElementById('cart-total').innerText = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(total);
        }

        const updateCartCount = () => {
            document.getElementById('cart-count').innerText = cartItems.querySelectorAll('tr').length;
        }

        cartItems.addEventListener('click', e => {
            const row = e.target.closest('tr');
            if (!row) return;
            const itemId = row.dataset.itemId;
            const input = row.querySelector('.quantity-input');

            // Xóa sản phẩm
            if (e.target.classList.contains('remove-item')) {
                if (!confirm('Bạn có chắc muốn xóa sản phẩm?')) return;
                fetch('cart/remove', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `item_id=${itemId}`
                    })
                    .then(res => res.json()).then(data => {
                        if (data.success) {
                            row.remove();
                            updateTotal();
                            updateCartCount();
                            showToast('Đã xóa sản phẩm', 'success');
                            document.dispatchEvent(new CustomEvent('cartUpdated'));
                            if (cartItems.querySelectorAll('tr').length === 0) {
                                document.querySelector('.cart-page').innerHTML = `
                                <div class="flex flex-col items-center justify-center h-[60vh] text-center space-y-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.293 2.293A1 1 0 007 17h10m-4 0a1 1 0 100 2 1 1 0 000-2zm-6 0a1 1 0 100 2 1 1 0 000-2z"/>
                                    </svg>
                                    <p class="text-gray-700 text-lg">Bạn chưa chọn sản phẩm.</p>
                                    <a href="/trang-chu" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">Tiếp tục mua sắm</a>
                                </div>`;
                            }
                        }
                    });
            }

            // Cập nhật số lượng
            if (e.target.classList.contains('update-btn')) {
                const qty = Math.max(1, parseInt(input.value));
                input.value = qty;
                fetch('cart/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `item_id=${itemId}&quantity=${qty}`
                    })
                    .then(res => res.json()).then(data => {
                        if (data.success) {
                            const price = parseInt(row.querySelector('.price').dataset.value);
                            row.querySelector('.subtotal').innerText = new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(price * qty);
                            updateTotal();
                            showToast('Cập nhật số lượng thành công', 'success');
                            document.dispatchEvent(new CustomEvent('cartUpdated'));

                        }
                    });
            }
        });
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

<?php require_once __DIR__ . '/layouts/footer.php'; ?>