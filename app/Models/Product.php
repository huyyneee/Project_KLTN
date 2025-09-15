<?php
require_once __DIR__ . '/../Core/Model.php';

class Product extends Model
{
    protected $table = 'products';

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (code, name, price, description, specifications, usage, ingredients, category_id, main_image, detail_images) 
                VALUES (:code, :name, :price, :description, :specifications, :usage, :ingredients, :category_id, :main_image, :detail_images)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':code' => $data['code'] ?? null,
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':description' => $data['description'],
            ':specifications' => $data['specifications'] ?? null,
            ':usage' => $data['usage'] ?? null,
            ':ingredients' => $data['ingredients'] ?? null,
            ':category_id' => $data['category_id'],
            ':main_image' => $data['main_image'] ?? null,
            ':detail_images' => $data['detail_images'] ?? null
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                price = :price, 
                description = :description, 
                specifications = :specifications, 
                usage = :usage, 
                ingredients = :ingredients, 
                category_id = :category_id,
                main_image = :main_image,
                detail_images = :detail_images,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'] ?? null,
            ':price' => $data['price'] ?? null,
            ':description' => $data['description'] ?? null,
            ':specifications' => $data['specifications'] ?? null,
            ':usage' => $data['usage'] ?? null,
            ':ingredients' => $data['ingredients'] ?? null,
            ':category_id' => $data['category_id'] ?? null,
            ':main_image' => $data['main_image'] ?? null,
            ':detail_images' => $data['detail_images'] ?? null
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

    public function findAllWithCategory($includeDeleted = false)
    {
        $whereClause = $includeDeleted ? "" : "WHERE p.deleted_at IS NULL";
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                {$whereClause}
                ORDER BY p.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCategory($categoryId, $includeDeleted = false)
    {
        $whereClause = $includeDeleted ? "category_id = :category_id" : "category_id = :category_id AND deleted_at IS NULL";
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id, $includeDeleted = false)
    {
        $whereClause = $includeDeleted ? "id = :id" : "id = :id AND deleted_at IS NULL";
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findDeleted()
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NOT NULL 
                ORDER BY p.deleted_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
