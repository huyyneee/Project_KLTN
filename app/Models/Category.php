<?php
require_once __DIR__ . '/../Core/Model.php';

class Category extends Model
{
    protected $table = 'categories';

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET name = :name, description = :description, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'] ?? null,
            ':description' => $data['description'] ?? null
        ]);
    }

    public function delete($id)
    {
        // Soft delete - set deleted_at timestamp
        $sql = "UPDATE {$this->table} SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function restore($id)
    {
        // Restore soft deleted record
        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function forceDelete($id)
    {
        // Hard delete - permanently remove from database
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function findById($id, $includeDeleted = false)
    {
        $whereClause = $includeDeleted ? "id = :id" : "id = :id AND deleted_at IS NULL";
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll($includeDeleted = false)
    {
        $whereClause = $includeDeleted ? "" : "WHERE deleted_at IS NULL";
        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findDeleted()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasProducts($categoryId)
    {
        // Check if category has any active products
        $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
