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

    // Simple admin check using session + accounts.role
    private function ensureAdmin()
    {
        // Ensure session like other controllers
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (empty($_SESSION['account_id'])) {
            $this->sendError('Unauthorized: not signed in', 401);
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT role FROM accounts WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $_SESSION['account_id']]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row || ($row['role'] ?? 'user') !== 'admin') {
            $this->sendError('Forbidden: admin access only', 403);
        }
    }

    /**
     * GET /api/orders - Admin: list orders (paginated, filter by status)
     */
    public function index()
    {
        try {
            $this->ensureAdmin();

            $page = (int) ($_GET['page'] ?? 1);
            $limit = (int) ($_GET['limit'] ?? 20);
            $status = $_GET['status'] ?? null;

            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 200) $limit = 20;

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
            $this->ensureAdmin();

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
     * POST /api/orders/{id}/approve - Admin: approve order
     * Assumption: approving sets status to 'paid' (modify if you prefer 'shipped')
     */
    public function approve($id)
    {
        try {
            $this->ensureAdmin();

            $order = $this->orderModel->findById($id);
            if (!$order) {
                $this->sendError('Order not found', 404);
            }

            // Only allow approving pending orders
            if ($order['status'] !== 'pending') {
                $this->sendError('Only pending orders can be approved', 400);
            }

            $now = date('Y-m-d H:i:s');
            $result = $this->orderModel->update($id, ['status' => 'paid', 'updated_at' => $now]);

            if ($result) {
                $updated = $this->orderModel->findById($id);
                $updated['items'] = $this->orderItemModel->getItemsByOrder($id);
                $this->sendResponse($updated, 'Order approved');
            } else {
                $this->sendError('Failed to update order status', 500);
            }
        } catch (\Exception $e) {
            $this->sendError('Failed to approve order: ' . $e->getMessage(), 500);
        }
    }
}
