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
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            // Get total products
            $productsQuery = "SELECT COUNT(*) as total FROM products WHERE deleted_at IS NULL";
            $productsStmt = $conn->query($productsQuery);
            $totalProducts = $productsStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get total categories
            $categoriesQuery = "SELECT COUNT(*) as total FROM categories WHERE deleted_at IS NULL";
            $categoriesStmt = $conn->query($categoriesQuery);
            $totalCategories = $categoriesStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get total customers (users with role 'user')
            $customersQuery = "SELECT COUNT(*) as total FROM accounts WHERE role = 'user' AND status = 'active'";
            $customersStmt = $conn->query($customersQuery);
            $totalCustomers = $customersStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Check if orders table exists and get orders count
            $tableCheckQuery = "SHOW TABLES LIKE 'orders'";
            $tableStmt = $conn->query($tableCheckQuery);
            $hasOrdersTable = $tableStmt->rowCount() > 0;

            if ($hasOrdersTable) {
                $ordersQuery = "SELECT COUNT(*) as total FROM orders WHERE status = 'completed'";
                $ordersStmt = $conn->query($ordersQuery);
                $totalOrders = $ordersStmt->fetch(\PDO::FETCH_ASSOC)['total'];
            } else {
                $totalOrders = 0;
            }

            // Calculate growth (compare with previous month)
            $prevMonthStart = date('Y-m-01', strtotime('-1 month'));
            $prevMonthEnd = date('Y-m-t', strtotime('-1 month'));
            $currentMonthStart = date('Y-m-01');
            $currentMonthEnd = date('Y-m-t');

            // Products growth
            $prevProductsQuery = "SELECT COUNT(*) as prev FROM products 
                               WHERE deleted_at IS NULL AND created_at >= :prev_start AND created_at <= :prev_end";
            $prevProductsStmt = $conn->prepare($prevProductsQuery);
            $prevProductsStmt->bindParam(':prev_start', $prevMonthStart);
            $prevProductsStmt->bindParam(':prev_end', $prevMonthEnd);
            $prevProductsStmt->execute();
            $prevProducts = $prevProductsStmt->fetch(\PDO::FETCH_ASSOC)['prev'];

            $currentProductsQuery = "SELECT COUNT(*) as current FROM products 
                                   WHERE deleted_at IS NULL AND created_at >= :current_start AND created_at <= :current_end";
            $currentProductsStmt = $conn->prepare($currentProductsQuery);
            $currentProductsStmt->bindParam(':current_start', $currentMonthStart);
            $currentProductsStmt->bindParam(':current_end', $currentMonthEnd);
            $currentProductsStmt->execute();
            $currentProducts = $currentProductsStmt->fetch(\PDO::FETCH_ASSOC)['current'];

            $productsGrowth = $prevProducts > 0 ? round((($currentProducts - $prevProducts) / $prevProducts) * 100) : 0;
            $productsGrowthText = $productsGrowth >= 0 ? '+' . $productsGrowth . '%' : $productsGrowth . '%';

            // Categories growth
            $prevCategoriesQuery = "SELECT COUNT(*) as prev FROM categories 
                                  WHERE deleted_at IS NULL AND created_at >= :prev_start AND created_at <= :prev_end";
            $prevCategoriesStmt = $conn->prepare($prevCategoriesQuery);
            $prevCategoriesStmt->bindParam(':prev_start', $prevMonthStart);
            $prevCategoriesStmt->bindParam(':prev_end', $prevMonthEnd);
            $prevCategoriesStmt->execute();
            $prevCategories = $prevCategoriesStmt->fetch(\PDO::FETCH_ASSOC)['prev'];

            $currentCategoriesQuery = "SELECT COUNT(*) as current FROM categories 
                                      WHERE deleted_at IS NULL AND created_at >= :current_start AND created_at <= :current_end";
            $currentCategoriesStmt = $conn->prepare($currentCategoriesQuery);
            $currentCategoriesStmt->bindParam(':current_start', $currentMonthStart);
            $currentCategoriesStmt->bindParam(':current_end', $currentMonthEnd);
            $currentCategoriesStmt->execute();
            $currentCategories = $currentCategoriesStmt->fetch(\PDO::FETCH_ASSOC)['current'];

            $categoriesGrowth = $prevCategories > 0 ? round((($currentCategories - $prevCategories) / $prevCategories) * 100) : 0;
            $categoriesGrowthText = $categoriesGrowth >= 0 ? '+' . $categoriesGrowth . '%' : $categoriesGrowth . '%';

            // Customers growth
            $prevCustomersQuery = "SELECT COUNT(*) as prev FROM accounts 
                                WHERE role = 'user' AND status = 'active' AND created_at >= :prev_start AND created_at <= :prev_end";
            $prevCustomersStmt = $conn->prepare($prevCustomersQuery);
            $prevCustomersStmt->bindParam(':prev_start', $prevMonthStart);
            $prevCustomersStmt->bindParam(':prev_end', $prevMonthEnd);
            $prevCustomersStmt->execute();
            $prevCustomers = $prevCustomersStmt->fetch(\PDO::FETCH_ASSOC)['prev'];

            $currentCustomersQuery = "SELECT COUNT(*) as current FROM accounts 
                                    WHERE role = 'user' AND status = 'active' AND created_at >= :current_start AND created_at <= :current_end";
            $currentCustomersStmt = $conn->prepare($currentCustomersQuery);
            $currentCustomersStmt->bindParam(':current_start', $currentMonthStart);
            $currentCustomersStmt->bindParam(':current_end', $currentMonthEnd);
            $currentCustomersStmt->execute();
            $currentCustomers = $currentCustomersStmt->fetch(\PDO::FETCH_ASSOC)['current'];

            $customersGrowth = $prevCustomers > 0 ? round((($currentCustomers - $prevCustomers) / $prevCustomers) * 100) : 0;
            $customersGrowthText = $customersGrowth >= 0 ? '+' . $customersGrowth . '%' : $customersGrowth . '%';

            // Orders growth (if orders table exists)
            if ($hasOrdersTable) {
                $prevOrdersQuery = "SELECT COUNT(*) as prev FROM orders 
                                  WHERE status = 'completed' AND created_at >= :prev_start AND created_at <= :prev_end";
                $prevOrdersStmt = $conn->prepare($prevOrdersQuery);
                $prevOrdersStmt->bindParam(':prev_start', $prevMonthStart);
                $prevOrdersStmt->bindParam(':prev_end', $prevMonthEnd);
                $prevOrdersStmt->execute();
                $prevOrders = $prevOrdersStmt->fetch(\PDO::FETCH_ASSOC)['prev'];

                $currentOrdersQuery = "SELECT COUNT(*) as current FROM orders 
                                     WHERE status = 'completed' AND created_at >= :current_start AND created_at <= :current_end";
                $currentOrdersStmt = $conn->prepare($currentOrdersQuery);
                $currentOrdersStmt->bindParam(':current_start', $currentMonthStart);
                $currentOrdersStmt->bindParam(':current_end', $currentMonthEnd);
                $currentOrdersStmt->execute();
                $currentOrders = $currentOrdersStmt->fetch(\PDO::FETCH_ASSOC)['current'];

                $ordersGrowth = $prevOrders > 0 ? round((($currentOrders - $prevOrders) / $prevOrders) * 100) : 0;
                $ordersGrowthText = $ordersGrowth >= 0 ? '+' . $ordersGrowth . '%' : $ordersGrowth . '%';
            } else {
                $ordersGrowthText = '+0%';
            }

            $stats = [
                'products' => [
                    'total' => (int) $totalProducts,
                    'change' => $productsGrowthText,
                    'change_type' => $productsGrowth >= 0 ? 'increase' : 'decrease'
                ],
                'categories' => [
                    'total' => (int) $totalCategories,
                    'change' => $categoriesGrowthText,
                    'change_type' => $categoriesGrowth >= 0 ? 'increase' : 'decrease'
                ],
                'orders' => [
                    'total' => (int) $totalOrders,
                    'change' => $ordersGrowthText,
                    'change_type' => $ordersGrowth >= 0 ? 'increase' : 'decrease'
                ],
                'customers' => [
                    'total' => (int) $totalCustomers,
                    'change' => $customersGrowthText,
                    'change_type' => $customersGrowth >= 0 ? 'increase' : 'decrease'
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
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            // Check if orders table exists
            $tableCheckQuery = "SHOW TABLES LIKE 'orders'";
            $tableStmt = $conn->query($tableCheckQuery);
            $hasOrdersTable = $tableStmt->rowCount() > 0;

            if ($hasOrdersTable) {
                // Get best selling products from real order data
                $query = "SELECT 
                            p.id,
                            p.name,
                            c.name as category,
                            COALESCE(SUM(oi.quantity), 0) as sold
                          FROM products p
                          LEFT JOIN categories c ON p.category_id = c.id
                          LEFT JOIN order_items oi ON p.id = oi.product_id
                          LEFT JOIN orders o ON oi.order_id = o.id
                          WHERE p.deleted_at IS NULL 
                            AND (o.status = 'completed' OR o.status IS NULL)
                          GROUP BY p.id, p.name, c.name
                          ORDER BY sold DESC
                          LIMIT 5";

                $stmt = $conn->query($query);
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // Calculate change (compare with previous period)
                $bestSelling = [];
                foreach ($results as $row) {
                    // Get sales for current period (last 30 days)
                    $currentSold = (int) $row['sold'];

                    // Get sales for previous period (30-60 days ago)
                    $prevQuery = "SELECT COALESCE(SUM(oi.quantity), 0) as prev_sold
                                 FROM order_items oi
                                 JOIN orders o ON oi.order_id = o.id
                                 WHERE oi.product_id = :product_id 
                                 AND o.status = 'completed'
                                 AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
                                 AND o.created_at < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";

                    $prevStmt = $conn->prepare($prevQuery);
                    $prevStmt->bindParam(':product_id', $row['id']);
                    $prevStmt->execute();
                    $prevSold = (int) $prevStmt->fetch(\PDO::FETCH_ASSOC)['prev_sold'];

                    // Calculate change percentage
                    if ($prevSold > 0) {
                        $change = round((($currentSold - $prevSold) / $prevSold) * 100);
                        $changeText = ($change >= 0 ? '+' : '') . $change . '%';
                    } else {
                        $changeText = $currentSold > 0 ? '+100%' : '0%';
                    }

                    $bestSelling[] = [
                        'id' => (int) $row['id'],
                        'name' => $row['name'],
                        'category' => $row['category'] ?: 'Chưa phân loại',
                        'sold' => $currentSold,
                        'change' => $changeText
                    ];
                }
            } else {
                // Fallback: Show products by creation date if no orders table
                $query = "SELECT 
                            p.id,
                            p.name,
                            c.name as category,
                            0 as sold
                          FROM products p
                          LEFT JOIN categories c ON p.category_id = c.id
                          WHERE p.deleted_at IS NULL
                          ORDER BY p.created_at DESC
                          LIMIT 5";

                $stmt = $conn->query($query);
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $bestSelling = [];
                foreach ($results as $row) {
                    $bestSelling[] = [
                        'id' => (int) $row['id'],
                        'name' => $row['name'],
                        'category' => $row['category'] ?: 'Chưa phân loại',
                        'sold' => 0,
                        'change' => '0%'
                    ];
                }
            }

            $this->sendResponse($bestSelling, 'Best selling products retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve best selling products: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/dashboard/recent-activity - Lấy hoạt động gần đây
    public function getRecentActivity()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            $limit = 10; // Lấy 10 hoạt động gần đây nhất
            $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));

            // Kiểm tra xem bảng inventory_entries có tồn tại không
            $tableCheckQuery = "SHOW TABLES LIKE 'inventory_entries'";
            $tableStmt = $conn->query($tableCheckQuery);
            $hasInventoryTable = $tableStmt->rowCount() > 0;

            // Kiểm tra xem bảng orders có tồn tại không
            $ordersTableCheckQuery = "SHOW TABLES LIKE 'orders'";
            $ordersTableStmt = $conn->query($ordersTableCheckQuery);
            $hasOrdersTable = $ordersTableStmt->rowCount() > 0;

            $activities = [];

            // 1. Thêm sản phẩm mới (products.created_at)
            $productsCreatedQuery = "SELECT 
                id,
                CONCAT('Thêm sản phẩm mới: ', name) as action,
                created_at as activity_time,
                'create' as type
            FROM products 
            WHERE deleted_at IS NULL
                AND created_at >= :three_days_ago
            ORDER BY created_at DESC 
            LIMIT 10";
            $stmt = $conn->prepare($productsCreatedQuery);
            $stmt->bindParam(':three_days_ago', $threeDaysAgo);
            $stmt->execute();
            $productsCreated = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($productsCreated as $row) {
                $activities[] = [
                    'id' => 'product_create_' . $row['id'],
                    'action' => $row['action'],
                    'time' => $this->formatTimeAgo($row['activity_time']),
                    'type' => $row['type'],
                    'timestamp' => strtotime($row['activity_time'])
                ];
            }

            // 2. Cập nhật sản phẩm (products.updated_at khác created_at)
            $productsUpdatedQuery = "SELECT 
                id,
                CONCAT('Cập nhật sản phẩm: ', name) as action,
                updated_at as activity_time,
                'update' as type
            FROM products 
            WHERE deleted_at IS NULL 
                AND updated_at != created_at
                AND updated_at >= :three_days_ago
            ORDER BY updated_at DESC 
            LIMIT 10";
            $stmt = $conn->prepare($productsUpdatedQuery);
            $stmt->bindParam(':three_days_ago', $threeDaysAgo);
            $stmt->execute();
            $productsUpdated = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($productsUpdated as $row) {
                $activities[] = [
                    'id' => 'product_update_' . $row['id'],
                    'action' => $row['action'],
                    'time' => $this->formatTimeAgo($row['activity_time']),
                    'type' => $row['type'],
                    'timestamp' => strtotime($row['activity_time'])
                ];
            }

            // 3. Thêm danh mục mới (categories.created_at)
            $categoriesCreatedQuery = "SELECT 
                id,
                CONCAT('Thêm danh mục: ', name) as action,
                created_at as activity_time,
                'create' as type
            FROM categories 
            WHERE deleted_at IS NULL
                AND created_at >= :three_days_ago
            ORDER BY created_at DESC 
            LIMIT 10";
            $stmt = $conn->prepare($categoriesCreatedQuery);
            $stmt->bindParam(':three_days_ago', $threeDaysAgo);
            $stmt->execute();
            $categoriesCreated = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($categoriesCreated as $row) {
                $activities[] = [
                    'id' => 'category_create_' . $row['id'],
                    'action' => $row['action'],
                    'time' => $this->formatTimeAgo($row['activity_time']),
                    'type' => $row['type'],
                    'timestamp' => strtotime($row['activity_time'])
                ];
            }

            // 4. Cập nhật danh mục (categories.updated_at khác created_at)
            $categoriesUpdatedQuery = "SELECT 
                id,
                CONCAT('Cập nhật danh mục: ', name) as action,
                updated_at as activity_time,
                'update' as type
            FROM categories 
            WHERE deleted_at IS NULL 
                AND updated_at != created_at
                AND updated_at >= :three_days_ago
            ORDER BY updated_at DESC 
            LIMIT 10";
            $stmt = $conn->prepare($categoriesUpdatedQuery);
            $stmt->bindParam(':three_days_ago', $threeDaysAgo);
            $stmt->execute();
            $categoriesUpdated = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($categoriesUpdated as $row) {
                $activities[] = [
                    'id' => 'category_update_' . $row['id'],
                    'action' => $row['action'],
                    'time' => $this->formatTimeAgo($row['activity_time']),
                    'type' => $row['type'],
                    'timestamp' => strtotime($row['activity_time'])
                ];
            }

            // 5. Khách hàng mới (accounts với role='user')
            $customersQuery = "SELECT 
                a.id,
                CONCAT('Khách hàng mới: ', COALESCE(u.full_name, a.email)) as action,
                a.created_at as activity_time,
                'create' as type
            FROM accounts a
            LEFT JOIN users u ON u.account_id = a.id
            WHERE a.role = 'user' AND a.status = 'active'
                AND a.created_at >= :three_days_ago
            ORDER BY a.created_at DESC 
            LIMIT 10";
            $stmt = $conn->prepare($customersQuery);
            $stmt->bindParam(':three_days_ago', $threeDaysAgo);
            $stmt->execute();
            $customers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($customers as $row) {
                $activities[] = [
                    'id' => 'customer_create_' . $row['id'],
                    'action' => $row['action'],
                    'time' => $this->formatTimeAgo($row['activity_time']),
                    'type' => $row['type'],
                    'timestamp' => strtotime($row['activity_time'])
                ];
            }

            // 6. Đơn hàng mới (orders.created_at)
            if ($hasOrdersTable) {
                $ordersQuery = "SELECT 
                    id,
                    CONCAT('Đơn hàng mới #', id) as action,
                    created_at as activity_time,
                    'create' as type
                FROM orders 
                WHERE created_at >= :three_days_ago
                ORDER BY created_at DESC 
                LIMIT 10";
                $stmt = $conn->prepare($ordersQuery);
                $stmt->bindParam(':three_days_ago', $threeDaysAgo);
                $stmt->execute();
                $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($orders as $row) {
                    $activities[] = [
                        'id' => 'order_create_' . $row['id'],
                        'action' => $row['action'],
                        'time' => $this->formatTimeAgo($row['activity_time']),
                        'type' => $row['type'],
                        'timestamp' => strtotime($row['activity_time'])
                    ];
                }
            }

            // 7. Nhập kho (inventory_entries.created_at)
            if ($hasInventoryTable) {
                $inventoryQuery = "SELECT 
                    ie.id,
                    CONCAT('Nhập kho: ', p.name, ' (', ie.quantity, ' sản phẩm)') as action,
                    ie.created_at as activity_time,
                    'create' as type
                FROM inventory_entries ie
                JOIN products p ON p.id = ie.product_id
                WHERE p.deleted_at IS NULL
                    AND ie.created_at >= :three_days_ago
                ORDER BY ie.created_at DESC 
                LIMIT 10";
                $stmt = $conn->prepare($inventoryQuery);
                $stmt->bindParam(':three_days_ago', $threeDaysAgo);
                $stmt->execute();
                $inventoryEntries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($inventoryEntries as $row) {
                    $activities[] = [
                        'id' => 'inventory_' . $row['id'],
                        'action' => $row['action'],
                        'time' => $this->formatTimeAgo($row['activity_time']),
                        'type' => $row['type'],
                        'timestamp' => strtotime($row['activity_time'])
                    ];
                }
            }

            // Sắp xếp theo thời gian (mới nhất trước) và lấy top N
            usort($activities, function ($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

            // Lấy top N và loại bỏ timestamp (nếu không đủ thì lấy những cái có)
            $activities = array_slice($activities, 0, $limit);
            foreach ($activities as &$activity) {
                unset($activity['timestamp']);
            }

            $this->sendResponse($activities, 'Recent activity retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve recent activity: ' . $e->getMessage(), 500);
        }
    }

    // Helper function để format thời gian "X phút/giờ/ngày trước"
    private function formatTimeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return $diff . ' giây trước';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' phút trước';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' giờ trước';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' ngày trước';
        } elseif ($diff < 31536000) {
            $months = floor($diff / 2592000);
            return $months . ' tháng trước';
        } else {
            $years = floor($diff / 31536000);
            return $years . ' năm trước';
        }
    }

    // GET /api/dashboard/category-distribution - Lấy phân bố danh mục
    public function getCategoryDistribution()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            // Get products count by category
            $query = "SELECT c.name, c.id, COUNT(p.id) as product_count
                      FROM categories c
                      LEFT JOIN products p ON c.id = p.category_id
                      WHERE c.deleted_at IS NULL AND (p.deleted_at IS NULL OR p.deleted_at IS NULL)
                      GROUP BY c.id, c.name
                      ORDER BY product_count DESC";

            $stmt = $conn->query($query);
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $totalProducts = 0;
            foreach ($results as $row) {
                $totalProducts += $row['product_count'];
            }

            $distribution = [];
            $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];
            $colorIndex = 0;

            foreach ($results as $row) {
                $percentage = $totalProducts > 0 ? round(($row['product_count'] / $totalProducts) * 100) : 0;
                $distribution[] = [
                    'name' => $row['name'],
                    'value' => $percentage,
                    'color' => $colors[$colorIndex % count($colors)]
                ];
                $colorIndex++;
            }

            $this->sendResponse($distribution, 'Category distribution retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve category distribution: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/dashboard/monthly-stats - Lấy thống kê theo tháng
    public function getMonthlyStats()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            // Get date range from query parameters
            $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-5 months'));
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            // Get product creation data for the specified period
            $query = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as count
                      FROM products
                      WHERE created_at >= :start_date 
                        AND created_at <= :end_date
                        AND deleted_at IS NULL
                      GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                      ORDER BY month ASC";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Generate month labels
            $monthLabels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'];
            $monthlyData = [];

            // Initialize all months with 0
            for ($i = 0; $i < 6; $i++) {
                $monthlyData[] = [
                    'name' => $monthLabels[$i],
                    'value' => 0
                ];
            }

            // Fill in actual data
            $index = 0;
            foreach ($results as $row) {
                if ($index < 6) {
                    $monthlyData[$index]['value'] = (int) $row['count'];
                    $index++;
                }
            }

            $this->sendResponse($monthlyData, 'Monthly stats retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve monthly stats: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/dashboard/customer-stats - Lấy thống kê khách hàng
    public function getCustomerStats()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            // Get date range from query parameters
            $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-5 months'));
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            // Get total customers (users with role 'user')
            $totalQuery = "SELECT COUNT(*) as total FROM accounts WHERE role = 'user' AND status = 'active'";
            $totalStmt = $conn->query($totalQuery);
            $totalCustomers = $totalStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get new customers in date range
            $newQuery = "SELECT COUNT(*) as new FROM accounts 
                        WHERE role = 'user' AND status = 'active' 
                        AND created_at >= :start_date AND created_at <= :end_date";
            $newStmt = $conn->prepare($newQuery);
            $newStmt->bindParam(':start_date', $startDate);
            $newStmt->bindParam(':end_date', $endDate);
            $newStmt->execute();
            $newCustomers = $newStmt->fetch(\PDO::FETCH_ASSOC)['new'];

            // Get returning customers (customers with recent activity)
            // Check if lasted_login column exists first
            $columnCheckQuery = "SHOW COLUMNS FROM accounts LIKE 'lasted_login'";
            $columnStmt = $conn->query($columnCheckQuery);
            $hasLastedLoginColumn = $columnStmt->rowCount() > 0;

            if ($hasLastedLoginColumn) {
                $returningQuery = "SELECT COUNT(*) as returning FROM accounts 
                                 WHERE role = 'user' AND status = 'active' 
                                 AND lasted_login IS NOT NULL 
                                 AND lasted_login >= :start_date";
                $returningStmt = $conn->prepare($returningQuery);
                $returningStmt->bindParam(':start_date', $startDate);
                $returningStmt->execute();
                $returningCustomers = $returningStmt->fetch(\PDO::FETCH_ASSOC)['returning'];
            } else {
                // Fallback: count customers who have been active (created recently)
                $returningQuery = "SELECT COUNT(*) as returning FROM accounts 
                                 WHERE role = 'user' AND status = 'active' 
                                 AND created_at >= :start_date";
                $returningStmt = $conn->prepare($returningQuery);
                $returningStmt->bindParam(':start_date', $startDate);
                $returningStmt->execute();
                $returningCustomers = $returningStmt->fetch(\PDO::FETCH_ASSOC)['returning'];
            }

            // Calculate growth (compare with previous period)
            $prevStartDate = date('Y-m-01', strtotime($startDate . ' -1 month'));
            $prevEndDate = date('Y-m-t', strtotime($startDate . ' -1 month'));

            $prevQuery = "SELECT COUNT(*) as prev FROM accounts 
                         WHERE role = 'user' AND status = 'active' 
                         AND created_at >= :prev_start AND created_at <= :prev_end";
            $prevStmt = $conn->prepare($prevQuery);
            $prevStmt->bindParam(':prev_start', $prevStartDate);
            $prevStmt->bindParam(':prev_end', $prevEndDate);
            $prevStmt->execute();
            $prevCustomers = $prevStmt->fetch(\PDO::FETCH_ASSOC)['prev'];

            $growth = $prevCustomers > 0 ? round((($newCustomers - $prevCustomers) / $prevCustomers) * 100) : 0;
            $growthText = $growth >= 0 ? '+' . $growth . '%' : $growth . '%';

            // Get customer distribution by city (from addresses table)
            $cityQuery = "SELECT 
                            COALESCE(addr.city, addr.province, 'Không xác định') as city_name,
                            COUNT(DISTINCT u.id) as count 
                         FROM users u 
                         JOIN accounts a ON u.account_id = a.id 
                         LEFT JOIN addresses addr ON u.id = addr.user_id AND addr.is_default = 1
                         WHERE a.role = 'user' AND a.status = 'active' 
                         AND (addr.city IS NOT NULL OR addr.province IS NOT NULL)
                         GROUP BY COALESCE(addr.city, addr.province, 'Không xác định')
                         ORDER BY count DESC 
                         LIMIT 5";
            $cityStmt = $conn->query($cityQuery);
            $cityResults = $cityStmt->fetchAll(\PDO::FETCH_ASSOC);

            $topCities = [];
            foreach ($cityResults as $row) {
                $topCities[] = [
                    'name' => $row['city_name'] ?: 'Không xác định',
                    'count' => (int) $row['count']
                ];
            }

            // If no cities, add default data
            if (empty($topCities)) {
                $topCities = [
                    ['name' => 'Hồ Chí Minh', 'count' => 0],
                    ['name' => 'Hà Nội', 'count' => 0],
                    ['name' => 'Đà Nẵng', 'count' => 0],
                    ['name' => 'Cần Thơ', 'count' => 0],
                    ['name' => 'Khác', 'count' => 0]
                ];
            }

            $customerStats = [
                'total_customers' => (int) $totalCustomers,
                'new_customers' => (int) $newCustomers,
                'returning_customers' => (int) $returningCustomers,
                'customer_growth' => $growthText,
                'top_cities' => $topCities
            ];

            $this->sendResponse($customerStats, 'Customer stats retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve customer stats: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/dashboard/revenue-stats - Lấy thống kê doanh thu
    public function getRevenueStats()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            // Get date range from query parameters
            $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-5 months'));
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            // Check if orders table exists
            $tableCheckQuery = "SHOW TABLES LIKE 'orders'";
            $tableStmt = $conn->query($tableCheckQuery);
            $hasOrdersTable = $tableStmt->rowCount() > 0;

            if ($hasOrdersTable) {
                // Real revenue data from orders table
                $totalRevenueQuery = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders 
                                    WHERE status = 'completed' AND created_at >= :start_date AND created_at <= :end_date";
                $totalStmt = $conn->prepare($totalRevenueQuery);
                $totalStmt->bindParam(':start_date', $startDate);
                $totalStmt->bindParam(':end_date', $endDate);
                $totalStmt->execute();
                $totalRevenue = $totalStmt->fetch(\PDO::FETCH_ASSOC)['total'];

                // Monthly revenue
                $monthlyRevenueQuery = "SELECT COALESCE(SUM(total_amount), 0) as monthly FROM orders 
                                      WHERE status = 'completed' 
                                      AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
                $monthlyStmt = $conn->query($monthlyRevenueQuery);
                $monthlyRevenue = $monthlyStmt->fetch(\PDO::FETCH_ASSOC)['monthly'];

                // Top products by revenue
                $topProductsQuery = "SELECT p.name, SUM(oi.quantity * oi.price) as revenue
                                   FROM order_items oi
                                   JOIN products p ON oi.product_id = p.id
                                   JOIN orders o ON oi.order_id = o.id
                                   WHERE o.status = 'completed' 
                                   AND o.created_at >= :start_date AND o.created_at <= :end_date
                                   GROUP BY p.id, p.name
                                   ORDER BY revenue DESC
                                   LIMIT 5";
                $topProductsStmt = $conn->prepare($topProductsQuery);
                $topProductsStmt->bindParam(':start_date', $startDate);
                $topProductsStmt->bindParam(':end_date', $endDate);
                $topProductsStmt->execute();
                $topProducts = $topProductsStmt->fetchAll(\PDO::FETCH_ASSOC);

                // Revenue by month
                $revenueByMonthQuery = "SELECT 
                                       DATE_FORMAT(created_at, '%Y-%m') as month,
                                       COALESCE(SUM(total_amount), 0) as revenue
                                       FROM orders 
                                       WHERE status = 'completed' 
                                       AND created_at >= :start_date AND created_at <= :end_date
                                       GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                                       ORDER BY month ASC";
                $revenueByMonthStmt = $conn->prepare($revenueByMonthQuery);
                $revenueByMonthStmt->bindParam(':start_date', $startDate);
                $revenueByMonthStmt->bindParam(':end_date', $endDate);
                $revenueByMonthStmt->execute();
                $revenueByMonthResults = $revenueByMonthStmt->fetchAll(\PDO::FETCH_ASSOC);

                // Calculate growth
                $prevStartDate = date('Y-m-01', strtotime($startDate . ' -1 month'));
                $prevEndDate = date('Y-m-t', strtotime($startDate . ' -1 month'));

                $prevRevenueQuery = "SELECT COALESCE(SUM(total_amount), 0) as prev FROM orders 
                                   WHERE status = 'completed' AND created_at >= :prev_start AND created_at <= :prev_end";
                $prevRevenueStmt = $conn->prepare($prevRevenueQuery);
                $prevRevenueStmt->bindParam(':prev_start', $prevStartDate);
                $prevRevenueStmt->bindParam(':prev_end', $prevEndDate);
                $prevRevenueStmt->execute();
                $prevRevenue = $prevRevenueStmt->fetch(\PDO::FETCH_ASSOC)['prev'];

                $growth = $prevRevenue > 0 ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100) : 0;
                $growthText = $growth >= 0 ? '+' . $growth . '%' : $growth . '%';

                // Average order value
                $avgOrderQuery = "SELECT COALESCE(AVG(total_amount), 0) as avg_order FROM orders 
                                WHERE status = 'completed' AND created_at >= :start_date AND created_at <= :end_date";
                $avgOrderStmt = $conn->prepare($avgOrderQuery);
                $avgOrderStmt->bindParam(':start_date', $startDate);
                $avgOrderStmt->bindParam(':end_date', $endDate);
                $avgOrderStmt->execute();
                $avgOrderValue = $avgOrderStmt->fetch(\PDO::FETCH_ASSOC)['avg_order'];

                $revenueStats = [
                    'total_revenue' => (int) $totalRevenue,
                    'monthly_revenue' => (int) $monthlyRevenue,
                    'revenue_growth' => $growthText,
                    'average_order_value' => (int) $avgOrderValue,
                    'top_products' => $topProducts,
                    'revenue_by_month' => $revenueByMonthResults
                ];
            } else {
                // Fallback to mock data when orders table doesn't exist
                $revenueStats = [
                    'total_revenue' => 0,
                    'monthly_revenue' => 0,
                    'revenue_growth' => '+0%',
                    'average_order_value' => 0,
                    'top_products' => [],
                    'revenue_by_month' => []
                ];
            }

            $this->sendResponse($revenueStats, 'Revenue stats retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve revenue stats: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/statistics/top-products - Top sản phẩm bán chạy
    public function getTopProducts()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            $limit = (int) ($_GET['limit'] ?? 10);
            $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-6 months'));
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            // Check if orders table exists
            $tableCheckQuery = "SHOW TABLES LIKE 'orders'";
            $tableStmt = $conn->query($tableCheckQuery);
            $hasOrdersTable = $tableStmt->rowCount() > 0;

            if ($hasOrdersTable) {
                $query = "SELECT 
                            p.id,
                            p.name,
                            p.price,
                            c.name as category_name,
                            COALESCE(SUM(oi.quantity), 0) as total_sold,
                            COALESCE(SUM(oi.quantity * oi.price), 0) as total_revenue,
                            COUNT(DISTINCT o.id) as order_count
                          FROM products p
                          LEFT JOIN categories c ON p.category_id = c.id
                          LEFT JOIN order_items oi ON p.id = oi.product_id
                          LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'completed'
                          WHERE p.deleted_at IS NULL 
                            AND (o.created_at IS NULL OR (o.created_at >= :start_date AND o.created_at <= :end_date))
                          GROUP BY p.id, p.name, p.price, c.name
                          ORDER BY total_sold DESC, total_revenue DESC
                          LIMIT :limit";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':start_date', $startDate);
                $stmt->bindParam(':end_date', $endDate);
                $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $topProducts = [];
                foreach ($results as $index => $row) {
                    $topProducts[] = [
                        'rank' => $index + 1,
                        'id' => (int) $row['id'],
                        'name' => $row['name'],
                        'price' => (float) $row['price'],
                        'category' => $row['category_name'] ?: 'Chưa phân loại',
                        'total_sold' => (int) $row['total_sold'],
                        'total_revenue' => (float) $row['total_revenue'],
                        'order_count' => (int) $row['order_count']
                    ];
                }
            } else {
                // Fallback: Show products by creation date
                $query = "SELECT 
                            p.id,
                            p.name,
                            p.price,
                            c.name as category_name,
                            0 as total_sold,
                            0 as total_revenue,
                            0 as order_count
                          FROM products p
                          LEFT JOIN categories c ON p.category_id = c.id
                          WHERE p.deleted_at IS NULL
                          ORDER BY p.created_at DESC
                          LIMIT :limit";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $topProducts = [];
                foreach ($results as $index => $row) {
                    $topProducts[] = [
                        'rank' => $index + 1,
                        'id' => (int) $row['id'],
                        'name' => $row['name'],
                        'price' => (float) $row['price'],
                        'category' => $row['category_name'] ?: 'Chưa phân loại',
                        'total_sold' => 0,
                        'total_revenue' => 0,
                        'order_count' => 0
                    ];
                }
            }

            $this->sendResponse($topProducts, 'Top products retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve top products: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/statistics/hot-categories - Danh mục hot
    public function getHotCategories()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            $limit = (int) ($_GET['limit'] ?? 10);
            $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-6 months'));
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            // Check if orders table exists
            $tableCheckQuery = "SHOW TABLES LIKE 'orders'";
            $tableStmt = $conn->query($tableCheckQuery);
            $hasOrdersTable = $tableStmt->rowCount() > 0;

            if ($hasOrdersTable) {
                $query = "SELECT 
                            c.id,
                            c.name,
                            COUNT(DISTINCT o.id) as order_count,
                            COALESCE(SUM(oi.quantity), 0) as total_sold,
                            COALESCE(SUM(oi.quantity * oi.price), 0) as total_revenue,
                            COUNT(DISTINCT p.id) as product_count
                          FROM categories c
                          LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
                          LEFT JOIN order_items oi ON p.id = oi.product_id
                          LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'completed'
                          WHERE c.deleted_at IS NULL
                            AND (o.created_at IS NULL OR (o.created_at >= :start_date AND o.created_at <= :end_date))
                          GROUP BY c.id, c.name
                          HAVING order_count > 0 OR total_sold > 0
                          ORDER BY total_revenue DESC, total_sold DESC, order_count DESC
                          LIMIT :limit";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':start_date', $startDate);
                $stmt->bindParam(':end_date', $endDate);
                $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $hotCategories = [];
                foreach ($results as $index => $row) {
                    $hotCategories[] = [
                        'rank' => $index + 1,
                        'id' => (int) $row['id'],
                        'name' => $row['name'],
                        'order_count' => (int) $row['order_count'],
                        'total_sold' => (int) $row['total_sold'],
                        'total_revenue' => (float) $row['total_revenue'],
                        'product_count' => (int) $row['product_count']
                    ];
                }
            } else {
                // Fallback: Show categories by product count
                $query = "SELECT 
                            c.id,
                            c.name,
                            0 as order_count,
                            0 as total_sold,
                            0 as total_revenue,
                            COUNT(p.id) as product_count
                          FROM categories c
                          LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
                          WHERE c.deleted_at IS NULL
                          GROUP BY c.id, c.name
                          ORDER BY product_count DESC
                          LIMIT :limit";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $hotCategories = [];
                foreach ($results as $index => $row) {
                    $hotCategories[] = [
                        'rank' => $index + 1,
                        'id' => (int) $row['id'],
                        'name' => $row['name'],
                        'order_count' => 0,
                        'total_sold' => 0,
                        'total_revenue' => 0,
                        'product_count' => (int) $row['product_count']
                    ];
                }
            }

            $this->sendResponse($hotCategories, 'Hot categories retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve hot categories: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/statistics/top-customers - Khách hàng mua nhiều đơn
    public function getTopCustomers()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            $limit = (int) ($_GET['limit'] ?? 10);
            $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-6 months'));
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            // Check if orders table exists
            $tableCheckQuery = "SHOW TABLES LIKE 'orders'";
            $tableStmt = $conn->query($tableCheckQuery);
            $hasOrdersTable = $tableStmt->rowCount() > 0;

            if ($hasOrdersTable) {
                $query = "SELECT 
                            u.id,
                            u.full_name,
                            a.email,
                            COUNT(DISTINCT o.id) as order_count,
                            COALESCE(SUM(o.total_amount), 0) as total_spent,
                            COALESCE(SUM(oi.quantity), 0) as total_items,
                            MAX(o.created_at) as last_order_date,
                            MIN(o.created_at) as first_order_date
                          FROM users u
                          JOIN accounts a ON u.account_id = a.id
                          LEFT JOIN orders o ON u.id = o.user_id 
                            AND o.status = 'completed'
                            AND o.created_at >= :start_date 
                            AND o.created_at <= :end_date
                          LEFT JOIN order_items oi ON o.id = oi.order_id
                          WHERE a.role = 'user' AND a.status = 'active'
                          GROUP BY u.id, u.full_name, a.email
                          HAVING order_count > 0
                          ORDER BY total_spent DESC, order_count DESC
                          LIMIT :limit";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':start_date', $startDate);
                $stmt->bindParam(':end_date', $endDate);
                $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $topCustomers = [];
                foreach ($results as $index => $row) {
                    $topCustomers[] = [
                        'rank' => $index + 1,
                        'id' => (int) $row['id'],
                        'full_name' => $row['full_name'] ?: 'Khách hàng',
                        'email' => $row['email'],
                        'order_count' => (int) $row['order_count'],
                        'total_spent' => (float) $row['total_spent'],
                        'total_items' => (int) $row['total_items'],
                        'average_order_value' => $row['order_count'] > 0
                            ? round($row['total_spent'] / $row['order_count'], 2)
                            : 0,
                        'last_order_date' => $row['last_order_date'],
                        'first_order_date' => $row['first_order_date']
                    ];
                }
            } else {
                // Fallback: Show active users
                $query = "SELECT 
                            u.id,
                            u.full_name,
                            a.email,
                            0 as order_count,
                            0 as total_spent,
                            0 as total_items,
                            NULL as last_order_date,
                            NULL as first_order_date
                          FROM users u
                          JOIN accounts a ON u.account_id = a.id
                          WHERE a.role = 'user' AND a.status = 'active'
                          ORDER BY u.created_at DESC
                          LIMIT :limit";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $topCustomers = [];
                foreach ($results as $index => $row) {
                    $topCustomers[] = [
                        'rank' => $index + 1,
                        'id' => (int) $row['id'],
                        'full_name' => $row['full_name'] ?: 'Khách hàng',
                        'email' => $row['email'],
                        'order_count' => 0,
                        'total_spent' => 0,
                        'total_items' => 0,
                        'average_order_value' => 0,
                        'last_order_date' => null,
                        'first_order_date' => null
                    ];
                }
            }

            $this->sendResponse($topCustomers, 'Top customers retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to retrieve top customers: ' . $e->getMessage(), 500);
        }
    }
}
