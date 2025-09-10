<!-- app/Views/login.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Tài Khoản</title>
    <link href="/assets/css/output.css" rel="stylesheet">
    <style>
        body {
            background-image: url('/assets/images/bg-login.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">
    <!-- Header -->
    <div class="bg-sky-200 py-1 flex justify-between px-4 text-xs">
        <span>namtran.tmg@gmail.com</span>
        <span>TP.Hồ Chí Minh</span>
        <div class="flex gap-2">
            <span>VNĐ</span>
            <a href="#" class="underline">Tạo tài khoản</a>
            <span>|</span>
            <span>Tài khoản Admin</span>
        </div>
    </div>
    <div class="bg-orange-300 py-4 flex flex-col gap-2">
        <div class="flex justify-between items-center px-8">
            <div class="flex gap-2">
                <input type="text" placeholder="Danh mục sản phẩm" class="px-2 py-1 rounded border">
                <input type="text" placeholder="Tên Sản phẩm" class="px-2 py-1 rounded border">
                <button class="bg-white px-4 py-1 rounded font-semibold">Tìm kiếm</button>
            </div>
            <div class="flex gap-8 items-center">
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 008.48 19h7.04a2 2 0 001.83-1.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" /></svg>
                    <span>Giỏ hàng</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span>Tài Khoản</span>
                </div>
            </div>
        </div>
        <div class="flex justify-center gap-8 font-semibold text-sm">
            <a href="#">Tùy chọn</a>
            <a href="#">Sữa rửa mặt</a>
            <a href="#">Kem chống nắng</a>
            <a href="#">Serum</a>
            <a href="#">Nước hoa</a>
            <a href="#">Nước tẩy trang</a>
            <a href="#">Sản phẩm khác</a>
        </div>
    </div>
    <!-- Login Form -->
    <div class="flex-1 flex items-center justify-center">
        <div class="bg-gray-200 bg-opacity-80 rounded-lg shadow-lg p-10 w-full max-w-md">
            <h2 class="text-3xl font-bold text-center mb-6">Đăng Nhập Tài Khoản</h2>
            <form action="/login" method="POST" class="flex flex-col gap-4">
                <input type="text" name="username" placeholder="Username" class="px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-blue-400">
                <input type="password" name="password" placeholder="Password" class="px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-blue-400">
                <div class="flex gap-4 justify-center mt-2">
                    <button type="submit" class="bg-blue-400 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-500">Đăng nhập</button>
                    <a href="/" class="bg-white border px-6 py-2 rounded-full font-semibold hover:bg-gray-100">Thoát</a>
                </div>
            </form>
            <div class="text-center mt-4">
                <span>Bạn có tài khoản chưa? </span><a href="/register" class="text-blue-600 font-semibold hover:underline">Đăng kí</a>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="bg-red-200 py-6 mt-8">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
            <div>
                <h4 class="font-bold mb-2">Thông tin liên hệ</h4>
                <div class="h-4 bg-white rounded mb-1"></div>
                <div class="h-4 bg-white rounded"></div>
            </div>
            <div>
                <h4 class="font-bold mb-2">Giờ mở cửa</h4>
                <div class="h-4 bg-white rounded mb-1"></div>
                <div class="h-4 bg-white rounded"></div>
            </div>
            <div>
                <h4 class="font-bold mb-2">Chính Sách</h4>
                <div class="h-4 bg-white rounded mb-1"></div>
                <div class="h-4 bg-white rounded"></div>
            </div>
            <div>
                <h4 class="font-bold mb-2">Theo dõi chúng tôi</h4>
                <div class="h-4 bg-white rounded mb-1"></div>
                <div class="h-4 bg-white rounded"></div>
            </div>
        </div>
    </footer>
</body>
</html>
