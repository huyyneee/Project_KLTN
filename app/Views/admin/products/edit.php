<?php
$title = 'Sửa sản phẩm';
include __DIR__ . '/../../layouts/admin_header.php';

// Parse specifications JSON
$specifications = json_decode($product['specifications'], true);
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">Sửa sản phẩm</h1>
        </div>
        
        <form method="POST" class="p-6 space-y-8">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên sản phẩm <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           value="<?= htmlspecialchars($product['name']) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nhập tên sản phẩm">
                </div>
                
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Giá sản phẩm <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           required
                           min="0"
                           step="1000"
                           value="<?= $product['price'] ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nhập giá sản phẩm">
                </div>
            </div>
            
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Danh mục <span class="text-red-500">*</span>
                </label>
                <select id="category_id" 
                        name="category_id" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Chọn danh mục</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Specifications Table -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thông số kỹ thuật</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                        <input type="text" 
                               id="brand" 
                               name="brand"
                               value="<?= htmlspecialchars($specifications['brand'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nhập thương hiệu">
                    </div>
                    
                    <div>
                        <label for="brand_origin" class="block text-sm font-medium text-gray-700 mb-2">Xuất xứ thương hiệu</label>
                        <input type="text" 
                               id="brand_origin" 
                               name="brand_origin"
                               value="<?= htmlspecialchars($specifications['brand_origin'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nhập xuất xứ thương hiệu">
                    </div>
                    
                    <div>
                        <label for="manufacturing_location" class="block text-sm font-medium text-gray-700 mb-2">Nơi sản xuất</label>
                        <input type="text" 
                               id="manufacturing_location" 
                               name="manufacturing_location"
                               value="<?= htmlspecialchars($specifications['manufacturing_location'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nhập nơi sản xuất">
                    </div>
                    
                    <div>
                        <label for="volume" class="block text-sm font-medium text-gray-700 mb-2">Dung tích</label>
                        <input type="text" 
                               id="volume" 
                               name="volume"
                               value="<?= htmlspecialchars($specifications['volume'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="VD: 50ml, 100ml">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="skin_type" class="block text-sm font-medium text-gray-700 mb-2">Loại da</label>
                        <input type="text" 
                               id="skin_type" 
                               name="skin_type"
                               value="<?= htmlspecialchars($specifications['skin_type'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="VD: Da dầu, da khô, da nhạy cảm">
                    </div>
                </div>
            </div>
            
            <!-- Rich Text Editors -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Mô tả chi tiết</h3>
                <div class="space-y-6">
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  required
                                  class="rich-text-editor w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  rows="6"
                                  placeholder="Nhập mô tả chi tiết sản phẩm"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    
                    <div>
                        <label for="usage" class="block text-sm font-medium text-gray-700 mb-2">Cách sử dụng</label>
                        <textarea id="usage" 
                                  name="usage"
                                  class="rich-text-editor w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  rows="6"
                                  placeholder="Hướng dẫn cách sử dụng sản phẩm"><?= htmlspecialchars($product['usage']) ?></textarea>
                    </div>
                    
                    <div>
                        <label for="ingredients" class="block text-sm font-medium text-gray-700 mb-2">Thành phần</label>
                        <textarea id="ingredients" 
                                  name="ingredients"
                                  class="rich-text-editor w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  rows="6"
                                  placeholder="Danh sách thành phần sản phẩm"><?= htmlspecialchars($product['ingredients']) ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="/admin/products" 
                   class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>Cập nhật sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/admin_footer.php'; ?>
