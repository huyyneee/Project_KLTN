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

            if ($page < 1)
                $page = 1;
            if ($limit < 1 || $limit > 200)
                $limit = 20;

            $offset = ($page - 1) * $limit;

            $db = Database::getInstance()->getConnection();

            // Count
            if ($status) {
                $countStmt = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE status = :status");
                $countStmt->execute([':status' => $status]);
            } else {
                $countStmt = $db->query("SELECT COUNT(*) as total FROM orders");
            }
            $totalRecords = (int) ($countStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

            // Fetch orders
            if ($status) {
                $stmt = $db->prepare("SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':status', $status);
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
                $stmt->execute();
            }

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

            $this->sendResponse($response, 'Orders retrieved');
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

    /**
     * PATCH /api/orders/{id}/status - Admin: update order status with validation
     * Allowed statuses: pending, paid, shipped, completed, cancelled
     * Transitions:
     *  - pending -> paid | cancelled
     *  - paid -> shipped | cancelled
     *  - shipped -> completed
     *  - completed, cancelled -> (no further transitions)
     */
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
            $result = $this->orderModel->update($id, ['status' => $targetStatus, 'updated_at' => $now]);

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
