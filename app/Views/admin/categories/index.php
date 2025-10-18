<?php
$title = 'Quản lý danh mục';
include __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="space-y-6 animate-fade-in max-w-full">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-display text-gray-900 mb-2">Quản lý danh mục</h1>
            <p class="text-body-lg text-gray-600">Tổ chức và phân loại sản phẩm hiệu quả</p>
        </div>
        <a href="/admin/categories/create" class="btn-primary px-6 py-4 rounded-xl font-semibold">
            <i class="fas fa-plus mr-3"></i>
            Thêm danh mục
        </a>
    </div>

    <!-- Stats Bar -->
    <div class="flex items-center space-x-4">
        <div class="bg-white rounded-xl px-6 py-4 shadow-sm border border-gray-200">
            <div class="flex items-center space-x-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder text-white text-lg"></i>
                </div>
                <div>
                    <div class="text-subheading font-bold text-gray-900"><?= count($categories) ?></div>
                    <div class="text-caption text-gray-600">Tổng số danh mục</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card rounded-2xl overflow-hidden">
        <?php if (empty($categories)): ?>
            <!-- Empty State -->
            <div class="p-16 text-center">
                <div
                    class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-folder text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-subheading font-semibold text-gray-600 mb-4">Chưa có danh mục nào</h3>
                <p class="text-body text-gray-500 mb-8 max-w-md mx-auto">Bắt đầu tạo danh mục đầu tiên để tổ chức sản phẩm
                    của bạn một cách hiệu quả</p>
                <a href="/admin/categories/create" class="btn-primary px-8 py-4 rounded-xl font-semibold">
                    <i class="fas fa-plus mr-3"></i>
                    Tạo danh mục đầu tiên
                </a>
            </div>
        <?php else: ?>
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-list text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-heading font-semibold text-gray-900">Danh sách danh mục</h3>
                        <p class="text-body text-gray-600">Quản lý tất cả danh mục trong hệ thống</p>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-8 py-4 text-left text-caption font-semibold text-gray-600 uppercase tracking-wider">
                                ID</th>
                            <th
                                class="px-8 py-4 text-left text-caption font-semibold text-gray-600 uppercase tracking-wider">
                                Tên danh mục</th>
                            <th
                                class="px-8 py-4 text-left text-caption font-semibold text-gray-600 uppercase tracking-wider">
                                Mô tả</th>
                            <th
                                class="px-8 py-4 text-left text-caption font-semibold text-gray-600 uppercase tracking-wider">
                                Ngày tạo</th>
                            <th
                                class="px-8 py-4 text-left text-caption font-semibold text-gray-600 uppercase tracking-wider">
                                Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($categories as $category): ?>
                            <tr class="table-row">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-caption">
                                            <?= $category['id'] ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-xl flex items-center justify-center mr-4">
                                            <i class="fas fa-folder text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-body-lg font-semibold text-gray-900">
                                                <?= htmlspecialchars($category['name']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-body text-gray-600 max-w-xs">
                                        <?php if (!empty($category['description'])): ?>
                                            <?= htmlspecialchars(substr($category['description'], 0, 100)) ?>
                                            <?php if (strlen($category['description']) > 100): ?>...<?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">Chưa có mô tả</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-body text-gray-500">
                                        <div class="font-medium"><?= date('d/m/Y', strtotime($category['created_at'])) ?></div>
                                        <div class="text-caption"><?= date('H:i', strtotime($category['created_at'])) ?></div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <a href="/admin/categories/edit?id=<?= $category['id'] ?>"
                                            class="btn-secondary px-4 py-2 rounded-lg font-medium">
                                            <i class="fas fa-edit mr-2"></i>Sửa
                                        </a>
                                        <a href="/admin/categories/delete?id=<?= $category['id'] ?>"
                                            onclick="return confirmDelete('Bạn có chắc chắn muốn xóa danh mục này?')"
                                            class="btn-danger px-4 py-2 rounded-lg font-medium">
                                            <i class="fas fa-trash mr-2"></i>Xóa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/admin_footer.php'; ?>