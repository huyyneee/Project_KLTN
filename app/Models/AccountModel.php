<?php

namespace App\Models;

use App\Core\Model;
use PDOException;


class AccountModel extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'email',
        'password',
        'full_name',
        'created_at',
        'updated_at',
        'last_login',
        'role',
        'status',
        'password_reset_token',
        'password_reset_expires'
    ];
    public function insert(array $data)
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (email, password, full_name, created_at, updated_at, role, status) 
                    VALUES (:email, :password, :full_name, :created_at, :updated_at, :role, :status)";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                'email'      => $data['email'],
                'password'   => $data['password'],
                'full_name'  => $data['full_name'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                'role'       => $data['role'] ?? 'user',
                'status'     => $data['status'] ?? 1
            ]);

            return (int)$this->db->lastInsertId(); // ID vừa tạo
        } catch (PDOException $e) {
            error_log("Account insert error: " . $e->getMessage());
            return false;
        }
    }
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public function setPasswordResetToken($accountId, $token, $expires)
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id"
        );
        return $stmt->execute([':token' => $token, ':expires' => $expires, ':id' => $accountId]);
    }
    public function findByResetToken($token)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE password_reset_token = :token LIMIT 1"
        );
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public function updatePassword($accountId, $hashedPassword)
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET password = :pwd, password_reset_token = NULL, password_reset_expires = NULL WHERE id = :id"
        );
        return $stmt->execute([':pwd' => $hashedPassword, ':id' => $accountId]);
    }
    public function updateLastLogin($accountId)
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id"
        );
        return $stmt->execute([':id' => $accountId]);
    }
}
