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
            <div class="w-full max-w-md border border-gray-300 rounded-md p-6 shadow">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold">Thay đổi mật khẩu</h2>
                    <button
                        type="button"
                        onclick="window.location.href='/account/edit'"
                        class="inline-flex items-center bg-gray-100 hover:bg-gray-400 text-gray-700 px-3 py-1 rounded-md text-sm transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Quay lại
                    </button>
                </div>
                <?php if (!empty($error)) : ?>
                    <div class="mb-4 text-red-600  rounded-md p-2">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)) : ?>
                    <div class="mb-4 text-green-700 rounded-md p-2">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="/account/change-password">
                    <div class="mb-4">
                        <label for="current-password" class="block text-gray-700 mb-1">Mật khẩu hiện tại:</label>
                        <input
                            type="password"
                            id="current-password"
                            name="current-password"
                            placeholder="Nhập mật khẩu cũ"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent" />
                    </div>
                    <div class="mb-4">
                        <label for="new-password" class="block text-gray-700 mb-1">Mật khẩu mới:</label>
                        <input
                            type="password"
                            id="new-password"
                            name="new-password"
                            placeholder="Nhập mật khẩu (ít nhất 8 ký tự, tối đa 32)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent" />
                    </div>
                    <div class="mb-6">
                        <label for="confirm-password" class="block text-gray-700 mb-1">Nhập lại:</label>
                        <input
                            type="password"
                            id="confirm-password"
                            name="confirm-password"
                            placeholder="Nhập lại mật khẩu mới"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent" />
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-md transition-colors">
                        Cập nhật
                    </button>
                </form>
            </div>
        </main>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>