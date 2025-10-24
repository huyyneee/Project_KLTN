<?php include __DIR__ . '/layouts/header.php'; ?>
<div class="container" style="max-width:700px;margin:40px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 0 15px rgba(0,0,0,0.1);">
    <h1 style="color:#28a745;text-align:center;"> Thanh toán thành công!</h1>
    <p style="text-align:center;">Cảm ơn bạn đã mua hàng. Dưới đây là thông tin đơn hàng:</p>

    <hr>

    <h4>Mã đơn: <?= htmlspecialchars($order['order_code']) ?></h4>
    <p><b>Ngày đặt:</b> <?= htmlspecialchars($order['created_at']) ?></p>
    <p><b>Địa chỉ giao hàng:</b> <?= htmlspecialchars($order['shipping_address']) ?></p>

    <table style="width:100%;border-collapse:collapse;margin-top:20px;">
        <thead>
            <tr style="background:#f1f1f1;">
                <th style="padding:10px;border:1px solid #ddd;">Sản phẩm</th>
                <th style="padding:10px;border:1px solid #ddd;">Số lượng</th>
                <th style="padding:10px;border:1px solid #ddd;">Giá</th>
                <th style="padding:10px;border:1px solid #ddd;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td style="padding:10px;border:1px solid #ddd;"><?= htmlspecialchars($it['product_name'] ?? '') ?></td>
                    <td style="padding:10px;border:1px solid #ddd;text-align:center;"><?= $it['quantity'] ?></td>
                    <td style="padding:10px;border:1px solid #ddd;text-align:right;"><?= number_format($it['price']) ?>₫</td>
                    <td style="padding:10px;border:1px solid #ddd;text-align:right;"><?= number_format($it['price'] * $it['quantity']) ?>₫</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3 style="text-align:right;margin-top:20px;">Tổng tiền: <?= number_format($order['total_amount']) ?>₫</h3>

    <div style="text-align:center;margin-top:30px;">
        <a href="/" style="background:#007bff;color:white;padding:12px 25px;border-radius:8px;text-decoration:none;">Quay về trang chủ</a>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>