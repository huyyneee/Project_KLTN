<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Core\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAll();
        $this->render('categories/index', ['categories' => $categories]);
    }

    /**
     * Show products for a category. Accepts GET param 'id' (category id) or 'cat' (category name).
     */
    public function show()
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();

        $idParam = $_GET['id'] ?? null;
        $catParam = $_GET['cat'] ?? null;

        $category = null;
        $products = [];

        // load all categories for the category nav
        $categories = $categoryModel->getAllCategories();

        if ($idParam !== null) {
            $id = (int)$idParam;
            $category = $categoryModel->getCategoryById($id);
            if ($category) {
                $products = $productModel->getProductsByCategory($id);
            }
        } elseif ($catParam !== null) {
            // find category by name
            foreach ($categories as $c) {
                if (isset($c['name']) && $c['name'] === $catParam) {
                    $category = $c;
                    $products = $productModel->getProductsByCategory((int)$c['id']);
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
