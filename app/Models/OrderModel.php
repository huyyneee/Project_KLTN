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
        'shipping_address',
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

    /**
     * Lấy tất cả đơn hàng của user, loại trừ status = 'paid'
     */
    public function getOrdersByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} 
            WHERE user_id = :user_id AND status != 'paid'
            ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy đơn hàng kèm chi tiết (items), loại trừ paid, có ảnh sản phẩm
     */
    public function getOrdersWithItems($userId)
    {
        $orders = $this->getOrdersByUserId($userId); // đã loại trừ paid

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

        $dbHost = null;
        // Lấy host từ config nếu có
        $cfgPath = __DIR__ . '/../../config/config.php';
        if (file_exists($cfgPath)) {
            $cfg = require $cfgPath;
            $dbHost = $cfg['database']['host'] ?? null;
        }

        foreach ($orders as &$order) {
            $stmtItem->bindValue(':order_id', $order['id'], \PDO::PARAM_INT);
            $stmtItem->execute();
            $items = $stmtItem->fetchAll(\PDO::FETCH_ASSOC);

            // Xử lý ảnh giống CartItemModel
            foreach ($items as &$item) {
                $img = trim($item['product_image'] ?? '');
                $img = str_replace('\\/', '/', $img);
                $img = trim($img, "'\" \t\n\r\0\x0B");
                if ($img !== '') {
                    if (preg_match('#^/#', $img) && $dbHost) {
                        $item['product_image'] = 'http://' . $dbHost . ':8000' . $img;
                    } elseif (preg_match('#^https?://#i', $img)) {
                        $item['product_image'] = $img;
                    } else {
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
}
