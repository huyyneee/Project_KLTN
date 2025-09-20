<?php
namespace App\Controllers;

use App\Models\ProductImage;
use App\Core\Controller;

class ProductImageController extends Controller
{
    public function index()
    {
        $images = (new ProductImage())->findAll();
        $this->render('product_images/index', ['images' => $images]);
    }
}
