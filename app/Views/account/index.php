<?php
/** @var array|null $account */
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

    <!-- Nội dung phải -->
    <main class="md:col-span-9 bg-white rounded-lg shadow-sm p-4 md:p-6">
      <!-- Thông tin tài khoản -->
      <section class="mb-6 md:mb-8 border-b border-gray-200 pb-4 md:pb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 space-y-2 sm:space-y-0">
          <h2 class="text-base md:text-lg font-semibold text-gray-800">Thông tin tài khoản</h2>
          <a href="/account/edit" class="text-xs md:text-sm text-gray-600 hover:underline hover:text-green-800 transition-colors">Chỉnh sửa</a>
        </div>
        <div class="space-y-2">
          <p class="font-medium text-gray-800 text-sm md:text-base"><?php echo htmlspecialchars($userName ?: ''); ?></p>
          <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($userEmail ?: ''); ?></p>
        </div>
      </section>

      <!-- Số địa chỉ -->
      <section>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 space-y-2 sm:space-y-0">
          <h2 class="text-base md:text-lg font-semibold text-gray-800">Số địa chỉ</h2>
          <a href="/addresses" class="text-xs md:text-sm text-gray-600 hover:underline hover:text-green-800 transition-colors">Quản lý sổ địa chỉ</a>
        </div>
        <p class="text-sm text-gray-600">Bạn chưa có địa chỉ nào được lưu.</p>
      </section>
    </main>
  </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
