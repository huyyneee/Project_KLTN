<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';

use App\Models\Product;
use App\Models\Category;
use Exception;

class DashboardApiController extends ApiController
{

    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    // GET /api/dashboard/stats - Lấy thống kê dashboard
    public function getStats()
    {
        try {
            // Get total products
            $products = $this->productModel->findAll();
            $totalProducts = count($products);

            // Get total categories
            $categories = $this->categoryModel->findAll();
            $totalCategories = count($categories);

            // Mock data for orders and customers (since we don't have these tables yet)
            $totalOrders = 890;
            $totalCustomers = 2345;

            $stats = [
                'products' => [
                    'total' => $totalProducts,
                    'change' => '+12%',
                    'change_type' => 'increase'
                ],
                'categories' => [
                    'total' => $totalCategories,
                    'change' => '+3%',
                    'change_type' => 'increase'
                ],
                'orders' => [
                    'total' => $totalOrders,
                    'change' => '+25%',
                    'change_type' => 'increase'
                ],
                'customers' => [
                    'total' => $totalCustomers,
                    'change' => '+18%',
                    'change_type' => 'increase'
                ]
            ];

            $this->sendResponse($stats, 'Dashboard stats retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve dashboard stats: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/dashboard/best-selling - Lấy sản phẩm bán chạy
    public function getBestSelling()
    {
        try {
            // Mock data for best selling products
            $bestSelling = [
                [
                    'id' => 1,
                    'name' => 'Sản phẩm #1',
                    'category' => 'Danh mục A',
                    'sold' => 123,
                    'change' => '+15%'
                ],
                [
                    'id' => 2,
                    'name' => 'Sản phẩm #2',
                    'category' => 'Danh mục B',
                    'sold' => 98,
                    'change' => '+8%'
                ],
                [
                    'id' => 3,
                    'name' => 'Sản phẩm #3',
                    'category' => 'Danh mục A',
                    'sold' => 76,
                    'change' => '+12%'
                ],
                [
                    'id' => 4,
                    'name' => 'Sản phẩm #4',
                    'category' => 'Danh mục C',
                    'sold' => 54,
                    'change' => '+5%'
                ],
                [
                    'id' => 5,
                    'name' => 'Sản phẩm #5',
                    'category' => 'Danh mục B',
                    'sold' => 43,
                    'change' => '+3%'
                ]
            ];

            $this->sendResponse($bestSelling, 'Best selling products retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve best selling products: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/dashboard/recent-activity - Lấy hoạt động gần đây
    public function getRecentActivity()
    {
        try {
            // Mock data for recent activity
            $activities = [
                [
                    'id' => 1,
                    'action' => 'Thêm sản phẩm mới',
                    'time' => '2 phút trước',
                    'type' => 'create'
                ],
                [
                    'id' => 2,
                    'action' => 'Cập nhật danh mục',
                    'time' => '15 phút trước',
                    'type' => 'update'
                ],
                [
                    'id' => 3,
                    'action' => 'Xóa sản phẩm',
                    'time' => '1 giờ trước',
                    'type' => 'delete'
                ],
                [
                    'id' => 4,
                    'action' => 'Thêm danh mục',
                    'time' => '2 giờ trước',
                    'type' => 'create'
                ],
                [
                    'id' => 5,
                    'action' => 'Cập nhật sản phẩm',
                    'time' => '3 giờ trước',
                    'type' => 'update'
                ]
            ];

            $this->sendResponse($activities, 'Recent activity retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve recent activity: ' . $e->getMessage(), 500);
        }
    }
}
