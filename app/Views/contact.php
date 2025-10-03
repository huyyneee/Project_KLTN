<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow mt-8 mb-8 border">
    <h2 class="text-2xl font-bold text-green-900 mb-2">Chúng tôi trân trọng ý kiến của quý khách.</h2>
    <hr class="mb-4">
    <p class="mb-4 text-gray-700">Quý khách vui lòng gửi thắc mắc hoặc ý kiến đóng góp qua biểu mẫu.</p>
    <form action="#" method="post" class="space-y-4">
        <div>
            <label class="block font-semibold mb-1">Tiêu đề <span class="text-red-500">(*)</span></label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" placeholder="Tiêu đề" required>
        </div>
        <div>
            <label class="block font-semibold mb-1">Chi tiết <span class="text-red-500">(*)</span></label>
            <textarea name="detail" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" placeholder="Hãy mô tả chi tiết" required></textarea>
        </div>
        <div>
            <label class="block font-semibold mb-1">Tên <span class="text-red-500">(*)</span></label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" placeholder="Nhập đầy đủ họ và tên" required>
        </div>
        <div class="flex space-x-4">
            <div class="flex-1">
                <label class="block font-semibold mb-1">Số điện thoại <span class="text-red-500">(*)</span></label>
                <input type="text" name="phone" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" placeholder="Số điện thoại" required>
            </div>
            <div class="flex-1">
                <label class="block font-semibold mb-1">Email <span class="text-red-500">(*)</span></label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" placeholder="Email liên hệ" required>
            </div>
        </div>
        <button type="submit" class="bg-green-800 text-white font-semibold px-6 py-2 rounded hover:bg-green-700 mt-2">Gửi yêu cầu</button>
    </form>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
