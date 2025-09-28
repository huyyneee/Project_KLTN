<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Core\Database;

class TestController extends Controller
{
    public function index()
    {
    $error = null;
    $categories = [];
    $category = null;
    $categoryProducts = [];
    // instantiate model once and reuse
    $categoryModel = new CategoryModel();
        try {
            // Prefer fetching single category when requested via GET param 'lll' or 'id'
            $idParam = $_GET['lll'] ?? ($_GET['id'] ?? null);
            if ($idParam !== null) {
                $id = (int)$idParam;
                $category = $categoryModel->find($id);
                if ($category) {
                    // fetch products for this category using ProductModel
                    $productModel = new ProductModel();
                    $categoryProducts = $productModel->findByCategory($id);
                }
            } else {
                // no id requested — return all categories
                $categories = $categoryModel->findAll();
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
