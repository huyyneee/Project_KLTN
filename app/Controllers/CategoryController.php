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

        // load all categories for the category nav
        $categories = $this->categoryModel->getAllCategories();

        if ($idParam !== null) {
            $id = (int) $idParam;
            $category = $this->categoryModel->getCategoryById($id);
            if ($category) {
                $products = $this->productModel->getProductsByCategory($id);
            }
        } elseif ($catParam !== null) {
            // find category by name
            foreach ($categories as $c) {
                if (isset($c['name']) && $c['name'] === $catParam) {
                    $category = $c;
                    $products = $this->productModel->getProductsByCategory((int) $c['id']);
                    break;
                }
            }
        }

        // If requested as XHR, return JSON only
        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode([
                'category' => $category,
                'products' => $products,
                'categories' => $categories,
            ]);
            return;
        }

        $this->render('categories/show', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
