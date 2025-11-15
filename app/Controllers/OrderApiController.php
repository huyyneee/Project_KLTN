<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Models/OrderItemModel.php';
require_once __DIR__ . '/../Core/Database.php';

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Core\Database;

class OrderApiController extends ApiController
{
    private $orderModel;
    private $orderItemModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    /**
     * GET /api/orders - Admin: list orders (paginated, filter by status)
     */
    public function index()
    {
        try {

            $page = (int) ($_GET['page'] ?? 1);
            $limit = (int) ($_GET['limit'] ?? 20);
            $status = $_GET['status'] ?? null;
            $keyword = isset($_GET['q']) ? trim((string) $_GET['q']) : null;

            if ($page < 1)
                $page = 1;
            if ($limit < 1 || $limit > 200)
                $limit = 20;

            $offset = ($page - 1) * $limit;

            $db = Database::getInstance()->getConnection();

            $whereClauses = [];
            $params = [];
            if ($status) {
                $whereClauses[] = 'status = :status';
                $params[':status'] = $status;
            }
            if ($keyword !== null && $keyword !== '') {
                // Use separate placeholders for each LIKE condition to avoid parameter binding issues
                $whereClauses[] = '(order_code LIKE :kw1 OR receiver_name LIKE :kw2 OR receiver_phone LIKE :kw3 OR shipping_address LIKE :kw4)';
                $keywordValue = '%' . $keyword . '%';
                $params[':kw1'] = $keywordValue;
                $params[':kw2'] = $keywordValue;
                $params[':kw3'] = $keywordValue;
                $params[':kw4'] = $keywordValue;
            }
            $whereSql = '';
            if (!empty($whereClauses)) {
                $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
            }

            // Count
            $countSql = "SELECT COUNT(*) as total FROM orders $whereSql";
            $countStmt = $db->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $totalRecords = (int) ($countStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

            // Fetch orders
            $sql = "SELECT * FROM orders $whereSql ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Attach items for each order
            foreach ($orders as &$order) {
                $order['items'] = $this->orderItemModel->getItemsByOrder($order['id']);
            }

            $totalPages = (int) ceil($totalRecords / $limit);

            $response = [
                'orders' => $orders,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_records' => $totalRecords,
                    'limit' => $limit
                ]
            ];

            $message = 'Orders retrieved';
            if ($keyword !== null && $keyword !== '') {
                $message .= ' (search: "' . $keyword . '")';
            }
            $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            $this->sendError('Failed to load orders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/orders/{id} - Admin: get single order
     */
    public function show($id)
    {
        try {

            $order = $this->orderModel->findById($id);
            if (!$order) {
                $this->sendError('Order not found', 404);
            }

            $order['items'] = $this->orderItemModel->getItemsByOrder($order['id']);

            $this->sendResponse($order, 'Order retrieved');
        } catch (\Exception $e) {
            $this->sendError('Failed to load order: ' . $e->getMessage(), 500);
        }
    }

    public function updateStatus($id)
    {
        try {

            $order = $this->orderModel->findById($id);
            if (!$order) {
                $this->sendError('Order not found', 404);
            }

            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $this->sendError('Invalid JSON body', 400);
            }

            $targetStatus = strtolower(trim($data['status'] ?? ''));
            if ($targetStatus === '') {
                $this->sendError('Missing required field: status', 400);
            }

            $allowedStatuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
            if (!in_array($targetStatus, $allowedStatuses, true)) {
                $this->sendError('Invalid status value', 400);
            }

            $currentStatus = strtolower($order['status'] ?? '');
            $transitions = [
                'pending' => ['paid', 'cancelled'],
                'paid' => ['shipped', 'cancelled'],
                'shipped' => ['completed'],
                'completed' => [],
                'cancelled' => []
            ];

            if (!array_key_exists($currentStatus, $transitions)) {
                $this->sendError('Order has unknown current status', 400);
            }

            if ($currentStatus === $targetStatus) {
                $this->sendError('Order already in target status', 400);
            }

            if (!in_array($targetStatus, $transitions[$currentStatus], true)) {
                $this->sendError('Invalid status transition', 400);
            }

            $now = date('Y-m-d H:i:s');

            // If moving from paid -> shipped, deduct inventory for each order item atomically
            if ($currentStatus === 'paid' && $targetStatus === 'shipped') {
                $db = Database::getInstance()->getConnection();
                $db->beginTransaction();
                try {
                    // Fetch order items
                    $items = $this->orderItemModel->getItemsByOrder($id);
                    if (!is_array($items)) {
                        throw new \Exception('Failed to load order items for stock deduction');
                    }

                    // Lock and update each product's quantity
                    $selectStmt = $db->prepare("SELECT id, name, quantity FROM products WHERE id = :pid FOR UPDATE");
                    $updateStmt = $db->prepare("UPDATE products SET quantity = :qty, updated_at = CURRENT_TIMESTAMP WHERE id = :pid");

                    foreach ($items as $item) {
                        $productId = (int) ($item['product_id'] ?? 0);
                        $qtyToDeduct = (int) ($item['quantity'] ?? 0);
                        if ($productId <= 0 || $qtyToDeduct <= 0) {
                            continue;
                        }

                        $selectStmt->execute([':pid' => $productId]);
                        $product = $selectStmt->fetch(\PDO::FETCH_ASSOC);
                        if (!$product) {
                            throw new \Exception('Product not found for order item (ID: ' . $productId . ')');
                        }

                        $currentQty = (int) ($product['quantity'] ?? 0);
                        $newQty = $currentQty - $qtyToDeduct;
                        if ($newQty < 0) {
                            throw new \Exception('Insufficient stock for product ID ' . $productId . ' (' . ($product['name'] ?? 'Unknown') . '). Required: ' . $qtyToDeduct . ', Available: ' . $currentQty);
                        }

                        $updateStmt->execute([':qty' => $newQty, ':pid' => $productId]);
                    }

                    // Update order status
                    $result = $this->orderModel->update($id, ['status' => $targetStatus, 'updated_at' => $now]);
                    if (!$result) {
                        throw new \Exception('Failed to update order status');
                    }

                    $db->commit();
                } catch (\Exception $txe) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    $this->sendError('Failed to ship order: ' . $txe->getMessage(), 400);
                }
            } else {
                // Other transitions: just update status
                $result = $this->orderModel->update($id, ['status' => $targetStatus, 'updated_at' => $now]);
            }

            if ($result) {
                $updated = $this->orderModel->findById($id);
                $updated['items'] = $this->orderItemModel->getItemsByOrder($id);
                $this->sendResponse($updated, 'Order status updated');
            } else {
                $this->sendError('Failed to update order status', 500);
            }
        } catch (\Exception $e) {
            $this->sendError('Failed to update order status: ' . $e->getMessage(), 500);
        }
    }

}
