<?php
namespace App\Controllers;

use App\Models\ProductImageModel;
use App\Core\Controller;

class ProductImageController extends Controller
{
    public function index()
    {
    $productImageModel = new ProductImageModel();
    $images = $productImageModel->findAll();
        $this->render('product_images/index', ['images' => $images]);
    }
}
