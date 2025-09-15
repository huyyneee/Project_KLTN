<?php
$title = 'Sửa danh mục';
include __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">Sửa danh mục</h1>
        </div>

        <form method="POST" class="p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Tên danh mục <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($category['name']) ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Nhập tên danh mục">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả
                </label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Nhập mô tả danh mục"><?= htmlspecialchars($category['description']) ?></textarea>
            </div>

            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="/admin/categories"
                    class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>Cập nhật danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/admin_footer.php'; ?>