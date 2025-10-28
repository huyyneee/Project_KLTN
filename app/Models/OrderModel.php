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

    public function getOrdersWithItems($userId, $status = null)
    {
        //Truy vấn danh sách đơn hàng (có thể lọc theo trạng thái)
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
            created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        if ($status !== null) {
            $stmt->bindValue(':status', $status, \PDO::PARAM_STR);
        }
        $stmt->execute();
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        //Chuẩn bị truy vấn chi tiết sản phẩm
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

        //Lấy host để xử lý link ảnh (nếu có)
        $dbHost = null;
        $cfgPath = __DIR__ . '/../../config/config.php';
        if (file_exists($cfgPath)) {
            $cfg = require $cfgPath;
            $dbHost = $cfg['database']['host'] ?? null;
        }

        //Gắn chi tiết sản phẩm vào từng đơn
        foreach ($orders as &$order) {
            $stmtItem->bindValue(':order_id', $order['id'], \PDO::PARAM_INT);
            $stmtItem->execute();
            $items = $stmtItem->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($items as &$item) {
                $img = trim($item['product_image'] ?? '');
                $img = str_replace('\\/', '/', $img);
                $img = trim($img, "'\" \t\n\r\0\x0B");

                if ($img !== '') {
                    if (preg_match('#^/#', $img) && $dbHost) {
                        $item['product_image'] = 'http://' . $dbHost . ':8000' . $img;
                    } elseif (preg_match('#^https?://#i', $img)) {
                        $item['product_image'] = $img;
                    }
                } else {
                    $item['product_image'] = null;
                }
            }

            $order['items'] = $items;
        }

        return $orders;
    }
    public function cancelOrder($orderId, $userId)
    {
        $sql = "UPDATE {$this->table} 
                SET status = 'cancelled', updated_at = NOW() 
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $orderId, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
