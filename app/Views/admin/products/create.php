<?php
$title = 'Thêm sản phẩm mới';
include __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="max-w-5xl mx-auto animate-fade-in">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-display text-gray-900 mb-2">Thêm sản phẩm mới</h1>
        <p class="text-body-lg text-gray-600">Tạo sản phẩm mới với thông tin chi tiết và đầy đủ</p>
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
                    <h2 class="text-heading font-semibold text-gray-900">Thông tin sản phẩm</h2>
                    <p class="text-body text-gray-600">Điền đầy đủ thông tin để tạo sản phẩm mới</p>
                </div>
            </div>
        </div>

        <form method="POST" class="p-8 space-y-10">
            <!-- Basic Information -->
            <div class="space-y-6">
                <h3 class="text-subheading font-semibold text-gray-900 border-b border-gray-200 pb-3">Thông tin cơ bản
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label for="name" class="block text-body-lg font-semibold text-gray-700">
                            Tên sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="Nhập tên sản phẩm">
                    </div>

                    <div class="space-y-4">
                        <label for="price" class="block text-body-lg font-semibold text-gray-700">
                            Giá sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="price" name="price" required min="0" step="1000"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="Nhập giá sản phẩm">
                    </div>
                </div>

                <div class="space-y-4">
                    <label for="category_id" class="block text-body-lg font-semibold text-gray-700">
                        Danh mục <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                        <option value="">Chọn danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Specifications Table -->
            <div class="space-y-6">
                <h3 class="text-subheading font-semibold text-gray-900 border-b border-gray-200 pb-3">Thông số kỹ thuật
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label for="brand" class="block text-body-lg font-semibold text-gray-700">Thương hiệu</label>
                        <input type="text" id="brand" name="brand"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="Nhập thương hiệu">
                    </div>

                    <div class="space-y-4">
                        <label for="brand_origin" class="block text-body-lg font-semibold text-gray-700">Xuất xứ thương
                            hiệu</label>
                        <input type="text" id="brand_origin" name="brand_origin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="Nhập xuất xứ thương hiệu">
                    </div>

                    <div class="space-y-4">
                        <label for="manufacturing_location" class="block text-body-lg font-semibold text-gray-700">Nơi
                            sản xuất</label>
                        <input type="text" id="manufacturing_location" name="manufacturing_location"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="Nhập nơi sản xuất">
                    </div>

                    <div class="space-y-4">
                        <label for="volume" class="block text-body-lg font-semibold text-gray-700">Dung tích</label>
                        <input type="text" id="volume" name="volume"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="VD: 50ml, 100ml">
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <label for="skin_type" class="block text-body-lg font-semibold text-gray-700">Loại da</label>
                        <input type="text" id="skin_type" name="skin_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="VD: Da dầu, da khô, da nhạy cảm">
                    </div>
                </div>
            </div>

            <!-- Rich Text Editors -->
            <div class="space-y-8">
                <h3 class="text-subheading font-semibold text-gray-900 border-b border-gray-200 pb-3">Mô tả chi tiết
                </h3>

                <div class="space-y-6">
                    <div class="space-y-4">
                        <label for="description" class="block text-body-lg font-semibold text-gray-700">
                            Mô tả sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" required
                            class="rich-text-editor w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            rows="6" placeholder="Nhập mô tả chi tiết sản phẩm"></textarea>
                    </div>

                    <div class="space-y-4">
                        <label for="usage" class="block text-body-lg font-semibold text-gray-700">Cách sử dụng</label>
                        <textarea id="usage" name="usage"
                            class="rich-text-editor w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            rows="6" placeholder="Hướng dẫn cách sử dụng sản phẩm"></textarea>
                    </div>

                    <div class="space-y-4">
                        <label for="ingredients" class="block text-body-lg font-semibold text-gray-700">Thành
                            phần</label>
                        <textarea id="ingredients" name="ingredients"
                            class="rich-text-editor w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            rows="6" placeholder="Danh sách thành phần sản phẩm"></textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-8 border-t border-gray-200">
                <a href="/admin/products" class="btn-secondary px-6 py-4 rounded-xl font-semibold">
                    <i class="fas fa-arrow-left mr-3"></i>Quay lại
                </a>
                <button type="submit" class="btn-primary px-8 py-4 rounded-xl font-semibold">
                    <i class="fas fa-save mr-3"></i>Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/admin_footer.php'; ?>