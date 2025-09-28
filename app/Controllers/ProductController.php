<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Core\Controller;

class ProductController extends Controller
{
    public function index()
    {
    $productModel = new ProductModel();
    $products = $productModel->findAll();
        $this->render('products/index', ['products' => $products]);
    }
}
