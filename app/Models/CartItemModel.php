<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class CartItemModel extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'created_at',
        'updated_at'
    ];

    /**
     * Lấy danh sách sản phẩm trong giỏ và xử lý đường dẫn ảnh
     * ci: cart_items
     * pi: product_images
     */
    public function getItemsByCart($cartId)
    {
        $sql = "SELECT ci.*, p.name as productname, p.price as product_price, pi.url as image
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            WHERE ci.cart_id = :cart_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cart_id' => $cartId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Xử lý đường dẫn ảnh để tạo URL đầy đủ
        $dbHost = null;
        if (function_exists('env')) {
            $dbHost = env('DB_HOST');
        }
        if (!$dbHost) {
            $cfgPath = __DIR__ . '/../../config/config.php';
            if (file_exists($cfgPath)) {
                $cfg = require $cfgPath;
                $dbHost = $cfg['database']['host'] ?? null;
            }
        }

        foreach ($rows as &$row) {
            $img = trim($row['image'] ?? '');
            $img = str_replace('\\/', '/', $img);
            $img = trim($img, "'\" \t\n\r\0\x0B");
            if ($img !== '') {
                if (preg_match('#^/#', $img) && $dbHost) {
                    $row['image'] = 'http://' . $dbHost . ':8000' . $img;
                } elseif (preg_match('#^https?://#i', $img)) {
                    $row['image'] = $img;
                } else {
                    $row['image'] = $img;
                }
            } else {
                $row['image'] = null;
            }
        }

        return $rows;
    }

    // Kiểm tra xem sản phẩm đã có trong giỏ chưa
    public function getItemByCartAndProduct($cartId, $productId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE cart_id = :cartId AND product_id = :productId LIMIT 1
        ");
        $stmt->execute([
            'cartId' => $cartId,
            'productId' => $productId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm sản phẩm mới vào giỏ (debug-ready)
    public function addItem($cartId, $productId, $quantity, $price)
    {
        $stmt = $this->db->prepare("
        INSERT INTO {$this->table} (cart_id, product_id, quantity, price) 
        VALUES (:cartId, :productId, :quantity, :price)
    ");
        return $stmt->execute([
            'cartId' => $cartId,
            'productId' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ]);
    }

    // Cập nhật số lượng sản phẩm
    public function updateQuantity($itemId, $quantity)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET quantity = :quantity WHERE id = :itemId
        ");
        return $stmt->execute([
            'quantity' => $quantity,
            'itemId' => $itemId
        ]);
    }

    // Xóa sản phẩm khỏi giỏ
    public function deleteItem($itemId)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :itemId");
        return $stmt->execute(['itemId' => $itemId]);
    }

    // Xóa toàn bộ sản phẩm trong giỏ
    public function deleteAllByCart($cartId)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE cart_id = :cartId");
        return $stmt->execute(['cartId' => $cartId]);
    }
    public function getTotalQuantity($cartId)
    {
        $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM {$this->table} WHERE cart_id = :cart_id");
        $stmt->execute(['cart_id' => $cartId]);
        $total = $stmt->fetchColumn();
        return $total !== false ? (int)$total : 0;
    }
}
