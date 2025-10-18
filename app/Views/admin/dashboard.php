<?php
$title = 'Dashboard';
include __DIR__ . '/../layouts/admin_header.php';
?>

<div class="space-y-8 animate-fade-in max-w-full">
    <!-- Dashboard Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Tổng quan về cửa hàng của bạn</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Products Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-lg"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900"><?= $products_count ?></div>
                    <div class="text-sm text-gray-500">Tổng sản phẩm</div>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+12% so với tháng trước</span>
                <i class="fas fa-arrow-up text-green-600 ml-1"></i>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder-open text-green-600 text-lg"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900"><?= $categories_count ?></div>
                    <div class="text-sm text-gray-500">Danh mục</div>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+3% so với tháng trước</span>
                <i class="fas fa-arrow-up text-green-600 ml-1"></i>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-yellow-600 text-lg"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">0</div>
                    <div class="text-sm text-gray-500">Đơn hàng</div>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+25% so với tháng trước</span>
                <i class="fas fa-arrow-up text-green-600 ml-1"></i>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-red-600 text-lg"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">0</div>
                    <div class="text-sm text-gray-500">Khách hàng</div>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">+18% so với tháng trước</span>
                <i class="fas fa-arrow-up text-green-600 ml-1"></i>
            </div>
        </div>
    </div>

    <!-- Bottom Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Best-selling Products -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-box text-blue-600 text-sm"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Sản phẩm bán chạy</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Sản phẩm #1</div>
                                <div class="text-xs text-gray-500">Danh mục A</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">123 đã bán</div>
                            <div class="text-xs text-green-600">+15%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Sản phẩm #2</div>
                                <div class="text-xs text-gray-500">Danh mục B</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">98 đã bán</div>
                            <div class="text-xs text-green-600">+8%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Sản phẩm #3</div>
                                <div class="text-xs text-gray-500">Danh mục A</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">76 đã bán</div>
                            <div class="text-xs text-green-600">+12%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Sản phẩm #4</div>
                                <div class="text-xs text-gray-500">Danh mục C</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">54 đã bán</div>
                            <div class="text-xs text-green-600">+5%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Sản phẩm #5</div>
                                <div class="text-xs text-gray-500">Danh mục B</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">43 đã bán</div>
                            <div class="text-xs text-green-600">+3%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-circle text-green-600 text-sm"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Hoạt động gần đây</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-900">Thêm sản phẩm mới</div>
                            <div class="text-xs text-gray-500">2 phút trước</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-900">Cập nhật danh mục</div>
                            <div class="text-xs text-gray-500">15 phút trước</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-900">Xóa sản phẩm</div>
                            <div class="text-xs text-gray-500">1 giờ trước</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-900">Thêm danh mục</div>
                            <div class="text-xs text-gray-500">2 giờ trước</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-900">Cập nhật sản phẩm</div>
                            <div class="text-xs text-gray-500">3 giờ trước</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/admin_footer.php'; ?>