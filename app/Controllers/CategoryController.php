<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Core\Controller;

class CategoryController extends Controller
{
    private $categoryModel;
    private $productModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }
    public function index()
    {
        $categories = $this->categoryModel->findAll();
        $this->render('categories/index', ['categories' => $categories]);
    }

    /**
     * Show products for a category. Accepts GET param 'id' (category id) or 'cat' (category name).
     */
    public function show()
    {
        $categories = $this->categoryModel->findAll();

        $idParam = $_GET['id'] ?? null;
        $catParam = $_GET['cat'] ?? null;

    $category = null;
    $products = [];
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 16; // 4 cols * 4 rows

        // load all categories for the category nav
        $categories = $this->categoryModel->getAllCategories();

        if ($idParam !== null) {
            $id = (int) $idParam;
            $category = $this->categoryModel->getCategoryById($id);
            if ($category) {
                $total = $this->productModel->countByCategory($id);
                $offset = ($page - 1) * $perPage;
                $products = $this->productModel->getProductsByCategory($id, $perPage, $offset);
            }
        } elseif ($catParam !== null) {
            // find category by name
            foreach ($categories as $c) {
                if (isset($c['name']) && $c['name'] === $catParam) {
                    $category = $c;
                    $total = $this->productModel->countByCategory((int)$c['id']);
                    $offset = ($page - 1) * $perPage;
                    $products = $this->productModel->getProductsByCategory((int) $c['id'], $perPage, $offset);
                    break;
                }
            }
        }

        // If requested as XHR, return JSON only
        // compute pagination values
        $total = $total ?? 0;
        $lastPage = (int) max(1, ceil($total / $perPage));

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode([
                'category' => $category,
                'products' => $products,
                'categories' => $categories,
                'pagination' => [
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total,
                    'lastPage' => $lastPage,
                ],
            ]);
            return;
        }

        $this->render('categories/show_category', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'lastPage' => $lastPage,
            ],
        ]);
    }
}
