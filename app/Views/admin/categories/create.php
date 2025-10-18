<?php
$title = 'Thêm danh mục mới';
include __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="max-w-3xl mx-auto animate-fade-in">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-display text-gray-900 mb-2">Thêm danh mục mới</h1>
        <p class="text-body-lg text-gray-600">Tạo danh mục mới để tổ chức và phân loại sản phẩm</p>
    </div>

    <!-- Form -->
    <div class="card rounded-2xl">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plus text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-heading font-semibold text-gray-900">Thông tin danh mục</h2>
                    <p class="text-body text-gray-600">Điền đầy đủ thông tin để tạo danh mục mới</p>
                </div>
            </div>
        </div>

        <form method="POST" class="p-8 space-y-8">
            <!-- Name Field -->
            <div class="space-y-3">
                <label for="name" class="block text-body-lg font-semibold text-gray-700">
                    Tên danh mục <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-tag text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" id="name" name="name" required
                        class="form-input w-full pl-12 pr-4 py-4 rounded-xl text-body-lg"
                        placeholder="Nhập tên danh mục">
                </div>
                <p class="text-caption text-gray-500">Tên danh mục sẽ hiển thị trên trang web và giúp người dùng dễ dàng
                    nhận biết</p>
            </div>

            <!-- Description Field -->
            <div class="space-y-3">
                <label for="description" class="block text-body-lg font-semibold text-gray-700">
                    Mô tả danh mục
                </label>
                <div class="relative">
                    <div class="absolute top-4 left-4 pointer-events-none">
                        <i class="fas fa-align-left text-gray-400 text-lg"></i>
                    </div>
                    <textarea id="description" name="description" rows="6"
                        class="form-input w-full pl-12 pr-4 py-4 rounded-xl text-body-lg resize-none"
                        placeholder="Nhập mô tả chi tiết về danh mục này..."></textarea>
                </div>
                <p class="text-caption text-gray-500">Mô tả sẽ giúp người dùng hiểu rõ hơn về mục đích và nội dung của
                    danh mục</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200">
                <a href="/admin/categories" class="btn-secondary px-6 py-4 rounded-xl font-semibold">
                    <i class="fas fa-arrow-left mr-3"></i>Quay lại
                </a>
                <button type="submit" class="btn-primary px-8 py-4 rounded-xl font-semibold">
                    <i class="fas fa-save mr-3"></i>Lưu danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/admin_footer.php'; ?>