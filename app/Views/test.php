<?php
// app/Views/test.php
// View to display categories fetched by TestController
/** @var array $categories */
/** @var string|null $error */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test - Categories</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:Segoe UI,Arial;padding:20px;color:#222}
        table{border-collapse:collapse;width:100%;max-width:1000px}
        table th,table td{border:1px solid #ddd;padding:8px;text-align:left}
        table th{background:#f7f7f7}
        .muted{color:#666}
        pre{background:#f5f5f5;padding:10px;border-radius:6px;overflow:auto}
    </style>
</head>
<body>
    <h1>Test: categories</h1>

    <?php if (!empty($error)): ?>
        <div style="color:crimson;margin-bottom:16px">
            Lỗi khi lấy dữ liệu: <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($categories) && is_array($categories)): ?>
        <p>Tìm thấy <strong><?php echo count($categories); ?></strong> category(s):</p>
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Description</th><th>Created At</th><th>Updated At</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($c['name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($c['description'] ?? ''); ?></td>
                        <td class="muted"><?php echo htmlspecialchars($c['created_at'] ?? ''); ?></td>
                        <td class="muted"><?php echo htmlspecialchars($c['updated_at'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Không tìm thấy category nào hoặc bảng rỗng.</p>
    <?php endif; ?>

    <?php if (!empty($category)): ?>
        <hr>
        <h2>Chi tiết category id=<?php echo htmlspecialchars($category['id'] ?? ''); ?></h2>
        <p><strong>Tên:</strong> <?php echo htmlspecialchars($category['name'] ?? ''); ?></p>
        <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($category['description'] ?? '')); ?></p>

        <h3>Sản phẩm thuộc category</h3>
        <?php if (!empty($categoryProducts) && is_array($categoryProducts)): ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Price</th><th>Created At</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($categoryProducts as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($p['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($p['price'] ?? ''); ?></td>
                            <td class="muted"><?php echo htmlspecialchars($p['created_at'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Không có sản phẩm trong category này.</p>
        <?php endif; ?>
    <?php endif; ?>

    <hr>
    <h3>Raw output</h3>
    <pre><?php echo htmlspecialchars(json_encode($categories ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>

</body>
</html>
