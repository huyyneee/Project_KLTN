<?php
namespace App\Core;

use App\Core\Database;
use PDO;

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct() {
        $this->db = (new Database())->getConnection();
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

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey}=:id LIMIT 1");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $fields = array_intersect(array_keys($data), $this->fillable);
        $columns = implode(',', $fields);
        $placeholders = implode(',', array_map(fn($f) => ':' . $f, $fields));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $params = [];
        foreach ($fields as $field) {
            $params[':' . $field] = $data[$field];
        }
        $stmt->execute($params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = array_intersect(array_keys($data), $this->fillable);
        $set = implode(',', array_map(fn($f) => "$f=:$f", $fields));
        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey}=:id";
        $stmt = $this->db->prepare($sql);
        $params = [':id' => $id];
        foreach ($fields as $field) {
            $params[':' . $field] = $data[$field];
        }
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey}=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}