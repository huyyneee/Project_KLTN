<?php

namespace App\Models;

use App\Core\Model;

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
        $sql = "SELECT i.*, p.name AS product_name
                FROM {$this->table} i
                JOIN products p ON i.product_id = p.id
                WHERE i.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
