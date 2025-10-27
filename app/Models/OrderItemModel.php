<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'created_at',
        'updated_at'
    ];
    public function insert($data)
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
    public function getItemsByOrder($orderId)
    {
        $sql = "SELECT 
                    i.id,
                    i.order_id,
                    i.product_id,
                    i.quantity,
                    i.price,
                    p.name AS product_name,
                    p.code AS product_code,
                    p.description,
                    p.category_id,
                    pi.url AS image_url,
                    (SELECT url FROM product_images pi2 WHERE pi2.product_id = p.id AND pi2.is_main = 0 LIMIT 1) AS alt_image_url
                FROM {$this->table} AS i
                INNER JOIN products AS p ON i.product_id = p.id
                LEFT JOIN product_images AS pi ON pi.product_id = p.id AND pi.is_main = 1
                WHERE i.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔧 Chuẩn hóa đường dẫn ảnh (giống ProductModel)
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

        foreach ($rows as &$r) {
            $img = trim($r['alt_image_url'] ?? $r['image_url'] ?? '');
            $img = str_replace('\\/', '/', $img);
            $img = trim($img, "'\" \t\n\r\0\x0B");
            if ($img !== '') {
                if (preg_match('#^/#', $img) && $dbHost) {
                    $r['image_url'] = 'http://' . $dbHost . ':8000' . $img;
                } elseif (preg_match('#^https?://#i', $img)) {
                    $r['image_url'] = $img;
                } else {
                    $r['image_url'] = $img;
                }
            } else {
                $r['image_url'] = null;
            }
        }

        return $rows;
    }
}
