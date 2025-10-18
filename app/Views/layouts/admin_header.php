<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Header</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <header class="h-16 border-b border-gray-200 bg-white shadow px-6 flex items-center justify-between">
        <!-- Bên trái -->
        <div class="flex items-center gap-4">
            <!-- Sidebar Trigger -->
            <button class="hover:bg-blue-100 transition p-2 rounded-lg">
                ☰
            </button>

            <!-- Search box -->
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Tìm kiếm..."
                    class="pl-10 w-80 bg-gray-100 border-0 rounded-md focus:bg-white focus:ring-2 focus:ring-blue-400 transition">
            </div>
        </div>

        <!-- Bên phải -->
        <div class="flex items-center gap-4">
            <!-- Bell Notification -->
            <button class="relative hover:bg-blue-100 transition p-2 rounded-full">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 
                 2.032 0 0 1 18 14.158V11a6.002 
                 6.002 0 0 0-4-5.659V5a2 2 
                 0 1 0-4 0v.341C7.67 6.165 
                 6 8.388 6 11v3.159c0 .538-.214 
                 1.055-.595 1.436L4 17h5m6 0v1a3 
                 3 0 1 1-6 0v-1m6 0H9" />
                </svg>
                <!-- Chấm đỏ -->
                <span class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full"></span>
            </button>

            <!-- Dropdown Menu -->
            <div class="relative">
                <button onclick="toggleDropdown()"
                    class="flex items-center gap-2 hover:bg-blue-100 transition p-2 rounded-lg">
                    <div class="h-8 w-8 flex items-center justify-center rounded-full bg-blue-600 text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 
                     4-4s-1.79-4-4-4-4 1.79-4 
                     4 1.79 4 4 4zm0 2c-2.67 0-8 
                     1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <span class="hidden sm:block">Admin</span>
                </button>

                <!-- Dropdown content -->
                <div id="dropdownMenu" class="absolute right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg hidden">
                    <div class="px-4 py-2 font-semibold">Tài khoản của tôi</div>
                    <div class="border-t"></div>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">Hồ sơ</a>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">Cài đặt</a>
                    <div class="border-t"></div>
                    <a href="#" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Đăng xuất</a>
                </div>
            </div>
        </div>
    </header>

    <script>
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        }
    </script>

</body>

</html>