<?php
namespace App\Controllers;

use App\Models\ProductImageModel;
use App\Core\Controller;

class ProductImageController extends Controller
{
    private $productImageModel;

    public function __construct()
    {
        $this->productImageModel = new ProductImageModel();
    }

    public function index()
    {
        $images = $this->productImageModel->findAll();

        if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['images' => $images]);
            return;
        }

        $this->render('product_images/index', ['images' => $images]);
    }
}
