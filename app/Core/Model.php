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
    { /* build INSERT ... */
    }
    public function update($id, $data)
    { /* build UPDATE ... */
    }
    public function delete($id)
    { /* build DELETE ... */
    }
}
