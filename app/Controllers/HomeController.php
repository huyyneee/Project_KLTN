<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class HomeController extends Controller {
	public function index() {
		$error = null;
		$categories = [];
		try {
			$categories = (new Category())->findAll();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		// render the home view with categories
		$this->render('home', ['categories' => $categories, 'error' => $error]);
	}
}

