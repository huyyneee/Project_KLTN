<?php

namespace App\Models;

require_once __DIR__ . '/../Core/Model.php';

use App\Core\Model;
use PDO;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $fillable = ['product_id', 'url', 'is_main'];

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (product_id, url, is_main) 
                VALUES (:product_id, :url, :is_main)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':product_id' => $data['product_id'],
            ':url' => $data['url'],
            ':is_main' => $data['is_main'] ?? 0
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function findByProductId($productId)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE product_id = :product_id ORDER BY is_main DESC, id ASC LIMIT 50";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in findByProductId for product_id $productId: " . $e->getMessage());
            return [];
        }
    }

    public function deleteByProductId($productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':product_id' => $productId]);
    }

    public function getMainImage($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = :product_id AND is_main = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
