<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Core\Controller;

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $products = $this->productModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['products' => $products]);
            return;
        }

        $this->render('products/index', ['products' => $products]);
    }
}
