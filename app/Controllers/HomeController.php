<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class HomeController extends Controller
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
		$error = null;
		$categories = [];
		try {
			$categories = $this->categoryModel->findAll();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		// build products per category (limit to 12 each)
		$productsByCategory = [];
		foreach ($categories as $c) {
			try {
				$products = $this->productModel->getProductsByCategory((int)($c['id'] ?? 0));
				// load all products for the category (carousel will show 6 at a time)
				$productsByCategory[$c['id']] = $products;
			} catch (\Exception $e) {
				$productsByCategory[$c['id']] = [];
			}
		}

		// render the home view with categories and productsByCategory
		if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
			header('Content-Type: application/json');
			echo json_encode(['categories' => $categories, 'productsByCategory' => $productsByCategory, 'error' => $error]);
			return;
		}

		$this->render('home', ['categories' => $categories, 'productsByCategory' => $productsByCategory, 'error' => $error]);
	}
}

