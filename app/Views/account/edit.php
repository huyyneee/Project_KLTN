<?php

/** @var array|null $account */
/** @var array|null $user */

$userEmail = $account['email'] ?? '';
$userName = $user['full_name'] ?? $account['full_name'] ?? '';
$userPhone = $user['phone'] ?? '';
$userGender = $user['gender'] ?? 'other';
$userBirthday = $user['birthday'] ?? '';

// Parse birthday if exists
$birthDay = '';
$birthMonth = '';
$birthYear = '';
if (!empty($userBirthday)) {
  $date = date_create($userBirthday);
  if ($date) {
    $birthDay = date_format($date, 'd');
    $birthMonth = date_format($date, 'm');
    $birthYear = date_format($date, 'Y');
  }
}
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
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
<div class="max-w-7xl mx-auto py-6 sm:py-8 md:py-10 px-4 bg-gray-50">
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6">

    <!-- Sidebar trái -->
    <aside class="md:col-span-3 bg-white rounded-lg shadow-sm p-4 md:p-5">
      <?php include __DIR__ . '/layouts/navigation.php'; ?>
    </aside>

    <!-- Nội dung giữa -->
    <main class="md:col-span-6 bg-white rounded-lg shadow-sm p-4 md:p-6">
      <div class="max-w-2xl">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Thông tin tài khoản</h1>
        <form action="/account/update" method="POST" class="space-y-6">
          <!-- Ảnh đại diện -->
          <div class="flex items-center space-x-4 mb-6">
            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
              <i class="fas fa-user text-3xl"></i>
            </div>
            <div>
              <p class="text-sm text-gray-600 mb-2">Tải ảnh của bạn</p>
              <input type="file" class="hidden" id="avatar" name="avatar" accept="image/*">
              <label for="avatar" class="inline-block px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                Chọn ảnh
              </label>
            </div>
          </div>
          <!-- Họ tên -->
          <div class="space-y-1">
            <label for="fullName" class="block text-sm font-medium text-gray-700">Họ tên</label>
            <input type="text"
              id="fullName"
              name="full_name"
              value="<?php echo htmlspecialchars($userName); ?>"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent">
          </div>

          <!-- Giới tính -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Giới tính</label>
            <div class="flex space-x-4">
              <label class="inline-flex items-center">
                <input type="radio" name="gender" value="male" class="text-green-600 focus:ring-green-500"
                  <?php echo $userGender === 'male' ? 'checked' : ''; ?>>
                <span class="ml-2 text-sm text-gray-700">Nam</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="gender" value="female" class="text-green-600 focus:ring-green-500"
                  <?php echo $userGender === 'female' ? 'checked' : ''; ?>>
                <span class="ml-2 text-sm text-gray-700">Nữ</span>
              </label>
            </div>
          </div>
          <!-- Ngày sinh -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Ngày sinh (Không bắt buộc)</label>
            <div class="grid grid-cols-3 gap-4">
              <select name="birth_day" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent">
                <option value="">Ngày</option>
                <?php for ($i = 1; $i <= 31; $i++): ?>
                  <option value="<?php echo $i; ?>" <?php echo $birthDay == $i ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                  </option>
                <?php endfor; ?>
              </select>
              <select name="birth_month" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent">
                <option value="">Tháng</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                  <option value="<?php echo $i; ?>" <?php echo $birthMonth == $i ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                  </option>
                <?php endfor; ?>
              </select>
              <select name="birth_year" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-transparent">
                <option value="">Năm</option>
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= $currentYear - 100; $i--):
                ?>
                  <option value="<?php echo $i; ?>" <?php echo $birthYear == $i ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>
          </div>

          <!-- Checkbox đồng ý -->
          <div class="flex items-start space-x-2">
            <div class="flex items-center h-5">
              <input type="checkbox"
                id="privacy"
                name="privacy_agree"
                required
                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
            </div>
            <div class="ml-3">
              <label for="privacy" class="text-sm text-gray-600">
                Tôi đồng ý với <a href="/privacy" class="text-blue-600 hover:underline">chính sách xử lý dữ liệu cá nhân</a> của Xuân Hiệp
              </label>
            </div>
          </div>

          <!-- Button cập nhật -->
          <div class="pt-4">
            <button type="submit"
              class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
              Cập nhật
            </button>
          </div>
        </form>
        <div id="toast" class="toast"></div>
      </div>
    </main>

    <!-- Cột phải - Thông tin liên hệ -->
    <div class="md:col-span-3 space-y-4">
      <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Email</h2>

        <!-- Email -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span class="text-sm font-medium text-gray-700">Email</span>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600"><?php echo htmlspecialchars($userEmail); ?></span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Bảo mật</h2>
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 11c1.104 0 2 .895 2 2v2h-4v-2c0-1.105.896-2 2-2zm6 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2h8a2 2 0 012 2zm-6-8a4 4 0 014 4v3H8v-3a4 4 0 014-4z" />
            </svg>
            <span class="text-sm font-medium text-gray-700">Đổi mật khẩu</span>
          </div>
          <a href="/account/reset-password"
            class="text-sm font-medium text-blue-600 hover:underline focus:outline-none">Cập nhật</a>
        </div>
      </div>
      <!-- Liên kết mạng xã hội -->
      <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Liên kết mạng xã hội</h2>

        <!-- Facebook -->
        <div class="mb-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
              </svg>
              <span class="text-sm font-medium text-gray-700">Facebook</span>
            </div>
          </div>
        </div>

        <!-- Google -->
        <div>
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M5.266 9.765A7.077 7.077 0 0 1 12 4.909c1.69 0 3.218.6 4.418 1.582L19.91 3C17.782 1.145 15.055 0 12 0 7.27 0 3.198 2.698 1.24 6.65l4.026 3.115Z" />
                <path fill="#34A853" d="M16.04 18.013c-1.09.703-2.474 1.078-4.04 1.078a7.077 7.077 0 0 1-6.723-4.823l-4.04 3.067A11.965 11.965 0 0 0 12 24c2.933 0 5.735-1.043 7.834-3l-3.793-2.987Z" />
                <path fill="#4A90E2" d="M19.834 21c2.195-2.048 3.62-5.096 3.62-9 0-.71-.109-1.473-.272-2.182H12v4.637h6.436c-.317 1.559-1.17 2.766-2.395 3.558L19.834 21Z" />
                <path fill="#FBBC05" d="M5.277 14.268A7.12 7.12 0 0 1 4.909 12c0-.782.125-1.533.357-2.235L1.24 6.65A11.934 11.934 0 0 0 0 12c0 1.92.445 3.73 1.237 5.335l4.04-3.067Z" />
              </svg>
              <span class="text-sm font-medium text-gray-700">Google</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
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
<?php include __DIR__ . '/../layouts/footer.php'; ?>