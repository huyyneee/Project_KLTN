<?php
namespace App\Core;

require_once __DIR__ . '/Database.php';

use PDO;

class Model
{
    protected $db;
    protected $table;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll() {
        // Try to return only non-deleted rows if the table supports soft-delete (deleted_at).
        // If the column doesn't exist, fall back to returning all rows to avoid fatal errors.
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Likely the table doesn't have a deleted_at column; fallback to simple select
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        return $stmt->execute();
    }
    
    public function update($id, $data)
    {
        $columns = array_keys($data);
        $setClause = array_map(function($col) { return $col . ' = :' . $col; }, $columns);
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
}
