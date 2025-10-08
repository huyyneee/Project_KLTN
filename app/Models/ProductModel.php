<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code', 'name', 'price', 'description', 'specifications', 'usage', 'ingredients', 'category_id', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Find products by category id.
     * Returns an array of associative arrays.
     */
    public function findByCategory(int $categoryId): array
    {
        $stmt = $this->db->prepare('SELECT id, code, name, price, description, category_id, created_at, updated_at FROM ' . $this->table . ' WHERE category_id = :cid AND (deleted_at IS NULL) ORDER BY id ASC');
        $stmt->execute([':cid' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Return all products.
     * @return array
     */
    public function getAllProducts(): array
    {
        return $this->findAll();
    }

    /**
     * Return a single product by id or null if not found.
     * @param int $id
     * @return array|null
     */
    public function getProductById(int $id): ?array
    {
        $result = $this->find($id);
        return $result === false ? null : $result;
    }

    /**
     * Alias for findByCategory
     */
    public function getProductsByCategory(int $categoryId): array
    {
        return $this->findByCategory($categoryId);
    }
}
