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

    /**
     * Update user information
     * @param int $accountId
     * @param array $data
     * @return bool
     */
    public function updateByAccountId($accountId, $data)
    {
        $user = $this->findByAccountId($accountId);
        
        if (!$user) {
            // Create new user if not exists
            $data['account_id'] = $accountId;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $columns = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            
            $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($values)");
            return $stmt->execute($data);
        }

        // Update existing user
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = :$key";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE account_id = :account_id";
        $data['account_id'] = $accountId;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}
