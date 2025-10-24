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
}
