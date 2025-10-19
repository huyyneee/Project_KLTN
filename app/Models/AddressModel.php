<?php
namespace App\Models;

use App\Core\Model;

class AddressModel extends Model
{
    protected $table = 'addresses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'phone', 'receiver_name', 'street', 'ward', 'district', 'city', 'province', 'type', 'is_default', 'created_at', 'updated_at'
    ];

    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :uid ORDER BY is_default DESC, id DESC");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByIdAndUser($id, $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND user_id = :uid LIMIT 1");
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findOneByIdAndUser($id, $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND user_id = :uid LIMIT 1");
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function clearDefaultForUser($userId)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_default = 0 WHERE user_id = :uid");
        return $stmt->execute([':uid' => $userId]);
    }

    public function setDefault($id, $userId)
    {
        $this->clearDefaultForUser($userId);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_default = 1 WHERE id = :id AND user_id = :uid");
        return $stmt->execute([':id' => $id, ':uid' => $userId]);
    }
}
