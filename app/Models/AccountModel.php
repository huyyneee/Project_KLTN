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
        'status'
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
}
