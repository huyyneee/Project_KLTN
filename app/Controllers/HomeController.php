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
		$productsByCategory = [];
		$query = isset($_GET['search']) ? trim($_GET['search']) : '';

		try {
			$categories = $this->categoryModel->findAll();

			foreach ($categories as $c) {
				$catId = (int)($c['id'] ?? 0);

				if ($query !== '') {
					// Tìm theo tên sản phẩm trong từng danh mục
					$products = $this->productModel->searchByNameAndCategory($query, $catId);
				} else {
					// Hiển thị toàn bộ nếu không có từ khóa
					$products = $this->productModel->getProductsByCategory($catId);
				}

				if (!empty($products)) {
					$productsByCategory[$catId] = $products;
				}
			}
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		$this->render('home', [
			'categories' => $categories,
			'productsByCategory' => $productsByCategory,
			'query' => $query,
			'error' => $error
		]);
	}
}
