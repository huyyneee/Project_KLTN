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
    public function findByCategory(int $categoryId, ?int $limit = null, int $offset = 0): array
    {
        // include main image (if exists) from product_images where is_main = 1
        $sql = 'SELECT p.id, p.code, p.name, p.price, p.description, p.category_id, p.created_at, p.updated_at, pi.url AS image_url'
             . ' FROM ' . $this->table . ' p'
             . ' LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1'
             . ' WHERE p.category_id = :cid AND (p.deleted_at IS NULL)'
             . ' ORDER BY p.id ASC';

        if ($limit !== null) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cid', $categoryId, \PDO::PARAM_INT);
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // normalize image_url: if stored as relative path like /uploads/..., build absolute using DB_HOST:8000
        $dbHost = null;
        if (function_exists('env')) {
            $dbHost = env('DB_HOST');
        }
        if (!$dbHost) {
            $cfgPath = __DIR__ . '/../../config/config.php';
            if (file_exists($cfgPath)) {
                $cfg = require $cfgPath;
                $dbHost = $cfg['database']['host'] ?? null;
            }
        }

        foreach ($rows as &$r) {
            $img = trim($r['image_url'] ?? '');
            $img = str_replace('\\/', '/', $img);
            $img = trim($img, "'\" \t\n\r\0\x0B");
            if ($img !== '') {
                if (preg_match('#^/#', $img) && $dbHost) {
                    // build absolute URL with port 8000
                    $r['image_url'] = 'http://' . $dbHost . ':8000' . $img;
                } elseif (preg_match('#^https?://#i', $img)) {
                    $r['image_url'] = $img;
                } else {
                    // leave as-is (maybe already relative but no dbHost available)
                    $r['image_url'] = $img;
                }
            } else {
                $r['image_url'] = null;
            }
        }

        return $rows;
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
     * Return total number of products in a category
     */
    public function countByCategory(int $categoryId): int
    {
        $sql = 'SELECT COUNT(*) as c FROM ' . $this->table . ' WHERE category_id = :cid AND (deleted_at IS NULL)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cid' => $categoryId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? (int)$r['c'] : 0;
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
    public function getProductsByCategory(int $categoryId, ?int $limit = null, int $offset = 0): array
    {
        return $this->findByCategory($categoryId, $limit, $offset);
    }
}
