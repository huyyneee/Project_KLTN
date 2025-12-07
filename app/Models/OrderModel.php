<?php

namespace App\Models;

use App\Core\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'total_amount',
        'receiver_name',
        'preceiver_phone',
        'shipping_address',
        'payment_method',
        'created_at',
        'updated_at'
    ];

    public function insertOrder($data)
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ")
                VALUES (" . implode(',', $placeholders) . ")";

        $stmt = $this->db->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getOrdersWithItemsPaginated($userId, $limit, $offset, $status = null)
    {
        // --- Lấy danh sách đơn hàng có phân trang ---
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        $sql .= " ORDER BY 
        CASE 
            WHEN status = 'pending' THEN 1
            WHEN status = 'paid' THEN 2
            WHEN status = 'shipped' THEN 3
            WHEN status = 'completed' THEN 4
            WHEN status = 'cancelled' THEN 5
            ELSE 6
        END,
        created_at DESC
        LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        if ($status !== null) {
            $stmt->bindValue(':status', $status, \PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // --- Chuẩn bị truy vấn chi tiết sản phẩm ---
        $stmtItem = $this->db->prepare("
        SELECT i.*, 
               p.name AS product_name,
               p.price AS product_price,
               pi.url AS product_image
        FROM order_items i
        JOIN products p ON i.product_id = p.id
        LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
        WHERE i.order_id = :order_id
    ");

        // --- Lấy host để xử lý link ảnh (giống hàm cũ) ---
        $dbHost = null;
        $cfgPath = __DIR__ . '/../../config/config.php';
        if (file_exists($cfgPath)) {
            $cfg = require $cfgPath;
            $dbHost = $cfg['database']['host'] ?? null;
        }

        // --- Gắn items và xử lý đường dẫn ảnh ---
        foreach ($orders as &$order) {
            $stmtItem->bindValue(':order_id', $order['id'], \PDO::PARAM_INT);
            $stmtItem->execute();
            $items = $stmtItem->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($items as &$item) {
                $img = trim($item['product_image'] ?? '');
                if (!empty($img)) {
                    $item['product_image'] = \get_image_url($img);
                } else {
                    $item['product_image'] = null;
                }
            }

            $order['items'] = $items;
        }

        return $orders;
    }
    public function countOrders($userId, $status = null)
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE user_id = :user_id";
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        if ($status !== null) {
            $stmt->bindValue(':status', $status, \PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }
    public function cancelOrder($orderId, $accountId)
    {
        // --- Lấy user_id từ account_id ---
        $stmtUser = $this->db->prepare("SELECT id AS user_id FROM users WHERE account_id = :account_id LIMIT 1");
        $stmtUser->bindValue(':account_id', $accountId, \PDO::PARAM_INT);
        $stmtUser->execute();
        $userRow = $stmtUser->fetch(\PDO::FETCH_ASSOC);
        if (!$userRow) return false;
        $userId = $userRow['user_id'];
        // --- Lấy trạng thái đơn ---
        $stmtOrder = $this->db->prepare("SELECT status FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmtOrder->bindValue(':id', $orderId, \PDO::PARAM_INT);
        $stmtOrder->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmtOrder->execute();
        $order = $stmtOrder->fetch(\PDO::FETCH_ASSOC);
        if (!$order) return false;
        $status = $order['status'];

        // --- Nếu trạng thái là paid, cộng lại số lượng sản phẩm  còn pending thì ko---
        if ($status === 'paid') {
            $stmtItems = $this->db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :order_id");
            $stmtItems->bindValue(':order_id', $orderId, \PDO::PARAM_INT);
            $stmtItems->execute();
            $items = $stmtItems->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $stmtUpdate = $this->db->prepare("UPDATE products SET quantity = quantity + :qty WHERE id = :product_id");
                $stmtUpdate->bindValue(':qty', $item['quantity'], \PDO::PARAM_INT);
                $stmtUpdate->bindValue(':product_id', $item['product_id'], \PDO::PARAM_INT);
                $stmtUpdate->execute();
            }
        }
        // --- Cập nhật trạng thái đơn sang cancelled ---
        $stmtCancel = $this->db->prepare("UPDATE {$this->table} 
                                      SET status = 'cancelled', updated_at = NOW() 
                                      WHERE id = :id AND user_id = :user_id");
        $stmtCancel->bindValue(':id', $orderId, \PDO::PARAM_INT);
        $stmtCancel->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmtCancel->execute();

        return true;
    }
}
