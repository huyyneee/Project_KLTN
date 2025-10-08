<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'account_id', 'full_name', 'phone', 'address', 'birthday', 'gender', 'created_at', 'updated_at'
    ];

    /**
     * Find a user row by account_id
     * @param int $accountId
     * @return array|null
     */
    public function findByAccountId($accountId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE account_id = :aid LIMIT 1");
        $stmt->execute([':aid' => $accountId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
