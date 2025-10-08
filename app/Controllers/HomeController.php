<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;

class HomeController extends Controller
{
	private $categoryModel;

	public function __construct()
	{
		$this->categoryModel = new CategoryModel();
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

		// render the home view with categories
		if (isset($_GET['xhr']) && $_GET['xhr'] == '1') {
			header('Content-Type: application/json');
			echo json_encode(['categories' => $categories, 'error' => $error]);
			return;
		}

		$this->render('home', ['categories' => $categories, 'error' => $error]);
	}
}

