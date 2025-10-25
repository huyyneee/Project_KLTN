<?php

/** @var string|null $userName User's full name from the parent scope */
?>
<!-- Mobile Navigation Toggle -->
<div class="md:hidden">
  <button id="mobile-nav-toggle" class="flex items-center justify-between w-full p-3 bg-white rounded-lg shadow-sm">
    <div class="flex items-center space-x-3">
      <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
        <i class="fas fa-user text-lg"></i>
      </div>
      <div>
        <p class="font-semibold text-gray-800 text-sm truncate">Chào <?php echo htmlspecialchars($userName ?: ''); ?></p>
      </div>
    </div>
    <svg id="nav-arrow" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
  </button>
</div>

<!-- Desktop Profile Section -->
<div class="hidden md:flex items-center space-x-3 mb-5">
  <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 flex-shrink-0">
    <i class="fas fa-user text-xl"></i>
  </div>
  <div class="min-w-1/3">
    <p class="font-semibold text-gray-800 text-base truncate">Chào <?php echo htmlspecialchars($userName ?: ''); ?></p>
    <a href="/account/edit" class="text-sm text-green-600 hover:underline">Chỉnh sửa tài khoản</a>
  </div>
</div>

<!-- Navigation Menu -->
<div id="nav-menu" class="hidden md:block mx-auto">
  <hr class="mb-4 md:block hidden">
  <h3 class="text-gray-600 font-semibold mb-3 text-xs md:text-sm mt-0 md:mt-3 hidden md:block uppercase text-center md:text-left">Quản lý tài khoản</h3>
  <ul class="space-y-2 md:space-y-2 text-sm text-center md:text-left" id="account-nav">
    <li><a href="/account" class="nav-link block text-black-700 hover:text-orange-500 transition-colors py-2">Tài khoản của tôi</a></li>
    <li><a href="/account/edit" class="nav-link block text-black-700 hover:text-orange-500 transition-colors py-2">Thông tin tài khoản</a></li>
    <li><a href="/order" class="nav-link block text-black-700 hover:text-orange-500 transition-colors py-2">Đơn hàng của tôi</a></li>
    <li><a href="/account/address" class="nav-link block text-black-700 hover:text-orange-500 transition-colors py-2">Số địa chỉ nhận hàng</a></li>
  </ul>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Active link highlighting
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('#account-nav .nav-link');

    navLinks.forEach(link => {
      if (link.getAttribute('href') === currentPath) {
        link.classList.add('text-orange-500', 'font-bold');
        link.classList.remove('text-black-700', 'font-normal');
      }
    });

    // Mobile navigation toggle
    const toggleButton = document.getElementById('mobile-nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    const navArrow = document.getElementById('nav-arrow');
    let isOpen = false;

    toggleButton.addEventListener('click', function() {
      isOpen = !isOpen;
      if (isOpen) {
        navMenu.classList.remove('hidden');
        navArrow.classList.add('rotate-180');
      } else {
        navMenu.classList.add('hidden');
        navArrow.classList.remove('rotate-180');
      }
    });

    // Hide menu when clicking outside
    document.addEventListener('click', function(event) {
      const isClickInside = toggleButton.contains(event.target) || navMenu.contains(event.target);
      if (!isClickInside && isOpen) {
        navMenu.classList.add('hidden');
        navArrow.classList.remove('rotate-180');
        isOpen = false;
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 768) { // md breakpoint
        navMenu.classList.remove('hidden');
      } else if (!isOpen) {
        navMenu.classList.add('hidden');
      }
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Lấy đường dẫn hiện tại
    const currentPath = window.location.pathname;

    // Tìm tất cả các link trong navigation
    const navLinks = document.querySelectorAll('#account-nav .nav-link');

    // Kiểm tra từng link
    navLinks.forEach(link => {
      if (link.getAttribute('href') === currentPath) {
        // Nếu link trùng với đường dẫn hiện tại, thêm class active
        link.classList.add('text-orange-500');
        link.classList.add('font-bold');
        link.classList.remove('text-black-700', 'font-normal');
      }
    });
  });
</script>