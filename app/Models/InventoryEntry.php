<?php
namespace App\Models;

require_once __DIR__ . '/../Core/Model.php';

use App\Core\Model;
use PDO;

class InventoryEntry extends Model
{
    protected $table = 'inventory_entries';

    public function createEntry(array $data)
    {
        $sql = "INSERT INTO {$this->table} (product_id, category_id, quantity, entry_date, created_by, note) 
                VALUES (:product_id, :category_id, :quantity, :entry_date, :created_by, :note)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':product_id' => (int)$data['product_id'],
            ':category_id' => (int)$data['category_id'],
            ':quantity' => (int)$data['quantity'],
            ':entry_date' => $data['entry_date'],
            ':created_by' => (int)$data['created_by'],
            ':note' => $data['note'] ?? null,
        ]);
        if ($ok) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function listEntries(int $limit = 20, int $offset = 0)
    {
        $sql = "SELECT ie.*, p.name as product_name, p.code as product_code, c.name as category_name, a.email as created_by_email
                FROM {$this->table} ie
                JOIN products p ON p.id = ie.product_id
                JOIN categories c ON c.id = ie.category_id
                JOIN accounts a ON a.id = ie.created_by
                ORDER BY ie.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>


