<?php
// Simple account page skeleton. Controller passes $account and $user (may be null).
/** @var array|null $account */
/** @var array|null $user */
$userEmail = $account['email'] ?? '';
$userName = $user['full_name'] ?? $account['full_name'] ?? '';
$userPhone = $user['phone'] ?? '';
?>

<div class="max-w-5xl mx-auto py-8 px-4">
    <div class="grid grid-cols-12 gap-6">
        <aside class="col-span-3 bg-white p-4 rounded shadow-sm">
            <h3 class="font-semibold text-gray-700 mb-4">Tài khoản của bạn</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="/account" class="text-green-700 hover:underline">Thông tin chung</a></li>
                <li><a href="/orders" class="text-green-700 hover:underline">Đơn hàng của tôi</a></li>
                <li><a href="/addresses" class="text-green-700 hover:underline">Địa chỉ</a></li>
                <li><a href="/account/logout" class="text-red-600 hover:underline">Đăng xuất</a></li>
            </ul>
        </aside>
        <main class="col-span-9 bg-white p-6 rounded shadow-sm">
            <h2 class="text-xl font-semibold mb-2">Xin chào, <?php echo htmlspecialchars($userName ?: $userEmail); ?></h2>
            <p class="text-sm text-gray-600 mb-4">Quản lý thông tin cá nhân, địa chỉ và đơn hàng của bạn.</p>

            <section class="mb-6">
                <h3 class="font-medium text-gray-800 mb-2">Thông tin liên hệ</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Email</div>
                        <div class="font-medium"><?php echo htmlspecialchars($userEmail); ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Số điện thoại</div>
                        <div class="font-medium"><?php echo htmlspecialchars($userPhone ?? ''); ?></div>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="font-medium text-gray-800 mb-2">Hoạt động gần đây</h3>
                <p class="text-sm text-gray-600">Không có hoạt động nào trong thời gian gần đây.</p>
            </section>
        </main>
    </div>
</div>
