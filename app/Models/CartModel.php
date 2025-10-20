<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class CartModel extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    // Lấy giỏ hàng theo user
    public function getCartByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :userId LIMIT 1");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo giỏ hàng mới cho user
    public function createCart($userId)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, created_at) VALUES (:userId, NOW())");
        $stmt->execute(['userId' => $userId]);
        return $this->db->lastInsertId();
    }

    // Xóa giỏ hàng
    public function deleteCart($cartId)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :cartId");
        return $stmt->execute(['cartId' => $cartId]);
    }
}
