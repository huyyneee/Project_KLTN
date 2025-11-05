<?php
namespace App\Models;

require_once __DIR__ . '/../Core/Model.php';

use App\Core\Model;
use PDO;

class Product extends Model
{
    protected $table = 'products';

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (code, name, price, quantity, description, specifications, `usage`, ingredients, category_id) 
                VALUES (:code, :name, :price, :quantity, :description, :specifications, :usage, :ingredients, :category_id)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':code' => $data['code'] ?? null,
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':quantity' => isset($data['quantity']) ? (int)$data['quantity'] : 0,
            ':description' => $data['description'],
            ':specifications' => $data['specifications'] ?? null,
            ':usage' => $data['usage'] ?? null,
            ':ingredients' => $data['ingredients'] ?? null,
            ':category_id' => $data['category_id']
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                price = :price, 
                quantity = :quantity,
                description = :description, 
                specifications = :specifications, 
                `usage` = :usage, 
                ingredients = :ingredients, 
                category_id = :category_id,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'] ?? null,
            ':price' => $data['price'] ?? null,
            ':quantity' => isset($data['quantity']) ? (int)$data['quantity'] : 0,
            ':description' => $data['description'] ?? null,
            ':specifications' => $data['specifications'] ?? null,
            ':usage' => $data['usage'] ?? null,
            ':ingredients' => $data['ingredients'] ?? null,
            ':category_id' => $data['category_id'] ?? null
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

    public function findAllWithCategoryPaginated($limit, $offset, $search = '', $category_id = null, $sort_by = 'created_at', $sort_order = 'DESC')
    {
        // Build WHERE clause
        $whereConditions = ["p.deleted_at IS NULL"];
        $params = [];

        // Add search condition
        if (!empty($search)) {
            $whereConditions[] = "(p.name LIKE :search1 OR p.description LIKE :search2 OR p.code LIKE :search3)";
            $params[':search1'] = "%{$search}%";
            $params[':search2'] = "%{$search}%";
            $params[':search3'] = "%{$search}%";
        }

        // Add category filter
        if ($category_id !== null) {
            $whereConditions[] = "p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        $whereClause = "WHERE " . implode(" AND ", $whereConditions);

        // Validate sort parameters
        $allowedSortFields = ['name', 'price', 'created_at', 'updated_at'];
        $sort_by = in_array($sort_by, $allowedSortFields) ? $sort_by : 'created_at';
        $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

        // Get total count
        $countSql = "SELECT COUNT(*) as total 
                    FROM {$this->table} p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated data
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                {$whereClause}
                ORDER BY p.{$sort_by} {$sort_order}
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // Bind parameters for main query (including limit and offset)
        $mainParams = $params;
        $mainParams[':limit'] = $limit;
        $mainParams[':offset'] = $offset;

        $stmt->execute($mainParams);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate pagination info
        $totalPages = ceil($totalItems / $limit);
        $currentPage = floor($offset / $limit) + 1;

        return [
            'data' => $data,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page' => $limit,
            'has_next' => $currentPage < $totalPages,
            'has_prev' => $currentPage > 1
        ];
    }

    public function findWithCategory($id)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
