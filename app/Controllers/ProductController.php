<?php
namespace App\Controllers;

use App\Models\Product;
use App\Core\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = (new Product())->findAll();
        $this->render('products/index', ['products' => $products]);
    }
}
