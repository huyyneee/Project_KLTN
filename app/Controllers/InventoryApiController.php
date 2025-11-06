<?php
namespace App\Controllers;

require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/../Models/InventoryEntry.php';
require_once __DIR__ . '/../Models/Product.php';

use App\Models\InventoryEntry;
use App\Models\Product;
use Exception;

class InventoryApiController extends ApiController
{
    private $inventoryModel;
    private $productModel;

    public function __construct()
    {
        $this->inventoryModel = new InventoryEntry();
        $this->productModel = new Product();
    }

    // POST /api/inventory/stock-in
    public function stockIn()
    {
        try {
            // Require authentication - will throw 401 if not authenticated
            $accountId = $this->requireAuth();

            $data = $this->getInput();
            $required = ['category_id', 'product_id', 'quantity'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
            }

            $categoryId = (int) $data['category_id'];
            $productId = (int) $data['product_id'];
            $quantity = (int) $data['quantity'];

            if ($quantity <= 0) {
                $this->sendError('Quantity must be greater than 0', 400);
            }

            $product = $this->productModel->findById($productId);
            if (!$product) {
                $this->sendError('Product not found', 404);
            }
            if ((int) $product['category_id'] !== $categoryId) {
                $this->sendError('Product does not belong to selected category', 400);
            }

            // Increment product quantity atomically
            $newQty = ((int) ($product['quantity'] ?? 0)) + $quantity;
            $result = $this->productModel->update($productId, [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $newQty,
                'description' => $product['description'],
                'specifications' => $product['specifications'],
                'usage' => $product['usage'],
                'ingredients' => $product['ingredients'],
                'category_id' => $product['category_id']
            ]);

            if (!$result) {
                $this->sendError('Failed to update product quantity', 500);
            }

            $this->sendResponse(['new_quantity' => $newQty], 'Stock-in updated successfully', 200);
        } catch (Exception $e) {
            $this->sendError('Failed to stock-in: ' . $e->getMessage(), 500);
        }
    }

    // GET /api/inventory/stock-in
    public function list()
    {
        try {
            $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : 20;
            $offset = ($page - 1) * $limit;

            $items = $this->inventoryModel->listEntries($limit, $offset);
            $this->sendResponse([
                'data' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'has_next' => count($items) === $limit
                ]
            ], 'Inventory entries retrieved successfully');
        } catch (Exception $e) {
            $this->sendError('Failed to list inventory entries: ' . $e->getMessage(), 500);
        }
    }
}
?>