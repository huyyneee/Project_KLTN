<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Core\Database;

class TestController extends Controller
{
    public function index()
    {
        $error = null;
        $categories = [];
        $category = null;
        $categoryProducts = [];
        try {
            // Prefer fetching single category when requested via GET param 'lll' or 'id'
            $idParam = $_GET['lll'] ?? ($_GET['id'] ?? null);
            if ($idParam !== null) {
                $id = (int)$idParam;
                $category = (new Category())->find($id);
                if ($category) {
                    // fetch products for this category
                    $db = (new Database())->getConnection();
                    $stmt = $db->prepare('SELECT id, code, name, price, description, category_id, created_at, updated_at FROM products WHERE category_id = :cid AND (deleted_at IS NULL) ORDER BY id ASC');
                    $stmt->execute([':cid' => $id]);
                    $categoryProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }
            } else {
                // no id requested — return all categories
                $categories = (new Category())->findAll();
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $this->render('test', [
            'categories' => $categories,
            'error' => $error,
            'category' => $category,
            'categoryProducts' => $categoryProducts,
        ]);
    }
}
