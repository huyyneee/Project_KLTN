<?php
// app/Views/test.php
// Simple view used by TestController to display accounts
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test - Accounts</title>
    <style>body{font-family:Segoe UI,Arial;padding:20px} pre{background:#f5f5f5;padding:10px;border-radius:6px}</style>
</head>
<body>
    <h1>Test: accounts</h1>
    <?php if (!empty($accounts) && is_array($accounts)): ?>
        <p>Found <?php echo count($accounts); ?> account(s):</p>
        <pre><?php print_r($accounts); ?></pre>
    <?php else: ?>
        <p>No accounts found or database connection failed.</p>
    <?php endif; ?>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test Model & Controller</title>
</head>
<body>
    <h2>Test lấy dữ liệu từ bảng accounts</h2>
    <?php
    if (isset($accounts)) {
        echo '<pre>';
        print_r($accounts);
        echo '</pre>';
    } else {
        echo 'Không có dữ liệu.';
    }
    ?>
</body>
</html>
