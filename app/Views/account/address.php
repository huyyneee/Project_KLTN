<?php
/** @var array|null $account */
/** @var array|null $user */
/** @var array|null $addresses Array of user's addresses */

$userEmail = $account['email'] ?? '';
$userName = $user['full_name'] ?? $account['full_name'] ?? '';
$hasAddresses = !empty($addresses);
$editingAddress = $editing ?? null;
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
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Sổ địa chỉ</h1>

        <?php if (isset($_SESSION['errors'])): ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
          <?php foreach ($_SESSION['errors'] as $e): ?>
            <p class="text-sm"><?php echo htmlspecialchars($e); ?></p>
          <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['errors']); endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
          <p class="text-sm"><?php echo htmlspecialchars($_SESSION['success']); ?></p>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (!$hasAddresses): ?>
        <!-- Hiển thị khi chưa có địa chỉ -->
        <div class="text-center py-8">
          <div class="mb-4">
            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <p class="text-gray-600 mb-6">Bạn chưa có địa chỉ nhận hàng nào</p>
          <button id="addAddressBtn" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
            + Thêm địa chỉ mới
          </button>
        </div>
        <?php endif; ?>

        <?php if ($hasAddresses): ?>
        <div class="flex justify-end mb-4">
          <?php if (empty($editingAddress)): ?>
          <button id="addAddressBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">+ Thêm địa chỉ mới</button>
          <?php endif; ?>
        </div>
        <!-- Danh sách địa chỉ -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <?php foreach ($addresses as $addr): ?>
            <?php
              $maskedPhone = $addr['phone'] ?? '';
              if ($maskedPhone !== '' && strlen($maskedPhone) >= 7) {
                  $maskedPhone = substr($maskedPhone, 0, 3) . '*****' . substr($maskedPhone, -3);
              }
              $fullAddress = trim(($addr['street'] ?? '') . ', ' . ($addr['ward'] ?? '') . ', ' . ($addr['district'] ?? '') . ', ' . (($addr['city'] ?? '') ?: ($addr['province'] ?? '')));
            ?>
            <div class="border rounded-md p-4 <?php echo ($addr['is_default'] ?? 0) ? 'border-green-500' : 'border-gray-200'; ?>">
              <div class="flex items-center justify-between mb-2">
                <div class="font-semibold text-green-800">
                  <?php echo htmlspecialchars($addr['receiver_name'] ?? ''); ?> - <?php echo htmlspecialchars($maskedPhone); ?>
                  <?php if (!empty($addr['is_default'])): ?>
                    <span class="ml-2 text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded">Mặc định</span>
                  <?php endif; ?>
                </div>
                <div class="flex items-center space-x-3">
                  <a href="/account/address/edit?id=<?php echo (int)$addr['id']; ?>" class="text-sm text-green-700 hover:underline">Chỉnh sửa</a>
                  <form action="/account/address/delete" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">
                    <input type="hidden" name="id" value="<?php echo (int)$addr['id']; ?>">
                    <button type="submit" class="text-sm text-red-600 hover:underline">Xóa</button>
                  </form>
                </div>
              </div>
              <div class="text-sm text-gray-700"><?php echo htmlspecialchars($fullAddress); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Form thêm/cập nhật địa chỉ -->
        <?php
          $formVisible = $editingAddress ? true : !$hasAddresses;
          $action = $editingAddress ? '/account/address/update' : '/account/address/add';
          $btnText = $editingAddress ? 'Lưu thay đổi' : 'Cập nhật';
        ?>
        <div id="addressForm" class="<?php echo $formVisible ? '' : 'hidden'; ?>">
          <form action="<?php echo $action; ?>" method="POST" class="space-y-6">
            <?php if ($editingAddress): ?>
              <input type="hidden" name="id" value="<?php echo (int)$editingAddress['id']; ?>">
            <?php endif; ?>
            <!-- Tên -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên</label>
              <input type="text" 
                     id="name" 
                     name="name" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent"
                     value="<?php echo htmlspecialchars($editingAddress['receiver_name'] ?? ''); ?>"
                     required>
            </div>

            <!-- Số điện thoại -->
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
              <input type="tel" 
                     id="phone" 
                     name="phone" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent"
                     value="<?php echo htmlspecialchars($editingAddress['phone'] ?? ''); ?>"
                     required>
            </div>

            <!-- Tỉnh/Thành phố -->
            <div>
              <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành phố</label>
              <input type="text" 
                     id="province" 
                     name="province" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent"
                     value="<?php echo htmlspecialchars(($editingAddress['city'] ?? '') ?: ($editingAddress['province'] ?? '')); ?>"
                     required>
            </div>

            <!-- Quận/Huyện -->
            <div>
              <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện</label>
              <input type="text" 
                     id="district" 
                     name="district" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent"
                     value="<?php echo htmlspecialchars($editingAddress['district'] ?? ''); ?>"
                     required>
            </div>

            <!-- Phường/Xã -->
            <div>
              <label for="ward" class="block text-sm font-medium text-gray-700 mb-1">Phường/Xã</label>
              <input type="text" 
                     id="ward" 
                     name="ward" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent"
                     value="<?php echo htmlspecialchars($editingAddress['ward'] ?? ''); ?>"
                     required>
            </div>

            <!-- Địa chỉ nhận hàng -->
            <div>
              <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ nhận hàng</label>
              <input type="text" 
                     id="address" 
                     name="address" 
                     placeholder="Số nhà + tên đường" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent"
                     value="<?php echo htmlspecialchars($editingAddress['street'] ?? ''); ?>"
                     required>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
              <button type="button" 
                      id="cancelBtn"
                      class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                Hủy
              </button>
              <button type="submit" 
                      class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <?php echo $btnText; ?>
              </button>
            </div>
          </form>
        </div>

      </div>
    </main>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const addressForm = document.getElementById('addressForm');
  const addAddressBtn = document.getElementById('addAddressBtn');
  const cancelBtn = document.getElementById('cancelBtn');

  if (addAddressBtn) {
    addAddressBtn.addEventListener('click', function() {
      addressForm.classList.remove('hidden');
      if (addAddressBtn && addAddressBtn.parentElement) {
        addAddressBtn.parentElement.classList.add('hidden');
      }
    });
  }

  if (cancelBtn) {
    cancelBtn.addEventListener('click', function() {
      addressForm.classList.add('hidden');
      if (addAddressBtn && addAddressBtn.parentElement) {
        addAddressBtn.parentElement.classList.remove('hidden');
      }
    });
  }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
