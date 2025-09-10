<!DOCTYPE html>
<html lang="vi"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title)? $title : 'App' ?></title>
    <link rel="stylesheet" href="/assets/css/output.css">
</head><body>
<!-- Thanh điều hướng và header -->
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
        <div class="flex gap-2 w-2/3">
            <input type="text" placeholder="Danh mục sản phẩm" class="px-2 py-1 rounded border flex-1 min-w-40">
            <input type="text" placeholder="Tên Sản phẩm" class="px-2 py-1 rounded border flex-1 min-w-40">
            <button class="bg-white px-4 py-1 rounded font-semibold">Tìm kiếm</button>
        </div>
        <div class="flex gap-8 items-center w-1/3 justify-end">
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 008.48 19h7.04a2 2 0 001.83-1.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" /></svg>
                <span>Giỏ hàng</span>
            </div>
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <a href="/login"><span>Tài Khoản</span></a>
            </div>
        </div>
    </div>
    <div class="flex justify-center gap-8 font-semibold text-sm mt-2 flex-wrap">
        <a href="#">Tùy chọn</a>
        <a href="#">Sữa rửa mặt</a>
        <a href="#">Kem chống nắng</a>
        <a href="#">Serum</a>
        <a href="#">Nước hoa</a>
        <a href="#">Nước tẩy trang</a>
        <a href="#">Sản phẩm khác</a>
    </div>
</div>

